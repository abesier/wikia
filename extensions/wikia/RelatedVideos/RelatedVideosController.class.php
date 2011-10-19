<?php

class RelatedVideosController extends WikiaController {

	const SURVEY_URL = 'http://www.surveymonkey.com/s/RelatedVideosExperience';
	public function __construct( WikiaApp $app ) {
		$this->app = $app;
	}

	public function getCarusel(){
		if( Wikia::isMainPage() || ( !$this->app->wg->title instanceof Title ) || !$this->app->wg->title->exists() ) {
			return false;
		}
		$relatedVideos = RelatedVideos::getInstance();
		$videos = $relatedVideos->get(
			$this->app->wg->title->getArticleId(),
			RelatedVideos::MAX_RELATEDVIDEOS
		);
		
		if ( !is_array( $videos ) ){ $videos = array(); }

		$oLocalLists = RelatedVideosNamespaceData::newFromTargetTitle( F::app()->wg->title );
		$oGlobalLists = RelatedVideosNamespaceData::newFromGeneralMessage();		

		$oRelatedVideosService = F::build('RelatedVideosService');
		$blacklist = array();
		foreach( array( $oLocalLists, $oGlobalLists ) as $oLists ){
			if ( !empty( $oLists ) && $oLists->exists() ){
				$data = $oLists->getData();
				if ( isset(  $data['lists'] ) && isset( $data['lists']['WHITELIST'] ) ) {
					foreach( $data['lists']['WHITELIST'] as $page ){
						$videoData = $oRelatedVideosService->getRelatedVideoData( 0, $page['title'], $page['source'] );
						if ( isset( $videoData['timestamp'] ) && isset( $videoData['id'] ) ){
							$videoId = $videoData['timestamp'].'|'.$videoData['id'];
							$videos[ $videoId ] = $videoData;
						}
					}
					foreach( $data['lists']['BLACKLIST'] as $page ){
						$videoData = $oRelatedVideosService->getRelatedVideoData( 0, $page['title'], $page['source'] );
						if ( isset( $videoData['timestamp'] ) && isset( $videoData['id'] ) ){
							$videoId = $videoData['timestamp'].'|'.$videoData['id'];
							$blacklist[ $videoId ] = $videoData;
						}
					}
				}
			}
		}

		foreach( $blacklist as $key => $blElement ){
			unset( $videos[ $key ] );
		}

		ksort( $videos );
		$videos = array_reverse( $videos, true );

		$this->setVal( 'videos', $videos );
	}

	public function getVideo(){

		$title = urldecode($this->getVal( 'title' ));
		$external = $this->getVal( 'external', '' );
		$external = empty( $external ) ? null : $this->app->wg->wikiaVideoRepoDBName;
		$cityShort = $this->getVal('cityShort');
		$videoHeight = $this->getVal('videoHeight');

		$oRelatedVideosService = F::build('RelatedVideosService');
		$result = $oRelatedVideosService->getRelatedVideoDataFromTitle( $title, $external, VideoPage::DEFAULT_OASIS_VIDEO_WIDTH, $cityShort, $videoHeight );
		if ( isset( $result['error'] ) ){
			$this->setVal( 'error', $result['error'] );
		} else {
			$this->setVal( 'width', intval( $result['thumbnailData']['width'] ) );
			$this->setVal( 'height', intval( $result['thumbnailData']['height'] ) );
			$this->setVal( 'json', $result['embedJSON'] );
			if ( !empty( $result['embedJSON'] ) && isset( $result['embedJSON']['id'] ) ){
				$videoHtml = '<div id="'.$result['embedJSON']['id'].'"></div>';
			} else {
				$videoHtml = $result['embedCode'];
			}
			$this->setVal( 'html',
				 $this->app->renderView(
					'RelatedVideos',
					'getVideoHtml',
					array(
						 'videoHtml' => $videoHtml,
						 'embedUrl' => empty( $result['external'] ) ? '' : $result['fullUrl']
					)
				)
			 );
			$this->setVal( 'title', $result['title'] );
			if ( !empty( $result['external'] ) ){
				$this->setVal( 'embedUrl', $result['fullUrl'] );
			}
		}
	}

