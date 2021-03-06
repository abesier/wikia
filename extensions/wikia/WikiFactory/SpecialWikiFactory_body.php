<?php

/**
 * @package MediaWiki
 * @subpackage SpecialPage
 * @author Krzysztof Krzyżaniak <eloy@wikia-inc.com> for Wikia.com
 * @version: 0.1
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	echo "This is MediaWiki extension and cannot be used standalone.\n";
	exit( 1 ) ;
}


/**
 * @addtogroup SpecialPage
 */
class WikiFactoryPage extends SpecialPage {

	private $mWiki, $mTitle, $mDomain, $mTab, $mVariableName, $mTags, $mSearchTag;
	public $mStatuses = array(-2 => 'spam', -1=> 'disabled*', "disabled", "enabled", "redirected" );
	private $mTagWikiIds = array();

	/**
	 * constructor
	 */
	function  __construct() {
		parent::__construct( "WikiFactory"  /*class*/, 'wikifactory' /*restriction*/);
	}

	/**
	 * execute
	 *
	 * main entry point
	 *
	 * @access public
	 *
	 * @param string $subpage: subpage of Title
	 *
	 * @return nothing
	 */
	public function execute( $subpage ) {
		global $wgUser, $wgOut, $wgRequest;

		if( $wgUser->isBlocked() ) {
			throw new UserBlockedError( $this->getUser()->mBlock );
		}
		if( wfReadOnly() && !wfAutomaticReadOnly() ) {
			$wgOut->readOnlyPage();
			return;
		}
		if( !$wgUser->isAllowed( 'wikifactory' ) ) {
			$this->displayRestrictionError();
			return;
		}

		$this->mTitle = Title::makeTitle( NS_SPECIAL, 'WikiFactory' );
		$this->mDomain = false;

		/**
		 * initial output
		 */
		$wgOut->setPageTitle( wfMsg('wikifactorypagetitle') );
		$wgOut->setRobotpolicy( 'noindex,nofollow' );
		$wgOut->setArticleRelated( false );

		#--- handle chagelog
		if ( $subpage === "change.log" ) {
			$oPager = new ChangeLogPager;

			$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
			$oTmpl->set_vars( array(
				"limit"     => $oPager->getForm(),
				"body"      => $oPager->getBody(),
				"nav"       => $oPager->getNavigationBar()
			));
			$wgOut->addHTML( $oTmpl->render("changelog") );
		}
		elseif ( in_array( strtolower($subpage), array( "metrics", "metrics/main", "metrics/monthly", "metrics/daily" ) ) ) {
			$oAWCMetrics = new WikiMetrics();
			$oAWCMetrics->show( $subpage );
		}
		elseif ( strpos( $subpage, "short.stats" ) === 0 ) {
			$subpageOptions = explode( '/', $subpage );
			$lang = isset( $subpageOptions[1] ) ? $subpageOptions[1] : null;
			$wgOut->addHTML( $this->shortStats( $lang ) );
		}
		elseif ( strpos( $subpage, "long.stats" ) === 0 ) {
			$subpageOptions = explode( '/', $subpage );
			$lang = isset( $subpageOptions[1] ) ? $subpageOptions[1] : null;
			$wgOut->addHTML( $this->longStats( $lang ) );
		}
		elseif ( strtolower($subpage) === "add.variable" ) {
			$varOverrides = array();
			$wgOut->addHTML( $this->doAddVariableForm($varOverrides) ); // process the post (if relevant).
			$wgOut->addHTML( $this->addVariableForm($varOverrides) ); // display the form
		}
		else {
			$subpage = ( $subpage == "/" ) ? null : $subpage;
			$oWiki = $this->getWikiData( $subpage );

			if( !isset( $oWiki->city_id )) {
				$this->doWikiSelector();
			}
			else {
				$this->mWiki = $oWiki;
				$this->mTags = new WikiFactoryTags( $this->mWiki->city_id );
				$this->doWikiForm( );
			}
		}
	}

	/**
	 * getWikiData
	 *
	 * use subpage as param and try to find wiki which match criteria
	 *
	 * @access private
	 *
	 * @param mixed $subpage:
	 *
	 * @return Database Row from city_list
	 */
	private function getWikiData( $subpage ) {
		global $wgRequest;

		$domain	= $wgRequest->getVal( "wpCityDomain", null );
		$cityid	= $wgRequest->getVal( "cityid", null );
		$tab = "variables";
		if( is_null( $cityid ) && ( isset( $subpage ) || isset( $domain ) ) ) {

			/**
			 * if there is # in subpage we are switching tab
			 */
			if( strpos( $subpage, "/" ) ) {
				$parts = explode( "/", $subpage, 3 );
				if( is_array( $parts ) && sizeof( $parts ) >= 2 ) {
					$subpage = $parts[0];
					$tab = $parts[1];
					if ( ( $tab === "variables" ) && ( isset($parts[2]) ) ) {
						$this->mVariableName = trim($parts[2]);
					}
					if ( ( $tab === "tags" || $tab === "findtags"  ) && ( isset($parts[2]) ) ) {
						$this->mSearchTag = trim($parts[2]);
					}
				}
			}

			if( is_null( $domain ) ) {
				$domain = $subpage;
			}

			if( ctype_digit( $subpage ) ) {
				$cityid = $subpage;
			}
			else {
				/**
				 * if $domain starts with db: it means that we want to search by database
				 * if $domain starts with id: it means that we want to search by city_id
				 */
				$cityid = null;
				if( preg_match( "/^db\:/", $domain ) ) {
					list( $prefix, $name ) = explode( ":", $domain, 2 );
					$cityid = WikiFactory::DBtoID( strtolower( $name ) );
				}
				elseif( preg_match( "/^id\:/", $domain ) ) {
					list( $prefix, $cityid ) = explode( ":", $domain, 2 );
				}
				else {
					/**
					 * if there's no dot in cityname we add .wikia.com
					 * or if is only one dot (language.domain.wikia.com)
					 */
					if( sizeof(explode(".", $domain )) <= 2 && strlen( $domain ) > 0) {
						$this->mDomain = $domain;
						$domain = $domain.".wikia.com";
					} else {
						list( $code, $subdomain ) = explode(".", $domain, 2 );
						$exists = 0;
						if ( in_array( $code, array_keys( Language::getLanguageNames() ) ) ) {
							$exists = 1;
						} else {
							$cityid = WikiFactory::DomainToId( $domain );
							if ( !is_null($cityid) ) {
								$exists = 1;
							}
						}
						if ( $exists == 1 ) {
							$this->mDomain = $code.".".$subdomain;
							$domain = sprintf("%s.%s", $code, $subdomain );
						}
					}
					if ( is_null($cityid) ) {
						$cityid = WikiFactory::DomainToId( $domain );
					}
				}
			}
		}
		$this->mTab = $tab;
		if( !is_null( $cityid ) ) {
			$this->mTitle = Title::makeTitle( NS_SPECIAL, "WikiFactory/{$cityid}/{$tab}" );
		}
		if ( !isset($this->mVariableName) ) {
			$this->mVariableName = "";
		}
		return WikiFactory::getWikiByID( $cityid );
	}

