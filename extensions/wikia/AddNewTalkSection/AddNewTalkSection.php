<?php

/**
 * AddNewTalkSection
 *
 * A AddNewTalkSection extension for MediaWiki
 * Make long talk pages easier to use, by adding an "add new section" link to the page end.
 *
 * @author Maciej Błaszkowski (Marooned) <marooned at wikia-inc.com>
 * @date 2009-05-19
 * @copyright Copyright (C) 2009 Maciej Błaszkowski, Wikia Inc.
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 * @package MediaWiki
 *
 * To activate this functionality, place this file in your extensions/
 * subdirectory, and add the following line to LocalSettings.php:
 *     require_once("$IP/extensions/wikia/AddNewTalkSection/AddNewTalkSection.php");
 */

if (!defined('MEDIAWIKI')) {
	echo "This is MediaWiki extension named AddNewTalkSection.\n";
	exit(1) ;
}

$wgExtensionCredits['other'][] = array(
	'name' => 'AddNewTalkSection',
	'author' => '[http://www.wikia.com/wiki/User:Marooned Maciej Błaszkowski (Marooned)]',
	'description' => 'Make long talk pages easier to use, by adding an "add new section" link to the page end.'
);

$wgExtensionFunctions[] = 'AddNewTalkSectionInit';
$wgExtensionMessagesFiles['AddNewTalkSection'] = dirname(__FILE__) . '/AddNewTalkSection.i18n.php';

/**
 * Initialize hooks
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function AddNewTalkSectionInit() {
	global $wgHooks;
	$wgHooks['CustomArticleFooter'][] = 'AddNewTalkSectionAddFooter';
	$wgHooks['SkinTemplateSetupPageCss'][] = 'AddNewTalkSectionAddCSS';
	$wgHooks['EditPage::importFormData::finished'][] = 'AddNewTalkSectionImportFormData';
	$wgHooks['EditPage::showEditForm:fields'][] = 'AddNewTalkSectionAddFormFields';
//	$wgHooks['UserToggles'][] = 'AddNewTalkSectionToggleUserPreference';
//	$wgHooks['getEditingPreferencesTab'][] = 'AddNewTalkSectionToggleUserPreference';
}

/**
 * add CSS to style new link
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function AddNewTalkSectionAddCSS(&$out) {
	global $wgExtensionsPath, $wgStyleVersion;
	$out = "@import url($wgExtensionsPath/wikia/AddNewTalkSection/AddNewTalkSection.css?$wgStyleVersion);";
	return true;
}

/**
 * add link to the bottom of the article
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function AddNewTalkSectionAddFooter(&$skin, &$tpl, &$custom_article_footer) {
	global $wgTitle;
	if ($wgTitle->getNamespace() == NS_USER_TALK) {
		global $wgHooks, $wgOut, $wgUser, $wgExtensionsPath, $wgStyleVersion;
		wfLoadExtensionMessages('AddNewTalkSection');
		$link = wfMsg('addnewtalksection-link');
		if (wfEmptyMsg('addnewtalksection-link', $link)) {
			$link = wfMsg('Add_comment');
		}
		$skin = $wgUser->getSkin();
		$link = $skin->makeKnownLinkObj( $wgTitle, $link, 'action=edit&section=new&src=footer' );
		$custom_article_footer .= '<div id="AddNewTalkSectionFooter"><div id="AddNewTalkSectionImage"></div><span id="AddNewTalkSectionLink">' . $link . '</span></div>';
	}
	return true;
}

/**
 * handle adding new section as a first one on EditPage POST
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function AddNewTalkSectionImportFormData($editPage, $request) {
	global $wgAddNewTalkSectionOnTop;
	if (!empty($wgAddNewTalkSectionOnTop) && $request->wasPosted() && $editPage->mTitle->exists()) {
		$sourceType = $request->getVal('wpAddNewTalkSectionSourceType');
		if ($sourceType == 'footer' && !($editPage->preview || $editPage->diff)) {
			global $wgParser;
			//grab section 0 (from the begining to the first heading)
			$section0 = $wgParser->getSection($editPage->mArticle->getContent(), '0');
			//format heading subject -> == subject ==
			$subject = $editPage->summary ? wfMsgForContent('newsectionheaderdefaultlevel', $editPage->summary) . "\n\n" : '';
			//append user content to the section 0
			$text = strlen( trim( $section0 ) ) > 0
					? "{$section0}\n\n{$subject}{$editPage->textbox1}"
					: "{$subject}{$editPage->textbox1}";
			//replace section 0 with new content
			$text = $editPage->mArticle->replaceSection('0', $text, $editPage->summary);
			//as we are in 'add new section' mode, change it so MW will replace whole article (with our changes) - not add new text at the bottom
			$editPage->section = '';
			$editPage->textbox1 = $text;
		}
	}
	return true;
}

/**
 * Add hidden field with source info
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
function AddNewTalkSectionAddFormFields($editPage, $wgOut) {
	global $wgRequest;
	//add hidden field to distinguish between regular 'start a new section' and our new one
	if ($wgRequest->getVal('wpAddNewTalkSectionSourceType', $wgRequest->getVal('src')) == 'footer') {
		$wgOut->addHTML('<input type="hidden" value="footer" name="wpAddNewTalkSectionSourceType" />');
	}
	return true;
}

/**
 * Toggle ANTS in user preferences
 *
 * @author Maciej Błaszkowski <marooned at wikia-inc.com>
 */
//function AddNewTalkSectionToggleUserPreference($toggles, $default_array = false) {
//	if(is_array($default_array)) {
//		$default_array[] = 'disableaddnewtalksection';
//	} else {
//		$toggles[] = 'disableaddnewtalksection';
//	}
//	return true;
//}