	public function getVideoHtml(){
		$videoHtml = $this->getVal( 'videoHtml' );
		$embedUrl = $this->getVal( 'embedUrl' );

		$this->setVal( 'videoHtml', $videoHtml );
		$this->setVal( 'embedUrl', $embedUrl );
	}
	
	/*
	 * get data for an article stored in NS_RELATED_VIDEOS 
	 */
	public function getLists() {

		$titleStr = $this->request->getVal('title', null);
		$title = Title::newFromText($titleStr, NS_RELATED_VIDEOS);
		$relatedVideosNSData = RelatedVideosNamespaceData::newFromTitle($title);
		$this->setVal('data', $relatedVideosNSData->getData());
	}

	/*
	 * for getting videos localy and cross wiki
	 */

	public function getVideoData() {
		
		$videoArticleId = $this->getVal('articleId', 0);
		$videoName = urldecode($this->getVal( 'title', '' ));
		$width = $this->getVal( 'width', 0 );
		$useMaster = $this->getVal( 'useMaster', 0 );
		$videoWidth = $this->getVal( 'videoWidth', VideoPage::DEFAULT_OASIS_VIDEO_WIDTH );
		$videoHeight = $this->getVal( 'videoHeight', '' );
		$cityShort = $this->getVal( 'cityShort', 'life');
		if ($videoArticleId) {
			$videoTitle = Title::newFromID($videoArticleId, GAID_FOR_UPDATE);
			$useMaster = true;
		} else {
			$videoTitle = Title::newFromText( $videoName, NS_VIDEO );
			$useMaster = ( false || !empty( $useMaster ) );
		}

		$rvd = new RelatedVideosData();
		$videoData = $rvd->getVideoData( $videoTitle, $width, $videoWidth, true, $useMaster, $cityShort, $videoHeight );
		$this->setVal( 'data', $videoData );
	}

	public function getCaruselElement(){

		$video = $this->getVal( 'video' );
		$preloaded = $this->getVal( 'preloaded' );
		
		$this->setVal( 'video', $video );
		$this->setVal( 'preloaded', $preloaded );
	}

	public function getAddVideoModal(){
		
		$this->setVal( 'html', $this->app->renderView( 'RelatedVideos', 'addVideoModalText' ) );
		$this->setVal( 'title',	wfMsg('related-videos-add-video-to-this-page') );
	}

	public function addVideoModalText(){
		
	}
	
	public function addVideo() {

		$url = urldecode($this->getVal('url', ''));
		$articleId = $this->getVal('articleId', '');
		$rvd = F::build('RelatedVideosData');
		$retval = $rvd->addVideo($articleId, $url);
		if (is_array($retval)) {
			$rvs = F::build('RelatedVideosService');
			$data = $rvs->getRelatedVideoDataFromMaster( $retval['articleId'], $retval['title'], $retval['external']);
			$this->setVal('html', $this->app->renderView( 'RelatedVideos', 'getCaruselElement', array('video'=>$data, 'preloaded'=>1) ));
			$this->setVal('error', isset( $data['error'] ) ? $data['error'] : null);
		}
		else {
			$this->setVal('data', null);
			$this->setVal('error', $retval);
		}

	}
	
	public function removeVideo() {
		
		$articleId = $this->getVal('articleId', '');
		$title = urldecode($this->getVal('title', ''));
		$external = $this->getVal('external', 0);
		$rvd = F::build('RelatedVideosData');
		$retval = $rvd->removeVideo($articleId, $title, $external);
		if (is_string($retval)) {
			$this->setVal('error', $retval);
		}
		else {
			$this->setVal('error', null);
		}
		
	}
}