	/**
	 * doWikiSelector
	 *
	 * Pager with all wikis
	 *
	 * @access private
	 * @author eloy
	 *
	 * @return nothing
	 */
	private function doWikiSelector() {
		global $wgOut;

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array(
			"title"  => $this->mTitle,
			"domain" => $this->mDomain
		));

		if( !empty( $this->mDomain ) ) {
			$pager = new CityListPager( $this->mDomain );
			$oTmpl->set( "pager", $pager->render() );
		}
		$wgOut->addHTML( $oTmpl->render( "selector" ) );
	}

	/**
	 * doWikiForm
	 *
	 * show wiki data
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @return nothing
	 */
	public function doWikiForm() {
		global $wgOut, $wgRequest, $wgStylePath;
		global $wgDevelEnvironment;

		$info = null;


		/**
		 * check maybe something was posted
		 */
		if( $wgRequest->wasPosted() ) {
			switch( $this->mTab ) {
				case "hubs":
					$info = $this->doUpdateHubs( $wgRequest );
					break;
				case "domains":
					$info = $this->doUpdateDomains( $wgRequest );
					break;
				case "tags":
					$info = $this->doUpdateTags( $wgRequest );
					break;
				case "findtags":
					#we have 2 things that post from this page :(
					if( $wgRequest->getVal('wpSearchTag') != null ) {
						$this->mSearchTag = $wgRequest->getVal('wpSearchTag');
						$info = $this->doSearchTags( $this->mSearchTag );
					}

						$this->mRemoveTag = $wgRequest->getVal('remove_tag');
						$this->mRemoveTags = $wgRequest->getIntArray('remove_tag_id');
					if( $this->mRemoveTag != null &&
						$this->mRemoveTags != null ) {
						$info = $this->doMultiRemoveTags();
					}
					break;
				case "masstags":
						$this->mMassTag = $wgRequest->getVal('wpMassTag');
						$this->mMassTagWikis = $wgRequest->getVal('wfMassTagWikis');

					if( $this->mMassTag != null && $this->mMassTagWikis != null ) {
						$info = $this->doMassTag();
					}
					else
					{
						if( $this->mMassTag == null )
						{
							$info = Wikia::errorbox('missing tag to apply to wikis');
						}
						if( $this->mMassTagWikis == null )
						{
							$info = Wikia::errorbox('missing wikis to apply tag to');
						}
					}
					break;
				case "ezsharedupload":
					if($wgRequest->getVal('ezsuSave') != null) {
						$info = $this->doSharedUploadEnable( $wgRequest );
					}
					break;
				case "google":
					if ($wgRequest->getVal('wt-add-site')) {
						$this->doAddToWebmasterTools();
					} else if ($wgRequest->getVal('wt-rem-site')) {
						$this->doRemoveFromWebmasterTools();
					} else if ($wgRequest->getVal('wt-verify-site')) {
						$this->doVerifyWithWebmasterTools();
					} else if ($wgRequest->getVal('wt-add-verify-site')) {
						if ($this->doAddToWebmasterTools()) {
							$this->doVerifyWithWebmasterTools();
						}
					}
					break;
			}
			wfRunHooks('WikiFactory::onPostChangesApplied', array($this->mWiki->city_id));
		}
		else {
			/**
			 * maybe some other action but with GET not POST
			 */
			switch( $this->mTab ) {
				case "tags":
					$tag_id  = $wgRequest->getVal( "wpTagId", false );
					if( $tag_id ) {
						$info = $this->doUpdateTags( $wgRequest, $tag_id );
					}
					break;
				case "findtags":
					if ( !empty( $this->mSearchTag ) ) {
						$info = $this->doSearchTags( $this->mSearchTag );
					}
					break;
			}
		}

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$vars = array(
			"tab"         => $this->mTab,
			"hub"         => WikiFactoryHub::getInstance(),
			"wiki"        => $this->mWiki,
			"tags"        => $this->mTags->getTags(),
			"info"        => $info,
			"title"       => $this->mTitle,
			"groups"      => WikiFactory::getGroups(),
			"cluster"     => WikiFactory::getVarValueByName( "wgDBcluster", $this->mWiki->city_id ),
			"domains"     => WikiFactory::getDomains( $this->mWiki->city_id ),
			"statuses" 	  => $this->mStatuses,
			"variables"   => WikiFactory::getVariables(),
			"variableName"=> $this->mVariableName,
			"isDevel"     => $wgDevelEnvironment,
			'wikiFactoryUrl' => Title::makeTitle( NS_SPECIAL, 'WikiFactory' )->getFullUrl(),
			'wgStylePath' => $wgStylePath,
		);
		if( $this->mTab === 'info' ) {
			$vars[ 'founder_id' ] = $this->mWiki->city_founding_user;

			if( !empty( $this->mWiki->city_founding_user ) ) {
				#if we knew who they were, get their current info
				$fu = User::newFromId( $this->mWiki->city_founding_user );
				$vars[ 'founder_username' ] = $fu->getName();
				$vars[ 'founder_usermail' ] = $fu->getEmail();
			}
			else
			{	#dont know who made the wiki, so dont try to do lookups
				$vars[ 'founder_username' ] = null;
				$vars[ 'founder_usermail' ] = null;
			}

			#this is the static stored email
			$vars[ 'founder_email' ] = $this->mWiki->city_founding_email;
		}
		if( $this->mTab === "tags" ||  $this->mTab === "findtags" ) {
			$vars[ 'searchTag' ] = $this->mSearchTag;
			$vars[ 'searchTagWikiIds' ] = $this->mTagWikiIds;
		}
		if( $this->mTab === "clog" ) {
			$pager = new ChangeLogPager( $this->mWiki->city_id );
			$vars[ "changelog" ] = array(
				"limit"     => $pager->getForm(),
				"body"      => $pager->getBody(),
				"nav"       => $pager->getNavigationBar()
			);
		}
		if( $this->mTab === "google" ) {
			global $wgGoogleWebToolsAccts;

			$api = new WebmasterToolsAPI('', '', $this->mWiki);
			$info = $api->site_info();

			$vars[ "google" ] = array(
				"info"     => $info,
				"accounts" => $wgGoogleWebToolsAccts,
				"api"      => $api,
			);
		}
		if( $this->mTab === "ezsharedupload" ) {
			global $wgServer;
			$vars[ "EZSharedUpload" ] = array(
				"active" => WikiFactory::getVarValueByName( "wgUseSharedUploads", $this->mWiki->city_id ),
				"varTitle" => Title::makeTitle( NS_SPECIAL, 'WikiFactory' )->getFullUrl() . ( "/" . $this->mWiki->city_id . "/variables/" ),
				"info" => ( isset($info) ? $info : "" ),
				"local"  => array(
					"wgServer" => $this->mWiki->city_url,
					"wgSharedUploadDBname" => WikiFactory::getVarValueByName( "wgSharedUploadDBname", $this->mWiki->city_id ),
					"wgSharedUploadDirectory" => WikiFactory::getVarValueByName( "wgSharedUploadDirectory", $this->mWiki->city_id ),
					"wgSharedUploadPath" => WikiFactory::getVarValueByName( "wgSharedUploadPath", $this->mWiki->city_id ),
					"wgRepositoryBaseUrl" => WikiFactory::getVarValueByName( "wgRepositoryBaseUrl", $this->mWiki->city_id ),
					"wgFetchCommonsDescriptions" => WikiFactory::getVarValueByName( "wgFetchCommonsDescriptions", $this->mWiki->city_id )
			),
				"remote" => array(
					"wikiId" => 0,
					"wgServer" => "",
					"wgDBname" => "",
					"wgUploadDirectory" => "",
					"wgUploadPath" => "",
					"baseUrl" => ""
				)
			);
			if( $wgRequest->wasPosted() && $wgRequest->getVal( "ezsuWikiId" ) ) {
				$ezsuRemoteWikiId = $wgRequest->getVal( "ezsuWikiId" );
				$vars[ "EZSharedUpload" ][ "remote" ] = array(
					"wikiId" => $ezsuRemoteWikiId,
					"wgServer" => WikiFactory::getVarValueByName( "wgServer", $ezsuRemoteWikiId ),
					"wgDBname" => WikiFactory::getVarValueByName( "wgDBname", $ezsuRemoteWikiId ),
					"wgUploadDirectory" => WikiFactory::getVarValueByName( "wgUploadDirectory", $ezsuRemoteWikiId ),
					"wgUploadPath" => WikiFactory::getVarValueByName( "wgUploadPath", $ezsuRemoteWikiId ),
					"baseUrl" => WikiFactory::getVarValueByName( "wgServer", $ezsuRemoteWikiId ) . WikiFactory::getVarValueByName( "wgScriptPath", $ezsuRemoteWikiId ) . str_replace( '$1', 'File:', WikiFactory::getVarValueByName( "wgArticlePath", $ezsuRemoteWikiId ) )
				);
			}
		}
		$oTmpl->set_vars( $vars );
		$wgOut->addHTML( $oTmpl->render("form") );
	}

	/**
	 * doUpdateHubs
	 *
	 * Store changes connected with hubs
	 *
	 * @access private
	 *
	 * @return mixed	info when change, null when not changed
	 */
	private function doUpdateHubs( &$request ) {
		$cat_id = $request->getVal( "wpWikiCategory", null );
		$reason = $request->getVal( "wpReason", null );
		if( !is_null( $cat_id ) ){
			$hub = WikiFactoryHub::getInstance();
			$hub->setCategory( $this->mWiki->city_id, $cat_id, $reason );
			$categories = $hub->getCategories();

			// ugly fast fix for fb#9937 (until all the hub management is cleaned up)
			global $wgMemc;
			$key = sprintf("%s:%d", 'WikiFactory::getCategory', intval($this->mWiki->city_id));
			$wgMemc->delete( $key );

			return Wikia::successbox( "Hub is now set to: ". $categories[ $cat_id ]['name'] );
		}
		else {
			return Wikia::successbox( "Hub was not changed.");
		}
	}

	/**
	 * enable shared uploads on wiki
	 *
	 * @access private
	 * @param WebRequest $request
	 * @return string
	 */
	private function doSharedUploadEnable( &$request ) {
		$remoteWikiId = $request->getVal('ezsuWikiId');
		if(!empty($remoteWikiId)) {
			$remoteWikiData = array(
				"wgDBname" => WikiFactory::getVarValueByName( "wgDBname", $remoteWikiId ),
				"wgUploadDirectory" => WikiFactory::getVarValueByName( "wgUploadDirectory", $remoteWikiId ),
				"wgUploadPath" => WikiFactory::getVarValueByName( "wgUploadPath", $remoteWikiId ),
				"baseUrl" => WikiFactory::getVarValueByName( "wgServer", $remoteWikiId ) . WikiFactory::getVarValueByName( "wgScriptPath", $remoteWikiId ) . str_replace( '$1', 'File:', WikiFactory::getVarValueByName( "wgArticlePath", $remoteWikiId ) )
			);

			// set variables
			WikiFactory::setVarByName( "wgSharedUploadDBname", $this->mWiki->city_id, $remoteWikiData['wgDBname'] );
			WikiFactory::setVarByName( "wgSharedUploadDirectory", $this->mWiki->city_id, $remoteWikiData['wgUploadDirectory'] );
			WikiFactory::setVarByName( "wgSharedUploadPath", $this->mWiki->city_id, $remoteWikiData['wgUploadPath'] );
			WikiFactory::setVarByName( "wgRepositoryBaseUrl", $this->mWiki->city_id, $remoteWikiData['baseUrl'] );
			WikiFactory::setVarByName( "wgUseSharedUploads", $this->mWiki->city_id, true );
			WikiFactory::setVarByName( "wgFetchCommonsDescriptions", $this->mWiki->city_id, true );

			return "<span style=\"color: green; font-weight: bold;\">Saved and enabled! :)</span>";
		}
		else {
			return "<span style=\"color: red; font-weight: bold;\">Invalid data :(</span>";
		}
	}

	/**
	 * doUpdateDomains
	 *
	 * Store changes connected with domains
	 *
	 * @access private
	 * @author eloy@wikia
	 *
	 * @return mixed	info when change, null when not changed
	 */
	private function doUpdateDomains( &$request ) {
		$action = $request->getText( "wpAction", null );
		$reason = $request->getText( "wpReason", wfMsg( 'wikifactory-public-status-change-default-reason' ) );
		$message = "";
		switch( $action ) {
			case "status":
				$status = $request->getVal( "wpWikiStatus", 0 );
				WikiFactory::setPublicStatus( $status, $this->mWiki->city_id, $reason );
				$this->mWiki->city_public = $status;
				WikiFactory::clearCache( $this->mWiki->city_id );
				$message = "Status of this wiki was changed to " . $this->mStatuses[ $status ];
			break;
		}
		return Wikia::successmsg( $message );
	}

	/**
	 * doUpdateTags
	 *
	 * @access private
	 * @author eloy@wikia-inc.com
	 *
	 * @param WebRequest $request  WebRequest instance
	 * @param Boolean    $tag_id   set if removing, tag_id of removing tag
	 *
	 * @return mixed	info when change, null when not changed
	 */
	private function doUpdateTags( &$request, $tag_id = false ) {
		#global $wgOut;

		wfProfileIn( __METHOD__ );

		$msg  = false;
		$wfTags = new WikiFactoryTags( $this->mWiki->city_id );

		if( $tag_id ) {
			$tagName = $request->getText( "wpTagName" );
			$newTags = $this->mTags->removeTagsById( array( $tag_id ) );
			#$wgOut->addHTML('<pre>' . print_r($newTags,1) . "</pre>" );
			$msg = "Tag {$tagName} removed";

		}
		else {
			$stag = $request->getText( "wpTag", false );

			if( $stag ) {
				$before = $wfTags->getTags( true ); // from master
				$after  = $wfTags->addTagsByName( $stag );
				$diff   = array_diff( $after, $before );
				#$wgOut->addHTML('<pre>' );
				#$wgOut->addHTML( print_r($before,1) ."\n" );
				#$wgOut->addHTML( print_r($after,1) ."\n");
				#$wgOut->addHTML( print_r($diff,1) ."\n");
				#$wgOut->addHTML( "</pre>");

				$msg = "Added new tags: " . implode( ", ", $diff );
			}
			else {
				$msg = "Nothing to add";
			}
		}
		wfProfileOut( __METHOD__ );

		return Wikia::successbox( $msg );
	}

	/**
	 * doMassTag
	 *
	 * data is stored in:
	 *  $this->mMassTag
	 *  $this->mMassTagWikis
	 *
	 * @access private
	 * @author uberfuzzy@wikia-inc.com
	 * @note yes, i know this is HORRIBLY ineffecient, but I was going for RAD, not clean.
	 */
	private function doMassTag() {

		wfProfileIn( __METHOD__ );

		$this->mMassTagWikis = explode("\n", $this->mMassTagWikis);
		global $wgOut;

		$msg  = false;
		$added=0;

		foreach($this->mMassTagWikis as $dart_string)
		{
			if( empty($dart_string) ) { continue; }
			$dart_parts = explode("/", $dart_string);
			if( count($dart_parts) < 2 ) {
				continue;
			}
			$db = ltrim($dart_parts[1], '_');

			$wkid = WikiFactory::DBtoID( $db );
			// $wgOut->addHTML("id={$wkid}<br/>"); #debug

			$added++;
			$oTag = new WikiFactoryTags( $wkid );
			$oTag->addTagsByName( $this->mMassTag );
			#$oTag->clearCache();
		}

		$oTQ = new WikiFactoryTagsQuery(''); // no tag, because we're just using it to uncache
		$oTQ->clearCache();

		$msg = "Added '{$this->mMassTag}' to {$added} wikis";
		wfProfileOut( __METHOD__ );

		return Wikia::successbox( $msg );
	}

	/**
	 * doSearchTags
	 *
	 * @access private
	 * @author adi@wikia-inc.com
	 */
	private function doSearchTags( $searchTag ) {
		if( empty( $searchTag ) ) {
			$result = Wikia::errorbox( "Nothing to search" );
		}
		else {
			$tagsQuery = new WikiFactoryTagsQuery( $searchTag );
			$this->mTagWikiIds = $tagsQuery->doQuery();
				#print "(filled mTagWikiIds with a WikiFactoryTagsQuery from inside doSearchTags ".gmdate('r').")";

			if( count( $this->mTagWikiIds ) == 0 ) {
				$result = Wikia::errorbox( "No wikis with \"" . $searchTag . "\" tag assigned" );
			}
			else {
				$result = Wikia::successbox( count( $this->mTagWikiIds ) . " wiki(s) found with \"" . $searchTag . "\" tag assigned" );
				$result ='';
			}
		}
		return $result;
	}

	/**
	 * doMultiRemoveTags
	 *
	 * @access private
	 * @author uberfuzzy@wikia-inc.com
	 *
	 * @return text message (use Wikia::*box functions)
	 */
	private function doMultiRemoveTags( ) {

		/* working data is stored in object prior to call
			$this->mRemoveTag; has the tag to remove
			$this->mRemoveTags; has int array of wiki id to remove from
		*/

		/* in theory, these should never trigger, but BSTS */
		if( empty( $this->mRemoveTag ) ) {
			return Wikia::errorbox( "no tag to remove?" );
		}
		if( empty( $this->mRemoveTags ) ) {
			return Wikia::errorbox( "no items to remove?" );
		}

		/* turn the tag string into the tag id */
		$tagID = WikiFactoryTags::idFromName( $this->mRemoveTag );
		if( $tagID === false ) {
			return Wikia::errorbox( "tag [{$this->mRemoveTag}] doesnt exist" );
		}

		/* to get list of all wikis with this tag, and later, use this to cache clear */
		$tagsQuery = new WikiFactoryTagsQuery( $this->mRemoveTag );

			$fails = array();
		foreach($this->mRemoveTags as $wkid)
		{
			$oTag = new WikiFactoryTags( $wkid );
			$ret = $oTag->removeTagsById( array($tagID) );
			if($ret === false) {
				$fails[] = $wkid;
			}
		}

		/* force dump of the tag_map in memcache */
		$tagsQuery->clearCache();

		/* since we /hopefully/ removed some tags from wikis,
			force the search results for this pageload to be empty. */
		$this->mTagWikiIds = array();
		#print "(forcing mTagWikiIds to null at ".gmdate('r').")";

		if( empty($fails) ) {
			return Wikia::successbox( "ok!" );
		}else{
			return Wikia::errorbox( "ok, but failed at ".count($fails)." wikis".
									" (" . implode(", ", $fails ) . ")" );
		}

	}

	/**
	 * showTab
	 *
	 * helper function, CSS/HTML code for tab
	 *
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 * @access public
	 * @static
	 *
	 * @param string	$tab		current tab
	 * @param string 	$active		active tab
	 * @param integer	$city_id	wiki id in city_list table
	 * @param string	$tabtext	text to use that is not $tab
	 *
	 * @return string HTML/CSS code
	 */
	static public function showTab( $tab, $active, $city_id, $tabtext=null ) {

		$title = Title::makeTitle( NS_SPECIAL, "WikiFactory/{$city_id}/{$tab}" );

		if( empty($tabtext) ) {
			$text = wfMsg( "wikifactory-label-{$tab}" );
		}
		else {
			$text = wfMsg( "wikifactory-label-{$tabtext}" );
		}

		if( $tab === $active ) {
			#return $text;
			$attribs = array(
				"href" => $title->getFullUrl()
			);
			return Xml::element( 'a', $attribs, $text );
		}
		else {
			$attribs = array(
				"href" => $title->getFullUrl()
			);
			return Xml::element( 'a', $attribs, $text );
		}
	}

	private function shortStats( $lang = null ) {
		return $this->doStats( 90, $lang );
	}
	private function longStats( $lang = null ) {
		return $this->doStats( null, $lang );
	}

	private function doStats( $days = null, $lang = null ) {
		global $wgOut;

		$where = null;
		if( !empty($days) ) {
			$ymd = gmdate('Y-m-d', strtotime("{$days} days ago"));
			$where = array("city_created > '{$ymd}'");
		}

		if ( !empty( $lang ) ) {
			$where['city_lang'] = $lang;
		}

		$dbr = WikiFactory::db( DB_SLAVE );
		$res = $dbr->select(
			array( "city_list" ),
			array(
				"date(city_created) as date",
				"city_public",
				"count(*) as count"
			),
			$where,
			__METHOD__,
			array(
				  "GROUP BY" => "date(city_created), city_public",
				  "ORDER BY" => "date(city_created) desc"
			)
		);
		$stats = array();
		while( $row = $dbr->fetchObject( $res ) ) {
			if( !isset( $stats[ $row->date ] ) ){
				$stats[ $row->date ] = (object) null;
			}
			$stats[ $row->date ]->total += $row->count;
			switch( $row->city_public ) {
				case 1:
					$stats[ $row->date ]->active += $row->count;
					break;
				case 0:
					$stats[ $row->date ]->disabled += $row->count;
					break;
				case 2:
					$stats[ $row->date ]->redirected += $row->count;
					break;
			}
		}
		$dbr->freeResult( $res );

		$wgOut->setPageTitle( strtoupper( $lang ) . ' Wikis created daily' );

		$Tmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$Tmpl->set( "stats", $stats );
		$Tmpl->set( "days", $days );

		return $Tmpl->render( "shortstats" );
	}

	/**
	 * Quick form for introducing a new variable to be used in WikiFactory (not setting a value).
	 *
	 * @author Sean Colombo
	 * @access private
	 *
	 * @param varOverrides array - associative array of values to put into the template.  These are assumed
	 *                             to have been loaded as a form re-initialization and are given precedence
	 *                             over the defaults.
	 *
	 * @return HTML to be rendered.
	 */
	private function addVariableForm($varOverrides = array()) {
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );

		$vars = array(
			"title"         => $this->mTitle,
			"groups"        => WikiFactory::getGroups(),
			"accesslevels"  => WikiFactory::$levels,
			"types"         => WikiFactory::$types,
		);
		$vars = array_merge($vars, $varOverrides);
		$oTmpl->set_vars( $vars );

		return $oTmpl->render( "add-variable" );
	}

	/**
	 * Quick form for choosing which variable to change.
	 *
	 * @author Sean Colombo
	 * @access private
	 *
	 * @param varOverrides array - associative array of values to put into the template.  These are assumed
	 *                             to have been loaded as a form re-initialization and are given precedence
	 *                             over the defaults.
	 *
	 * @return HTML to be rendered.
	 */
	private function chooseVariableToChange($varOverrides = array()) {
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );

		$vars = array(
			//"tab"         => $this->mTab,
			"hub"         => WikiFactoryHub::getInstance(),
			"wiki"        => $this->mWiki,
			"title"       => $this->mTitle,
			"groups"      => WikiFactory::getGroups(),
			"cluster"     => WikiFactory::getVarValueByName( "wgDBcluster", $this->mWiki->city_id ),
			"domains"     => WikiFactory::getDomains( $this->mWiki->city_id ),
			"statuses" 	  => $this->mStatuses,
			"variables"   => WikiFactory::getVariables(),
			"variableName"=> $this->mVariableName,
		);
		$vars = array_merge($vars, $varOverrides);
		$oTmpl->set_vars( $vars );

		return $oTmpl->render( "form-variables" );
	}

	/**
	 * If there was a post to the add variable form, this will process it.
	 *
	 * @author Sean Colombo
	 * @access private
	 *
	 * @param varOverrides array - array that will be filled (by reference) with any values
	 *                             which should be used as overrides for form re-initialization
	 *                             (for instance, if there was an error in the form we start where
	 *                             the user left off instead of starting from scratch).
	 *
	 * @return any additional HTML that should be rendered as a result of the form post.
	 */
	private function doAddVariableForm(&$varOverrides){
		global $wgRequest;
		$html = "";
		if( $wgRequest->wasPosted() ) {
			$cv_name = $wgRequest->getVal("cv_name");
			$cv_variable_type = $wgRequest->getVal("cv_variable_type");
			$cv_access_level = $wgRequest->getVal("cv_access_level");
			$cv_variable_group = $wgRequest->getVal("cv_variable_group");
			$cv_description = $wgRequest->getval("cv_description");
			$cv_is_unique = $wgRequest->getval("cv_is_unique", "0");

			// Verify that the form is filled out, then add the variable if it is (display an error if it isn't).
			$err = "";
			if($cv_name == ""){
				$err .= "<li>Please enter a name for the variable.</li>\n";
			}
			if(!in_array($cv_variable_type, WikiFactory::$types)){
				$err .= "<li>The value \"$cv_variable_type\" was not recognized as a valid WikiFactory::\$type.</li>\n";
			}
			if(!in_array($cv_access_level, array_keys(WikiFactory::$levels))){
				$err .= "<li>The value \"$cv_access_level\" was not recognized as a valid key from WikiFactory::\$levels.</li>\n";
			}
			if(!in_array($cv_variable_group, array_keys(WikiFactory::getGroups()))){
				$err .= "<li>The value \"$cv_variable_group\" was not recognized as a valid group_id from city_variables_groups table as returned by WikiFactory::getGroups()</li>\n";
			}
			if($cv_description == ""){
				$err .= "<li>Please enter a description of what the variable is used for.</li>\n";
			}
			if($err == ""){
				$success = WikiFactory::createVariable($cv_name, $cv_variable_type, $cv_access_level, $cv_variable_group, $cv_description, $cv_is_unique);
				if($success){
					$html .= "<div style='border:1px #0f0 solid;background-color:#afa;padding:5px'><strong>$cv_name</strong> successfully added to WikiFactory.</div>";
				} else {
					$html .= "<div style='border:1px #f00 solid;background-color:#faa;padding:5px'>";
					$html .= "<strong>ERROR: There was a database error while trying to create the variable.  Please see the logs for more info.</strong>";
					$html .= "</div>";
				}
			} else {
				$html .= "<div style='border:1px #f00 solid;background-color:#faa;padding:5px'>";
				$html .= "<strong>ERROR: Unable to add variable!</strong>";
				$html .= "<ul>\n$err</ul>\n";
				$html .= "</div>";

				$varOverrides['cv_name'] = $cv_name;
				$varOverrides['cv_variable_type'] = $cv_variable_type;
				$varOverrides['cv_access_level'] = $cv_access_level;
				$varOverrides['cv_variable_group'] = $cv_variable_group;
				$varOverrides['cv_description'] = $cv_description;
				$varOverrides['cv_is_unique'] = $cv_is_unique;
			}
		}
		return $html;
	}

	private function doAddToWebmasterTools () {
		global $wgRequest, $wgGoogleWebToolsAccts;
		$acct_name = $wgRequest->getVal('wt-account-name');

		if (array_key_exists($acct_name, $wgGoogleWebToolsAccts)) {
			$acct_pass = $wgGoogleWebToolsAccts[$acct_name]['pass'];

			$api = new WebmasterToolsAPI($acct_name, $acct_pass, $this->mWiki);
			$api->add_site();
			return true;
		} else {
			// Display error
			return false;
		}
	}

	private function doRemoveFromWebmasterTools () {
		$api = new WebmasterToolsAPI('', '', $this->mWiki);
		$api->remove_site();

		return true;
	}

	private function doVerifyWithWebmasterTools () {
		$api = new WebmasterToolsAPI('', '', $this->mWiki);
		$api->verify_site();

		return true;
	}
}

