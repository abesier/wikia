<?php
/**
 * Adds top title edit button (RT #37771)
 *
 * @author Bartek Lapinski <bartek@wikia-inc.com>
 */
$wgExtensionCredits['other'][] = array(
		'name' => 'TitleEdit',
		'description' => 'Adds top title edit buttons',
		'version' => '0.7',
		'author' => array('Bartek Lapinski')
		);

$wgHooks['MonacoPrintFirstHeading'][] = 'wfTitleEditPrintFirstHeading';


function wfTitleEditPrintFirstHeading() {
	global $wgTitle, $wgUser, $wgRequest;

	if ( $wgTitle->isProtected( 'edit' ) ) {
		return true;
	}

	if( 'edit' == $wgRequest->getVal( 'action', false ) ) {
		return true;
	}

	$sk = $wgUser->getSkin();
	$result = '';

	if (is_object($wgUser) && $wgUser->isLoggedIn()) {
		$link = $sk->link( $wgTitle, wfMsg('editsection'), // todo is it truly only 'edit' message?
			array( 'onclick' => '"WET.byStr(\'articleAction/topedit\')"'),
			array( 'action' => 'edit'),
			array( 'noclasses', 'known' )
			);
		$result = wfMsgHtml( 'editsection-brackets', $link );
		$result = "<span class=\"editsection-upper\">$result</span>";
	} else { // anon
		if ( empty($wgDisableAnonymousEditig)) {
			$link = "<a class=\"wikia_button\" onclick=\"WET.byStr(\'articleAction/topedit\')\" href=\"" . $wgTitle->getEditUrl() . "\"><span>" . wfMsg( 'editsection' ) . "</span></a>";
			$result = "<span class=\"editsection-upper\">$link</span>";
		}
	}

	echo $result;
	return true;
}


