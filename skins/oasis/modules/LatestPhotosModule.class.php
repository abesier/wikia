<?php
class LatestPhotosModule extends Module {

	const BLACKLIST_MESSAGE = 'Photosblacklist';

	var $thumbUrls;
	var $wgBlankImgUrl;
	var $enableScroll;
	var $enableEmptyGallery;
	var $total;

	var $wgEnableUploads;
	var $wgStylePath;
	var $wgUser;

	public function executeIndex() {
		global $wgUser, $wgTitle, $wgOut, $wgStylePath, $wgMemc;

		// get the count of images on this wiki
		$this->total = SiteStats::images();

		// Pull the list of images from memcache first
		// FIXME: create and use service (see RT #79288)

		$this->thumbUrls = $wgMemc->get( LatestPhotosModule::memcacheKey() );
		if (empty($this->thumbUrls)) {
			// api service
			$params = array(
				'action' => 'query',
				'list' => 'logevents',
				'letype' => 'upload',
				'leprop' => 'title',
				'lelimit' => 50,
			);

			$apiData = ApiService::call($params);

			if (empty($apiData)) {
				return false;
			}
			$imageList = $apiData['query']['logevents'];

			$fileList = array_map(array($this, "getImageData"), $imageList);
			$fileList = array_filter($fileList, array($this, "filterImages"));

			// make sure the list of images is unique and limited to 11 images (12 including the see all image)
			$shaList = array();
			$uniqueList = array();

			foreach ($fileList as $data) {
				$sha = $data['file']->sha1;
				if (! array_key_exists($sha, $shaList)) {
					$shaList[$sha] = true;
					$uniqueList[] = $data;
				}
				if (count($uniqueList) > 10) break;
			}

			$this->thumbUrls = array_map(array($this, 'getTemplateData'), $uniqueList);
			$wgMemc->set( LatestPhotosModule::memcacheKey(), $this->thumbUrls);
		}

		if (count($this->thumbUrls) < 3) {
			$this->enableScroll = false;
		} else {
			$this->enableScroll = true;
		}

		if (count($this->thumbUrls)  <= 0) {
			$this->enableEmptyGallery = true;
		}
	}

	private function getTemplateData($element) {

		if (! isset($element['file'])) return array();

		$file = $element['file'];
		// crop the images correctly using extension:imageservice
		$is = new imageServing(array(), 82);
		$thumb_url = $is->getThumbnails(array($file));
		$thumb_url = array_pop($thumb_url);
		$thumb_url = $thumb_url['url'];
		$userName = $file->user_text;

		$retval = array (
			"file_url" => $element['url'],
			"image_url" => $file->getUrl(),
			"thumb_url" => $thumb_url,
			"image_filename" => $file->getTitle()->getFullText(),
			"user_href" => Wikia::link(Title::newFromText($userName, NS_USER), $userName),
			"links" => $this->getLinkedFiles($file->name),
			"date" => wfTimeFormatAgo($file->timestamp));
		return $retval;
	}

 	private function getImageData($element) {
		$retval = array();
		if (isset($element['title'])) {
			$title = Title::newFromText($element['title']);
			$retval = array('url' => $title->getLocalUrl(), 'file' => wfFindFile ( $title ));
		}
		return $retval;
	}

	private function filterImages($element) {
		$file = $element['file'];
		$ret = true;

		if (isset($file->title)) {
			// filter by filetype and filesize (RT #42075)
			$minor_type = $file->minor_mime;
			$renderable = $file->canRender();
			$width = $file->width;
			$height = $file->height;
			$name = $file->title->getPrefixedText();
			// Don't try to display WikiCommons Remote files (RT# 75588)
			if (get_class($file) == "ForeignAPIFile") {
				$ret = false;
			}
			if ($renderable == false) { #covers all docs, audio, and binaries
				$ret = false;
			}
			if ($minor_type == 'x-bmp') { # exception, because imagemagick is dumb
				$ret = false;
			}
			if ($width < 100) {
				$ret = false;
			}
			if ($height < 100) {
				$ret = false;
			}

			if( $ret ) { #only do this semi-expensive check if we're still in the running
				// RT #70016: check blacklist
				if ($this->isImageBlacklisted($name)) {
					wfDebug(__METHOD__ . ": {$name} blacklisted\n");
					$ret = false;
				}
			}
		} else {
			$ret = false;
		}

		return $ret;
	}

	private function isImageBlacklisted($filename) {
		$blacklist = $this->getBlacklist();
		return !empty($blacklist[$filename]);
	}