/**
 * Changelog Pager
 */
class ChangeLogPager extends TablePager {

	public
		$mFieldNames = null,
		$mMessages = array(),
		$mQueryConds = array(),
		$mTitle,
		$mWikiId;

	/**
	 * __construct
	 *
	 * Public constructor with standard initializations
	 *
	 * @access public
	 * @author Krzysztof Krzyżaniak <eloy@wikia.com>
	 *
	 * @param integer $wiki_id	wiki identifier in wiki factory
	 *
	 */
	function __construct( $wiki_id = false ) {
		if( is_numeric( $wiki_id ) ) {
			$this->mTitle = Title::makeTitle( NS_SPECIAL, "WikiFactory/{$wiki_id}/clog" );
			$this->mWikiId = $wiki_id;
		}
		else {
			$this->mTitle = Title::makeTitle( NS_SPECIAL, "WikiFactory/change.log" );
			$this->mWikiId = false;
		}
		$this->mDefaultDirection = true;
		parent::__construct();
	}

	/**
	 * getTitle
	 *
	 * @return Title object
	 */
	function getTitle() {
		return $this->mTitle;
	}

	/**
	 * getFieldNames
	 *
	 * @return Array with column names
	 */
	public function getFieldNames() {
		if( !$this->mFieldNames ) {
			$this->mFieldNames = array();
			if( ! $this->mWikiId ) {
				$this->mFieldNames["city_url"]      = "Wiki";
			}
			$this->mFieldNames["cl_timestamp"]  = "Changed";
			$this->mFieldNames["cl_type"]       = "Type";
			$this->mFieldNames["cl_user_id"]    = "Who";
			$this->mFieldNames["cl_text"]       = "What";
		}

		return $this->mFieldNames;
	}

