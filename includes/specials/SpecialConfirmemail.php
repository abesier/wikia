<?php

/**
 * Special page allows users to request email confirmation message, and handles
 * processing of the confirmation code when the link in the email is followed
 *
 * @ingroup SpecialPage
 * @author Brion Vibber
 * @author Rob Church <robchur@gmail.com>
 */
class EmailConfirmation extends UnlistedSpecialPage {

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct( 'Confirmemail' );
	}

	/**
	 * Main execution point
	 *
	 * @param $code Confirmation code passed to the page
	 */
	function execute( $code ) {
		global $wgUser, $wgOut;
		$this->setHeaders();

		/* Wikia change begin - @author: Uberfuzzy */
		/* manual confirm code entry */
		if( empty( $code ) ) {
			#no code passed as execute param,
			#attempt to pull code from URL (as sent by manual form), and put where normal flow expects
			global $wgRequest;
			$code = $wgRequest->getText( 'code' );
			$code = trim($code);
		} else 
		{
			#execute param not empty, try to catch new state here
			if( $code === 'manual' ) {
				$this->showManualForm();
				return;
			}
		}
		/* wikia change end */

		if( empty( $code ) ) {
			if( $wgUser->isLoggedIn() ) {
				if( User::isValidEmailAddr( $wgUser->getEmail() ) ) {
					/* Wikia change - begin */
					global $wgEnableUserLoginExt;
					if ( empty($wgEnableUserLoginExt) ) {
						$this->showRequestForm();
					} else {
						UserLoginHelper::getInstance()->showRequestFormConfirmEmail( $this );
					}
					/* Wikia change - end */
				} else {
					$wgOut->addWikiMsg( 'confirmemail_noemail' );
				}
			} else {
				$title = SpecialPage::getTitleFor( 'Userlogin' );
				$skin = $wgUser->getSkin();
				$llink = $skin->linkKnown(
					$title,
					wfMsgHtml( 'loginreqlink' ),
					array(),
					array( 'returnto' => $this->getTitle()->getPrefixedText() )
				);
				$wgOut->addHTML( wfMsgWikiHtml( 'confirmemail_needlogin', $llink ) );
			}
		} else {
			$this->attemptConfirm( $code );
		}
	}

	/**
	 * Show a nice form for the user to request a confirmation mail
	 */
	function showRequestForm() {
		global $wgOut, $wgUser, $wgLang, $wgRequest;
		if( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getText( 'token' ) ) ) {
			// Wikia change -- only allow one email confirmation attempt per hour
			if (strtotime($wgUser->mEmailTokenExpires) - strtotime("+6 days 23 hours") > 0) return;
			$ok = $wgUser->sendConfirmationMail();
			if ( WikiError::isError( $ok ) ) {
				$wgOut->addWikiMsg( 'confirmemail_sendfailed', $ok->toString() );
			} else {
				$wgOut->addWikiMsg( 'confirmemail_sent' );
			}
		} else {
			if( $wgUser->isEmailConfirmed() ) {
				// date and time are separate parameters to facilitate localisation.
				// $time is kept for backward compat reasons.
				// 'emailauthenticated' is also used in SpecialPreferences.php
				$time = $wgLang->timeAndDate( $wgUser->mEmailAuthenticated, true );
				$d = $wgLang->date( $wgUser->mEmailAuthenticated, true );
				$t = $wgLang->time( $wgUser->mEmailAuthenticated, true );
				$wgOut->addWikiMsg( 'emailauthenticated', $time, $d, $t );
				return;  // Wikia change -- don't show button at all if email is already confirmed (spam vector)
			}
			if( $wgUser->isEmailConfirmationPending() ) {
				$wgOut->wrapWikiMsg( "<div class=\"error mw-confirmemail-pending\">\n$1</div>", 'confirmemail_pending' );
				// Wikia change -- only allow one email confirmation attempt per hour
				if (strtotime($wgUser->mEmailTokenExpires) - strtotime("+6 days 23 hours") > 0) return;
			}
			$wgOut->addWikiMsg( 'confirmemail_text' );
			$form  = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $this->getTitle()->getLocalUrl() ) );
			$form .= Xml::hidden( 'token', $wgUser->editToken() );
			$form .= Xml::submitButton( wfMsg( 'confirmemail_send' ) );
			$form .= Xml::closeElement( 'form' );
			$wgOut->addHTML( $form );
		}
	}

	/* Wikia change begin - @author: Uberfuzzy */
	/**
	 * Show a specialized form for manual code entry
	 */
	function showManualForm() {
		global $wgOut;

		$self = SpecialPage::getTitleFor( 'ConfirmEmail' );

		$form  = Xml::openElement( 'form', array( 'method' => 'post', 'action' => $self->getLocalUrl() ) );
		$form .= Xml::input( 'code', 40 );
		$form .= ' ' . Xml::submitButton( 'Confirm' );
		$form .= Xml::closeElement( 'form' );
		$wgOut->addHTML( Xml::fieldset( wfMsg('enterconfirmcode'), $form) );
	}
	/* Wikia change end */

	/**
	 * Attempt to confirm the user's email address and show success or failure
	 * as needed; if successful, take the user to log in
	 *
	 * @param $code Confirmation code
	 */
	function attemptConfirm( $code ) {
		global $wgUser, $wgOut;
		$user = User::newFromConfirmationCode( $code );
		if( is_object( $user ) ) {
			$user->confirmEmail();
			$user->saveSettings();
			$message = $wgUser->isLoggedIn() ? 'confirmemail_loggedin' : 'confirmemail_success';
			$wgOut->addWikiMsg( $message );
			if( !$wgUser->isLoggedIn() ) {
				$title = SpecialPage::getTitleFor( 'Userlogin' );
				$wgOut->returnToMain( true, $title );
			}
			wfRunHooks( 'ConfirmEmailComplete', array( &$user ) );
		} else {
			$wgOut->addWikiMsg( 'confirmemail_invalid' );
		}
	}

}

/**
 * Special page allows users to cancel an email confirmation using the e-mail
 * confirmation code
 *
 * @ingroup SpecialPage
 */
class EmailInvalidation extends UnlistedSpecialPage {

	public function __construct() {
		parent::__construct( 'Invalidateemail' );
	}

	function execute( $code ) {
		$this->setHeaders();
		$this->attemptInvalidate( $code );
	}

	/**
	 * Attempt to invalidate the user's email address and show success or failure
	 * as needed; if successful, link to main page
	 *
	 * @param $code Confirmation code
	 */
	function attemptInvalidate( $code ) {
		global $wgUser, $wgOut;
		$user = User::newFromConfirmationCode( $code );
		if( is_object( $user ) ) {
			$user->invalidateEmail();
			$user->saveSettings();
			$wgOut->addWikiMsg( 'confirmemail_invalidated' );
			if( !$wgUser->isLoggedIn() ) {
				$wgOut->returnToMain();
			}
		} else {
			$wgOut->addWikiMsg( 'confirmemail_invalid' );
		}
	}
}