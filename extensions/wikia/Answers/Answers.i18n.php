<?php

/**
 * Internationalization for Answer extension
 *
 * @package MediaWiki
 * @subpackage Extensions
 *
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License 2.0 or later
 *
 */

if (!defined('MEDIAWIKI')) die();

$messages = array();

$messages['en'] = array(
	'answer_title' => 'Answer',
	'answered_by' => 'Answered by',
	'unregistered' => 'Unregistered',
	'anonymous_edit_points' => '$1 {{PLURAL:$1|helper|helpers}}',
	'edit_points' => '{{PLURAL:$1|edit point|edit points}}',
	'ask_a_question' => 'Ask a question...',
	'ask_a_question-widget' => 'Ask a question...',
	'in_category' => '...in category',
	'ask_button' => 'Ask',
	'ask_thanks' => 'Thanks for the rockin\' question!',
	'question_asked_by' => 'Question asked by',
	'question_asked_by_a_wikia_user' => 'Question asked by a Wikia user',
	'new_question_comment' => 'new question',
	'answers_toolbox' => 'Wikianswers toolbox',
	'improve_this_answer' => 'Improve this answer',
	'answer_this_question' => 'Answer this question',
	'notify_improved' => 'Email me when improved',
	'research_this' => 'Research this',
	'notify_answered' => 'Email me when answered',
	'recent_asked_questions' => 'Recently Asked Questions',
	'recent_answered_questions' => 'Recently Answered Questions',
	'recent_edited_questions' => 'Recently Edited Questions',
	'unanswered_category' => 'Un-answered questions',
	'answered_category' => 'Answered questions',
	'related_questions' => 'Related questions',
	'related_answered_questions' => 'Related answered questions',
	'recent_unanswered_questions' => 'Recent Unanswered Questions',
	'popular_categories' => 'Popular Categories',
	'createaccount-captcha' => 'Please type the word below',
	'inline-register-title' => 'Notify me when my question is answered!',
	'inline-welcome' => 'Welcome to Wikianswers',
	'skip_this' => 'Skip this',
	'see_all_changes' => 'See all changes',
	'toolbox_anon_message' => '<i>"Wikianswers leverages the unique characterstics of a wiki to form the very best answers to any question."</i><br /><br /> <b>Jimmy Wales</b><br /> founder of Wikipedia and Wikianswers',
	'no_questions_found' => 'No questions found',
	'widget_settings'	=> 'Question Settings',
	'style_settings'	=> 'Style Settings',
	'get_widget_title' => 'Add Questions to your site',
	'background_color' => 'Background color',
	'widget_category' => 'Type of Questions',
	'category' => 'Category Name',
	'custom_category' => 'Custom Category',
	'number_of_items' => 'Number of items to show',
	'width'		=> 'Width',
	'next_page'		=> 'Next &raquo;',
	'prev_page'		=> '&laquo; Prev',
	'see_all'		=> 'See all',
	'get_code'	=> 'Grab Code',
	'link_color'	=> 'Question Link Color',
	'widget_order' => 'Question Order',
	'widget_ask_box' => 'Include ask box',
	'question_redirected_help_page' => 'Why was my question redirected here',
	'twitter_hashtag' => 'wikianswers',
	'twitter_ask' => 'Ask on Twitter',
	'facebook_ask' => 'Ask on Facebook',
	'facebook_send_request' => 'Send Directly to Friends',
	'ask_friends' => 'Ask your friends to help answer:',
	'facebook_send_request_content' => 'Can you help answer this? $1',
	'facebook_signed_in' => 'You are signed into Facebook Connect',
	'ads_by_google' => 'Ads by Google',
	'magic_answer_headline' => 'Does this answer your question?',
	'magic_answer_yes' => 'Yes, use this as a starting point',
	'magic_answer_no' => 'No, don\'t use this',
	'magic_answer_credit' => 'Provided by Yahoo Answers',
	'rephrase' => 'Rephrase this question',
	'rephrase_this' => '<a href="$1" $2>Reword the question</a>',
	'question_not_answered' => 'This question has not been answered',
	'you_can' => 'You can:',
	'answer_this' => '<a href="$1">Answer this question</a>, even if you don\'t know the whole answer',
	'research_this_on_wikipedia' => '<a href="$1">Research this question</a> on Wikipedia',
	'receive_email' => '<a href="$1" $2>Receive an email</a> when this question is answered',
	'ask_friends_on_twitter' => 'Ask Friends on <a href="$1" $2>Twitter</a>',
	'quick_action_panel' => 'Quick Action Panel',
	'categorize' => 'Categorize',
	'categorize_help' => 'One category per line',
	'answers_widget_admin_note' => '<b>Admins:</b> If you\'d like to be an admin on <a href="http://answers.wikia.com" target="_blank">Wikianswers</a>, <a href="http://answers.wikia.com/wiki/Wikianswers:Become_an_admin" target="_blank">click here</a>.',
	'answers_widget_user_note' => 'Can you help by becoming a <a href="http://answers.wikia.com/wiki/Wikianswers:Sign_up_for_a_category" target="_blank">category editor</a> on <a href="http://answers.wikia.com" target="_blank">Wikianswers</a>?',
	'answers_widget_anon_note' => '<a href="http://answers.wikia.com" target="_blank">Wikianswers</a> is a Q&amp;A wiki where answers are improved, wiki-style.',
	'answers-category-count-answered' => 'This category contains $1 answered questions.',
	'answers-category-count-unanswered' => 'This category contains $1 unanswered questions.',
	'answers_widget_no_questions' => '<a href="http://answers.wikia.com" target="_blank">Wikianswers</a> is a site where you can ask questions and contribute answers. We\'re aiming to create the best answer to any question. <a href="http://answers.wikia.com/wiki/Special:Search" target="_blank">Find</a> and answer <a href="http://answers.wikia.com/wiki/Category:Un-answered_questions">unanswered</a> questions. It\'s a wiki - so be bold!',
	'answers_widget_no_questions_askabout' => '<br><br>Get started by asking a question about "{{PAGENAME}}"',
	'reword_this' => '<a href="$1" $2>Reword this question</a> ',
	'no_related_answered_questions' => 'There are no related questions yet. Get a <a href="http://answers.wikia.com/wiki/Special:Randomincategory/answered_questions">random answered question instead</a>, or ask a new one!<br />
	<div class="createbox" align="center">
	<p></p><form name="createbox" action="/index.php" method="get" class="createboxForm">
	<input name="action" value="create" type="hidden">
	<input name="prefix" value="Special:CreateQuestionPage/" type="hidden">
	<input name="editintro" value="" type="hidden">
	<input class="createboxInput" name="title" value="" size="50" type="text">
	<input name="create" class="createboxButton" value="Type your question and click here" type="submit"></form></div>',
	'auto_friend_request_body' => 'Will you add me as a friend?',
	'tog-hidefromattribution' => 'Hide my avatar and name from attribution list',
	'q' => '<!-- -->',
	'a' => 'Answer:',
	'?' => '?',
	'answering_tips' => "<h3>Tips for answering:</h3> When contributing an answer, try to be as accurate as you can. If you're getting information from another source such as Wikipedia, put a link to this in the text. And thank you for contributing to {{SITENAME}}!",
	'header_questionmark_pre' => '',
	'header_questionmark_post' => '?',
	'plus_x_more_helpers' => '... plus $1 more helpers',
	'answer_this_question' => 'Answer this question:',
	'plus_x_more_helpers' => '... plus $1 more helpers',
	'answer_this_question' => 'Answer this question:',
	'anwb-step1-headline' => 'What\'s your wiki about?',
	'anwb-step1-text' => 'Your Wikianswers site needs a <strong>tagline</strong>.<br /><br />Your tagline will help people find your site from search engines, so try to be clear about what your site is about.',
	'anwb-step1-example' => 'Answers for all your pro-wrestling questions!',
	'anwb-choose-logo' => 'Choose your logo',
	'anwb-step2-text' => 'Next, choose a logo for {{SITENAME}}. It\'s best to upload a picture that you think represents your Answers site.<br />You can skip this step if you don\'t want to do it right now.<br /><br />',
	'anwb-step2-example' => 'This would be a good logo for a skateboarding answers site.',
	'anwb-fp-headline' => 'Create some questions!',
	'anwb-fp-text' => 'Your Answers site should start off with some questions!<br /><br />Add a list of questions, and then provide the answers yourself. It\'s important to get some useful information on the site, so people can find it and ask and answer even more questions.',
	'anwb-fp-example' => '<strong>Example</strong><br /><br />For a pet care answers site:<br /><br /><ul><li>Should I buy cat litter?</li><li>What\'s the best breed of dog?</li><li>What\'s the best way to train a cat?</li><li></ul><br /><br />For a health care answers site:<br /><br /><ul><li>What are the health benefits of exercise?</li><li>How can I find a good doctor in my area?</li><li>How can I lose weight easily?</li></ul>',
	'nwb-thatisall-headline' => 'That\'s it - you\'re done!',
	'anwb-thatisall-text' => 'That\'s it - you\'re ready to roll!<br /><br />Now it\'s time to start writing more questions and answers, so that your site can be found more easily in search engines.<br /><br />The list of questions added in the last step has been put into your questions site. Head in to answer your questions, and start your own answers community!',
	'anwb-logo-preview' => 'Here\'s a preview of your logo',
	'anwb-save-tagline' => 'Save tagline',
	'badWords' => 'fuck', // testing a bug

	// toolbox
	'qa-toolbox-button' => 'Answer a random question',
	'qa-toolbox-share' => 'Share',
	'qa-toolbox-tools' => 'Advanced tools»',
	'qa-toolbox-protect' => 'Protect this question',
	'qa-toolbox-delete' => 'Delete this question',
	'qa-toolbox-history' => 'Past versions of this page',
	'qa-featured-sites' => '-',

	// Skin Chooser
	'answers_skins' => 'Answers',
	'answers-bluebell' => 'Bluebell',
	'answers-leaf' => 'Leaf',
	'answers-carnation' => 'Carnation',
	'answers-sky' => 'Sky',
	'answers-spring' => 'Spring',
	'answers-forest' => 'Forest',
	'answers-moonlight' => 'Moonlight',
	'answers-carbon' => 'Carbon',
	'answers-obsession' => 'Obsession',
	'answers-custom' => 'Custom',
);