	/**
	 * isFieldSortable
	 *
	 * @param string $field: field name
	 *
	 * @return boolean: flag if $field is sortable of not
	*/
	public function isFieldSortable( $field ) {
		static $aSortable = array( "city_url", "cl_type", "cl_timestamp", "cl_user_id" );
		return in_array( $field, $aSortable );
	}

	/**
	 * formatValue
	 *
	 * field formatter
	 *
	 * @param string $field: field name
	 * @param mixed $value: field value
	 *
	 * @return string: formated table field
	 */
	function formatValue( $field, $value ) {
		switch ($field) {
			case "city_url":
				preg_match("/http:\/\/([\w\.\-]+)\//", $value, $matches );
				$sRetval = str_ireplace(".wikia.com", "", $matches[1]);
				return $sRetval;
				break;

			case "cl_timestamp":
				return wfTimestamp( TS_EXIF, $value );
				break;
			case "cl_type":
				switch( $value ) {
					case WikiFactory::LOG_CATEGORY:
						return "category";
						break;
					case WikiFactory::LOG_VARIABLE:
						return "variable";
						break;
					case WikiFactory::LOG_DOMAIN:
						return "domain";
						break;
					case WikiFactory::LOG_STATUS:
						return "status";
						break;
				}
				break;

			case "cl_user_id":
				$oUser = User::newFromId( $value );
				$oUser->load();
				return sprintf("<a href=\"%s\">%s</a>", $oUser->getUserPage()->getLocalUrl(), $oUser->getName());
				break;

			default:
				return $value;
		}
	}