	private function getBlacklist() {
		wfProfileIn(__METHOD__);
		static $blacklist = null;

		if (is_null($blacklist)) {
			$lines = getMessageAsArray(self::BLACKLIST_MESSAGE);
			$blacklist = array();

			if (!empty($lines)) {
				foreach($lines as $line) {
					$image = Title::newFromText(trim($line, "* "), NS_FILE);
					if (!empty($image)) {
						$blacklist[ $image->getPrefixedText() ] = 1;
					}
				}

				wfDebug(__METHOD__ . ": blacklist loaded\n");
			}
			else {
				wfDebug(__METHOD__ . ": blacklist is empty\n");
			}
		}

		wfProfileOut(__METHOD__);
		return $blacklist;
	}

	private function getLinkedFiles ( $name ) {
		global $wgUser, $wgMemc;

		wfProfileIn( __METHOD__ );
		$cacheKey = wfMemcKey( __METHOD__, $name );
		$data = $wgMemc->get( $cacheKey );
		if( !is_array($data) ) {
			// The ORDER BY ensures we get NS_MAIN pages first
			$dbr = wfGetDB( DB_SLAVE );
			$res = $dbr->select(
						array( 'imagelinks', 'page' ),
						array( 'page_namespace', 'page_title' ),
						array( 'il_to' => $name, 'il_from = page_id' ),
						__METHOD__,
						array( 'LIMIT' => 2, 'ORDER BY' => 'page_namespace ASC' )
				   );

			$data = array() ;
			// link where this page is used...
			if ( $s = $res->fetchObject() ) {
				$data[] = array( 'ns' => $s->page_namespace, 'title' => $s->page_title );
			}
			// if used in more than one place, add "more" link
			if ( $s = $res->fetchObject() ) {
				$data[] = array( 'ns' => NS_FILE, 'title' => $name );
			}

			$wgMemc->set( $cacheKey, $data, 60*15 );
		}

		$links = array();
		if ( !empty( $data ) ) {
			$sk = $wgUser->getSkin();

			foreach ( $data as $inx => $row ) {
				$Title = Title::makeTitle( $row['ns'], $row['title'] );

				if ( $row['title'] == $name && $row['ns'] == NS_FILE ) {
					$links[] = '<a href="' . $Title->getLocalUrl() .
							'#filelinks" class="wikia-gallery-item-more">' .
							wfMsg( 'oasis-latest-photos-more-dotdotdot' ) . '</a>';
				} else {
					$links[] = $sk->link( $Title, null, array( 'class' => 'wikia-gallery-item-posted' ) );
				}
			}
		}

		wfProfileOut(__METHOD__);
		return $links;
	}

	private static function memcacheKey() {
		global $wgDevelEnvironment;
		$mKey = wfMemcKey('mOasisLatestPhotos');
		if(!empty($wgDevelEnvironment)){
			$mKey = wfMemcKey('mOasisLatestPhotos', $_SERVER['SERVER_NAME']);
		}
		return $mKey;
	}

	private static function avoidUpdateRace(){
		global $wgMemc;
		// avoid a race in update event propgation by deleting key after 10 seconds
		// Memcache delete with a timeout is not implemented, but we can use set to fake it
		$thumbUrls = $wgMemc->get(LatestPhotosModule::memcacheKey());

		if (!empty($thumbUrls))
			$wgMemc->set(LatestPhotosModule::memcacheKey(), $thumbUrls, 10);
	}

	public static function onImageUpload( $image ){
		self::avoidUpdateRace();
		return true;
	}

	public static function onImageUploadComplete( &$image ){
		self::avoidUpdateRace();
		return true;
	}

	public static function onImageRenameCompleated( &$this , &$ot , &$nt ){
		global $wgMemc;
		if ( $nt->getNamespace() == NS_FILE ) {
			wfDebug(__METHOD__ . ": photo renamed\n");
			$wgMemc->delete( LatestPhotosModule::memcacheKey() );
		}
		return true;
	}

	public static function onImageDelete(&$file, $oldimage, $article, $user, $reason) {
		global $wgMemc;
		$wgMemc->delete(LatestPhotosModule::memcacheKey());
		return true;
	}


	public static function onMessageCacheReplace($title, $text) {
		global $wgMemc;
		if ($title == self::BLACKLIST_MESSAGE) {
			wfDebug(__METHOD__ . ": photos blacklist has been updated\n");
			$wgMemc->delete(LatestPhotosModule::memcacheKey());
		}

		return true;
	}

}