	/**
	 * getDefaultSort
	 *
	 * get default field for sorting
	 *
	 * @return string: table field
	 */
	function getDefaultSort() {
		$this->mDefaultDirection = true;
		return "cl_timestamp";
	}

	/**
	 * getQueryInfo
	 *
	 * get default field for sorting
	 *
	 * @return array: query info
	 */
	function getQueryInfo() {
		$query = array(
			"tables" => array(
				WikiFactory::table("city_list_log"),
				WikiFactory::table("city_list"),
			),
			"fields" => array( "*" ),
			"conds" => array(
				WikiFactory::table("city_list", "city_id" )
					. " = "
					. WikiFactory::table( "city_list_log", "cl_city_id" )
			)
		);

		if( $this->mWikiId ) {
			$query[ "conds" ][] = WikiFactory::table("city_list", "city_id" )
				. " = "
				. $this->mWikiId;
		}
		return $query;
	}

	/**
	 * getForm
	 *
	 * get form definition
	 *
	 * @return string: empty
	 */
	function getForm() {
		return "";
	}
}


/**
 * CityListPager( $this->mDomain );
 */
class CityListPager {

	private $mPart, $mRequest, $mLimit, $mOffset, $mTemplate, $mTitle;

	/**
	 * constructor
	 *
	 * @access public
	 */
	public function __construct( $part ) {
		global $wgRequest;

		$this->mPart     = $part;
		$this->mRequest  = $wgRequest;
		$this->mLimit    = 25;
		$this->mOffset   = $this->mRequest->getVal( "offset", false );
		$this->mTitle    = Title::makeTitle( NS_SPECIAL, 'WikiFactory' );;
		$this->mTemplate = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
	}

	/**
	 * render page of pager
	 *
	 * @access public
	 */
	public function render() {

		global $wgOut;
		$this->mTemplate->set( "part",  $this->mPart );
		$this->mTemplate->set( "data",  $this->getData() );
		$this->mTemplate->set( "limit", $this->mLimit );
		$this->mTemplate->set( "title", $this->mTitle );

		return $this->mTemplate->render( "listing" );
	}

	private function getData() {

		/**
		 * build query
		 */
		wfProfileIn( __METHOD__ );
		$offset = $this->mOffset ? array( "OFFSET" => $this->mOffset ) : array();
		$dbr = WikiFactory::db( DB_SLAVE );
		$sth = $dbr->select(
			array( "city_domains" ),
			array( "distinct city_id" ),
			array( "city_domain " . $dbr->buildLike( $dbr->anyString(), $this->mPart, $dbr->anyString() ) ),
			__METHOD__,
			array(
				"ORDER BY" => "city_id",
				"LIMIT" => $this->mLimit + 1
			) + $offset
		);
		$data = array();
		$counter = 0;
		while( $row = $dbr->fetchObject( $sth ) ) {
			$obj = new stdClass;
			$obj->wiki = WikiFactory::getWikiByID( $row->city_id );
			$obj->domains = WikiFactory::getDomains( $row->city_id );
			if( $counter <= $this->mLimit ) {
				$data[] = $obj;
			}
			else {
				/**
				 * there's next page
				 */
			}
		}
		wfProfileOut( __METHOD__ );
		return $data;
	}

	static public function bold( $subject, $search ) {
		echo str_replace( $search, "<strong>{$search}</strong>", $subject );
	}
};

class WebmasterToolsAPI {

	const FEED_URI = 'https://www.google.com/webmasters/tools/feeds';

	private $mAuth, $mEmail, $mPass, $mType, $mSource, $mService, $mWiki, $mSiteURI;

	/**
	 * constructor
	 * If email and password are empty, will use the default preferred account from '
	 *
	 * @access public
	 */
	public function __construct( $email, $pass, $wiki ) {
		global $wgGoogleWebToolsAccts;

		// If email and password weren't specified, use the preferred account.
		if(empty($email) && empty($pass)){
			if(is_array($wgGoogleWebToolsAccts)){
				foreach($wgGoogleWebToolsAccts as $acctEmail => $acctData){
					if(isset($acctData['preferred']) && ($acctData['preferred'] == 1)){
						$email = $acctEmail;
						$pass = $acctData['pass'];
						break;
					}
				}

				// If no preferred email/pass was found, just grab the first one
				if(empty($email) && empty($pass) && (count($wgGoogleWebToolsAccts) > 0)){
					$keys = array_keys($wgGoogleWebToolsAccts);
					$email = $keys[0];
					$pass = (isset($wgGoogleWebToolsAccts[$email]['pass']) ? $wgGoogleWebToolsAccts[$email]['pass'] : "");
				}
			}
		}

		$this->mEmail   = $email;
		$this->mPass    = $pass;
		$this->mType    = 'GOOGLE';
		$this->mSource  = 'WIKIA';
		$this->mService = 'sitemaps';

		if (!is_object($wiki)) {
			$wiki = WikiFactory::getWikiByID( $wiki );
			if (!$wiki) {
				throw new Exception("Could not find wiki by ID");
			}
		}
		$this->mWiki    = $wiki;
		$this->mSiteURI = $this->make_site_uri();
		$this->mAuth    = $this->getAuthToken();
	}

	private function getAuthToken () {
		$content = Http::post('https://www.google.com/accounts/ClientLogin',
							null,
							array(CURLOPT_POSTFIELDS => array(
										"Email"       => $this->mEmail,
										"Passwd"      => $this->mPass,
										"accountType" => $this->mType,
										"source"      => $this->mSource,
										"service"     => $this->mService,
									)
							)
					);

		if (preg_match('/Auth=(\S+)/', $content, $matches)) {
			return $matches[1];
		} else {
			return;
		}
	}

	private function normalize_site ($site) {
		if (!preg_match('!^http://!', $site)) $site = 'http://'.$site;
		if (!preg_match('!/$!', $site))       $site = $site.'/';

		return $site;
	}

	private function make_site_uri () {
		$site = $this->normalize_site($this->mWiki->city_url);
		$uri = self::FEED_URI . '/sites/' . urlencode($site);
		return $uri;
	}

	private function make_site_id () {
		return $this->normalize_site($this->mWiki->city_url).'sitemap-index.xml';
	}

	public function site_info () {
		$content = Http::get($this->mSiteURI,
							null,
							array(CURLOPT_HTTPHEADER => array('Authorization: GoogleLogin auth='.$this->mAuth))
					);
		if (!$content) return;

		$doc = new DOMDocument();
		$doc->loadXML($content);

		$e = $doc->documentElement;
		$info = array();

		foreach ($e->childNodes as $node) {
			switch ($node->nodeName) {
				case 'updated':
					$info['updated'] = $node->nodeValue;
					break;
				case 'wt:verified':
					$info['verified'] = $node->nodeValue == 'true' ? true : false;
					break;
				case 'wt:verification-method':
					if (preg_match('/google([a-f0-9]+)\.html/', $node->nodeValue, $matches)) {
						$info['verification_code'] = $matches[1];
					}
					break;
				case 'title':
					$info['site'] = $node->nodeValue;
					break;
				default:
					//error_log("#### NODE: ".$node->nodeName.'='.$node->nodeValue);
			}
		}

		$info['account_name'] = $this->mEmail;

		return $info;
	}

	public function add_site () {

		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars(array( "site" => $this->normalize_site($this->mWiki->city_url)));
		$xml = $oTmpl->render("wt-add-request");

		$content = Http::post($uri = self::FEED_URI . '/sites/',
							null,
							array(CURLOPT_POSTFIELDS => $xml,
									CURLOPT_HTTPHEADER => array('Content-type: application/atom+xml',
																'Authorization: GoogleLogin auth='.$this->mAuth)
							)
					);

		if ($content) {
			WikiFactory::setVarByName('wgGoogleWebToolsAccount', $this->mWiki->city_id, $this->mEmail);

			return true;
		} else {
			return false;
		}
	}

	public function remove_site () {
		global $wgHTTPTimeout, $wgHTTPProxy, $wgTitle, $wgVersion;

		// Update the wgGoogleSiteVerification variable with this code
		WikiFactory::setVarByName('wgGoogleSiteVerification', $this->mWiki->city_id, '');
		WikiFactory::setVarByName('wgGoogleWebToolsAccount', $this->mWiki->city_id, '');

		$c = curl_init( $this->mSiteURI );
		curl_setopt($c, CURLOPT_PROXY, $wgHTTPProxy);
		curl_setopt($c, CURLOPT_TIMEOUT, $wgHTTPTimeout);
		curl_setopt($c, CURLOPT_USERAGENT, "MediaWiki/$wgVersion");
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'DELETE' );
		curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: GoogleLogin auth='.$this->mAuth));

		curl_exec( $c );

		# Don't return the text of error messages, return false on error
		$retcode = curl_getinfo( $c, CURLINFO_HTTP_CODE );

		if ( $retcode != 200 ) {
			error_log("Failed to delete site ".$this->mWiki->site_url." from Webmaster Tools.  Code=".$retcode);
			return false;
		} else {
			return true;
		}
	}

	public function verify_site ($code = null) {

		if (!$code) {
			$info = $this->site_info();
			$code = $info['verification_code'];
		}

		// Update the wgGoogleSiteVerification variable with this code
		WikiFactory::setVarByName('wgGoogleSiteVerification', $this->mWiki->city_id, $code);

		// Send the verification request to google
		$oTmpl = new EasyTemplate( dirname( __FILE__ ) . "/templates/" );
		$oTmpl->set_vars( array( "site_id" => $this->make_site_id()) );
		$xml = $oTmpl->render("wt-verify-request");

		if ($this->put_verify($xml)) {
			return true;
		} else {
			return false;
		}
	}

	private function put_verify ( $xml ) {
		global $wgHTTPTimeout, $wgHTTPProxy, $wgTitle, $wgVersion;

		$c = curl_init( $this->mSiteURI );
		curl_setopt($c, CURLOPT_PROXY, $wgHTTPProxy);
		curl_setopt($c, CURLOPT_TIMEOUT, $wgHTTPTimeout);
		curl_setopt($c, CURLOPT_USERAGENT, "MediaWiki/$wgVersion");
		curl_setopt($c, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($c, CURLOPT_CUSTOMREQUEST, 'PUT' );

		curl_setopt($c, CURLOPT_HTTPHEADER, array('Authorization: GoogleLogin auth='.$this->mAuth,
												  'Content-type: application/atom+xml'));

		curl_setopt($c, CURLOPT_POSTFIELDS, $xml);

		ob_start();
		curl_exec( $c );
		$text = ob_get_contents();
		ob_end_clean();

		# Don't return the text of error messages, return false on error
		$retcode = curl_getinfo( $c, CURLINFO_HTTP_CODE );

		if ( $retcode != 200 ) {
			wfDebug( __METHOD__ . ": HTTP return code $retcode\n" );
			$text = false;
		}
		# Don't return truncated output
		$errno = curl_errno( $c );
		if ( $errno != CURLE_OK ) {
			$errstr = curl_error( $c );
			wfDebug( __METHOD__ . ": CURL error code $errno: $errstr\n" );
			$text = false;
		}
		curl_close( $c );

		return $text;
	}
};
