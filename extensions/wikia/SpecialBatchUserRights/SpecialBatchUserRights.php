<?php
/**
 * Special page to allow adding user group membership to a large number of users at once.
 * (such as adding a couple of hundred people to the "beta" user group).
 *
 * @file
 * @ingroup SpecialPage
 */

// TODO: Move this to a SpecialBatchUserRights.setup.php file:
$wgAvailableRights[] = 'batchuserrights';



/**
 * A class to manage user levels rights.
 * @ingroup SpecialPage
 */
class BatchUserRightsPage extends SpecialPage {
	protected $isself = false;

	public function __construct() {
		parent::__construct( 'BatchUserRights' );
	}

	public function isRestricted() {
		return true;
	}

	public function userCanExecute( $user ) {
		return $this->userCanChangeRights( $user, false );
	}

	public function userCanChangeRights( $user, $checkIfSelf = true ) {
		$available = $this->changeableGroups();
		return !empty( $available['add'] )
			or !empty( $available['remove'] )
			or ( ( $this->isself || !$checkIfSelf ) and
				(!empty( $available['add-self'] )
				 or !empty( $available['remove-self'] )));
	}

	/**
	 * Manage forms to be shown according to posted data.
	 * Depending on the submit button used, call a form or a save function.
	 *
	 * @param $par Mixed: string if any subpage provided, else null
	 */
	function execute( $par ) {
		// If the visitor doesn't have permissions to assign or remove
		// any groups, it's a bit silly to give them the user search prompt.
		global $wgUser, $wgRequest;

		if ( !$wgUser->isAllowed( 'batchuserrights' ) ) {
			$this->displayRestrictionError();
			return;
		}

// TODO: REMOVE
print "Extension not ready yet.  Do not enable.";
exit;
// TODO: SWC: MAKE THE FORM POST A TEXTAREA OF NAMES RATHER THAN ONE

// TODO: SWC: MAKE THE CODE READ A POSTED BUNCH OF NAMES RATHER THAN ONE NAME

		if( $par ) {
			$this->mTarget = $par;
		} else {
			$this->mTarget = $wgRequest->getVal( 'user' );
		}

// TODO: SWC: MAKE THE CODE LOOP THROUGH AND APPLY THE NEW USER GROUP TO EACH USER RATHER THAN JUST ONE

// TODO: SWC: SKIP THE STEP WHERE YOU HAVE TO ADD A NAME TO SEE THE CHECKBOXES... JUST USE THE CURRENT USER FOR WHICH BOXES TO SHOW.

		if( !$this->userCanChangeRights( $wgUser, true ) ) {
			// fixme... there may be intermediate groups we can mention.
			global $wgOut;
			$wgOut->showPermissionsErrorPage( array(
				$wgUser->isAnon()
					? 'userrights-nologin'
					: 'userrights-notallowed' ) );
			return;
		}

		// check if user is blocked -- see rt#19111
		if ( $wgUser->isBlocked() ) {
			global $wgOut;
			$wgOut->blockedPage();
			return;
		}	

		if ( wfReadOnly() ) {
			global $wgOut;
			$wgOut->readOnlyPage();
			return;
		}

		$this->outputHeader();

		$this->setHeaders();

		if( $wgRequest->wasPosted() ) {
			// save settings
			if( $wgRequest->getCheck( 'saveusergroups' ) ) {
				$reason = $wgRequest->getVal( 'user-reason' );
				$tok = $wgRequest->getVal( 'wpEditToken' );
				if( $wgUser->matchEditToken( $tok ) ) {
				
				
				
					$this->saveUserGroups(
						$this->mTarget,
						$reason
					);
					
					global $wgOut;
					
					$url = $this->getSuccessURL();
					$wgOut->redirect( $url );
					return;
				}
			}
		}

		// Show the list of avialable rights (for the current user - not for targets since there are many targets).
		$this->editUserGroupsForm( $wgUser->getName() );
	}
	
	function processSingleUser($username){
		if (!$this->mTarget) {
			/*
			 * If the user specified no target, and they can only
			 * edit their own groups, automatically set them as the
			 * target.
			 */
			$available = $this->changeableGroups();
			if (empty($available['add']) && empty($available['remove']))
				$this->mTarget = $wgUser->getName();
		}

		if ($this->mTarget == $wgUser->getName())
			$this->isself = true;
			
			
			
			
			
			
			
			
			
			
			
			
			
			// TODO: IMPLEMENT!!
			// TODO: IMPLEMENT!!
			
			
			
			
			
			
			
	}

	function getSuccessURL() {
		return $this->getTitle( $this->mTarget )->getFullURL();
	}

	/**
	 * Save user groups changes in the database.
	 * Data comes from the editUserGroupsForm() form function
	 *
	 * @param $username String: username to apply changes to.
	 * @param $reason String: reason for group change
	 * @return null
	 */
	function saveUserGroups( $username, $reason = '') {
		global $wgRequest, $wgUser, $wgGroupsAddToSelf, $wgGroupsRemoveFromSelf;

		$user = $this->fetchUser( $username );
		if( !$user ) {
			return;
		}

		$allgroups = $this->getAllGroups();
		$addgroup = array();

		// This could possibly create a highly unlikely race condition if permissions are changed between
		//  when the form is loaded and when the form is saved. Ignoring it for the moment.
		foreach ($allgroups as $group) {
			// This batch form is only for adding user groups, we don't remove any.
			if ($wgRequest->getCheck( "wpGroup-$group" )) {
				$addgroup[] = $group;
			}
		}

		// Validate input set...
		$changeable = $this->changeableGroups();
		$addable = array_merge( $changeable['add'], $this->isself ? $changeable['add-self'] : array() );

		$addgroup = array_unique(
			array_intersect( (array)$addgroup, $addable ) );

		$oldGroups = $user->getGroups();
		$newGroups = $oldGroups;

		if( $addgroup ) {
			$newGroups = array_merge($newGroups, $addgroup);
			foreach( $addgroup as $group ) {
				$user->addGroup( $group );
			}
		}
		$newGroups = array_unique( $newGroups );

		// Ensure that caches are cleared
		$user->invalidateCache();

		wfDebug( 'oldGroups: ' . print_r( $oldGroups, true ) );
		wfDebug( 'newGroups: ' . print_r( $newGroups, true ) );
		if( $user instanceof User ) {
			$removegroup = array();
			// hmmm
			wfRunHooks( 'UserRights', array( &$user, $addgroup, $removegroup ) );
		}

		if( $newGroups != $oldGroups ) {
			$this->addLogEntry( $user, $oldGroups, $newGroups );
		}
	}

	/**
	 * Add a rights log entry for an action.
	 */
	function addLogEntry( $user, $oldGroups, $newGroups ) {
		global $wgRequest;
		$log = new LogPage( 'rights' );

		$log->addEntry( 'rights',
			$user->getUserPage(),
			$wgRequest->getText( 'user-reason' ),
			array(
				$this->makeGroupNameListForLog( $oldGroups ),
				$this->makeGroupNameListForLog( $newGroups )
			)
		);
	}

	/**
	 * Edit user groups membership
	 * @param $username String: name of the user.
	 */
	function editUserGroupsForm( $username ) {
		global $wgOut;

		$user = $this->fetchUser( $username );
		if( !$user ) {
			return;
		}

		$groups = $user->getGroups();

		$this->showEditUserGroupsForm( $user, $groups );

		// This isn't really ideal logging behavior, but let's not hide the
		// interwiki logs if we're using them as is.
		$this->showLogFragment( $user, $wgOut );
	}

	/**
	 * Normalize the input username, which may be local or remote, and
	 * return a user (or proxy) object for manipulating it.
	 *
	 * Side effects: error output for invalid access
	 * @return mixed User, UserRightsProxy, or null
	 */
	function fetchUser( $username ) {
		global $wgOut, $wgUser, $wgUserrightsInterwikiDelimiter;

		$parts = explode( $wgUserrightsInterwikiDelimiter, $username );
		if( count( $parts ) < 2 ) {
			$name = trim( $username );
			$database = '';
		} else {
			list( $name, $database ) = array_map( 'trim', $parts );

			if( !$wgUser->isAllowed( 'userrights-interwiki' ) ) {
				$wgOut->addWikiMsg( 'userrights-no-interwiki' );
				return null;
			}
			if( !UserRightsProxy::validDatabase( $database ) ) {
				$wgOut->addWikiMsg( 'userrights-nodatabase', $database );
				return null;
			}
		}

		if( $name == '' ) {
			$wgOut->addWikiMsg( 'nouserspecified' );
			return false;
		}

		if( $name{0} == '#' ) {
			// Numeric ID can be specified...
			// We'll do a lookup for the name internally.
			$id = intval( substr( $name, 1 ) );

			if( $database == '' ) {
				$name = User::whoIs( $id );
			} else {
				$name = UserRightsProxy::whoIs( $database, $id );
			}

			if( !$name ) {
				$wgOut->addWikiMsg( 'noname' );
				return null;
			}
		}

		if( $database == '' ) {
			$user = User::newFromName( $name );
		} else {
			$user = UserRightsProxy::newFromName( $database, $name );
		}

		if( !$user || $user->isAnon() ) {
			$wgOut->addWikiMsg( 'nosuchusershort', $username );
			return null;
		}

		return $user;
	}

	function makeGroupNameList( $ids ) {
		if( empty( $ids ) ) {
			return wfMsgForContent( 'rightsnone' );
		} else {
			return implode( ', ', $ids );
		}
	}

	function makeGroupNameListForLog( $ids ) {
		if( empty( $ids ) ) {
			return '';
		} else {
			return $this->makeGroupNameList( $ids );
		}
	}

	/**
	 * Go through used and available groups and return the ones that this
	 * form will be able to manipulate based on the current user's system
	 * permissions.
	 *
	 * @param $groups Array: list of groups the given user is in
	 * @return Array:  Tuple of addable, then removable groups
	 */
	protected function splitGroups( $groups ) {
		list($addable, $removable, $addself, $removeself) = array_values( $this->changeableGroups() );

		$removable = array_intersect(
				array_merge( $this->isself ? $removeself : array(), $removable ),
				$groups ); // Can't remove groups the user doesn't have
		$addable   = array_diff(
				array_merge( $this->isself ? $addself : array(), $addable ),
				$groups ); // Can't add groups the user does have

		return array( $addable, $removable );
	}

	/**
	 * Show the form to edit group memberships.
	 *
	 * @param $user      User or UserRightsProxy you're editing
	 * @param $groups    Array:  Array of groups the user is in
	 */
	protected function showEditUserGroupsForm( $user, $groups ) {
		global $wgOut, $wgUser, $wgLang;

		/* Wikia change begin - @author: Marooned */
		/* This hook was invalid in this version of the code and in the current (2010-05-18) MW trunk it even doesn't exist */
		/* We need it to alter displayed list without alter real groups */
		wfRunHooks('UserRights::showEditUserGroupsForm', array( &$user, &$groups ));
		/* Wikia change end */

		$list = array();
		foreach( $groups as $group )
			$list[] = self::buildGroupLink( $group );

		$grouplist = '';
		if( count( $list ) > 0 ) {
			$grouplist = wfMsgHtml( 'userrights-groupsmember' );
			$grouplist = '<p>' . $grouplist  . ' ' . $wgLang->listToText( $list ) . '</p>';
		}
		$wgOut->addHTML(
			Xml::openElement( 'form', array( 'method' => 'post', 'action' => $this->getTitle()->getLocalURL(), 'name' => 'editGroup', 'id' => 'mw-userrights-form2' ) ) .
			Xml::hidden( 'user', $this->mTarget ) .
			Xml::hidden( 'wpEditToken', $wgUser->editToken() ) .
			Xml::openElement( 'fieldset' ) .
			Xml::element( 'legend', array(), wfMsg( 'userrights-editusergroup' ) ) .
			wfMsgExt( 'editinguser', array( 'parse' ), wfEscapeWikiText( $user->getName() ) ) .
			wfMsgExt( 'userrights-groups-help', array( 'parse' ) ) .
			$grouplist .
			Xml::tags( 'p', null, $this->groupCheckboxes( $groups ) ) .
			Xml::openElement( 'table', array( 'border' => '0', 'id' => 'mw-userrights-table-outer' ) ) .
				"<tr>
					<td class='mw-label'>" .
						Xml::label( wfMsg( 'userrights-reason' ), 'wpReason' ) .
					"</td>
					<td class='mw-input'>" .
						Xml::input( 'user-reason', 60, false, array( 'id' => 'wpReason', 'maxlength' => 255 ) ) .
					"</td>
				</tr>
				<tr>
					<td></td>
					<td class='mw-submit'>" .
						Xml::submitButton( wfMsg( 'saveusergroups' ), array( 'name' => 'saveusergroups', 'accesskey' => 's' ) ) .
					"</td>
				</tr>" .
			Xml::closeElement( 'table' ) . "\n" .
			Xml::closeElement( 'fieldset' ) .
			Xml::closeElement( 'form' ) . "\n"
		);
	}

	/**
	 * Format a link to a group description page
	 *
	 * @param $group string
	 * @return string
	 */
	private static function buildGroupLink( $group ) {
		static $cache = array();
		if( !isset( $cache[$group] ) )
			$cache[$group] = User::makeGroupLinkHtml( $group, User::getGroupName( $group ) );
		return $cache[$group];
	}
	
	/**
	 * Returns an array of all groups that may be edited
	 * @return array Array of groups that may be edited.
	 */
	 protected static function getAllGroups() {
	 	return User::getAllGroups();
	 }

	/**
	 * Adds a table with checkboxes where you can select what groups to add/remove
	 *
	 * @param $usergroups Array: groups the user belongs to
	 * @return string XHTML table element with checkboxes
	 */
	private function groupCheckboxes( $usergroups ) {
		$allgroups = $this->getAllGroups();
		$ret = '';

		$column = 1;
		$settable_col = '';
		$unsettable_col = '';

		foreach ($allgroups as $group) {
			$set = in_array( $group, $usergroups );
			# Should the checkbox be disabled?
			$disabled = !(
				( $set && $this->canRemove( $group ) ) ||
				( !$set && $this->canAdd( $group ) ) );
			# Do we need to point out that this action is irreversible?
			$irreversible = !$disabled && (
				($set && !$this->canAdd( $group )) ||
				(!$set && !$this->canRemove( $group ) ) );

			/* Wikia change begin - @author: Marooned */
			/* Because of "return all" in changeableGroups() hook UserrightsChangeableGroups is not invoked - this hook is to fill this gap */
			wfRunHooks('UserRights::groupCheckboxes', array( $group, &$disabled, &$irreversible ));
			/* Wikia change end */

			$attr = $disabled ? array( 'disabled' => 'disabled' ) : array();
			$attr['title'] = $group;
			$text = $irreversible
				? wfMsgHtml( 'userrights-irreversible-marker', User::getGroupMember( $group ) )
				: User::getGroupMember( $group );
			$checkbox = Xml::checkLabel( $text, "wpGroup-$group",
				"wpGroup-$group", $set, $attr );
			$checkbox = $disabled ? Xml::tags( 'span', array( 'class' => 'mw-userrights-disabled' ), $checkbox ) : $checkbox;

			if ($disabled) {
				$unsettable_col .= "$checkbox<br />\n";
			} else {
				$settable_col .= "$checkbox<br />\n";
			}
		}

		if ($column) {
			$ret .=	Xml::openElement( 'table', array( 'border' => '0', 'class' => 'mw-userrights-groups' ) ) .
				"<tr>
";
			if( $settable_col !== '' ) {
				$ret .= xml::element( 'th', null, wfMsg( 'userrights-changeable-col' ) );
			}
			if( $unsettable_col !== '' ) {
				$ret .= xml::element( 'th', null, wfMsg( 'userrights-unchangeable-col' ) );
			}
			$ret.= "</tr>
				<tr>
";
			if( $settable_col !== '' ) {
				$ret .=
"					<td style='vertical-align:top;'>
						$settable_col
					</td>
";
			}
			if( $unsettable_col !== '' ) {
				$ret .=
"					<td style='vertical-align:top;'>
						$unsettable_col
					</td>
";
			}
			$ret .= Xml::closeElement( 'tr' ) . Xml::closeElement( 'table' );
		}

		return $ret;
	}

	/**
	 * @param  $group String: the name of the group to check
	 * @return bool Can we remove the group?
	 */
	private function canRemove( $group ) {
		// $this->changeableGroups()['remove'] doesn't work, of course. Thanks,
		// PHP.
		$groups = $this->changeableGroups();
		return in_array( $group, $groups['remove'] ) || ($this->isself && in_array( $group, $groups['remove-self'] ));
	}

	/**
	 * @param $group string: the name of the group to check
	 * @return bool Can we add the group?
	 */
	private function canAdd( $group ) {
		$groups = $this->changeableGroups();
		return in_array( $group, $groups['add'] ) || ($this->isself && in_array( $group, $groups['add-self'] ));
	}

	/**
	 * Returns an array of the groups that the user can add/remove.
	 *
	 * @return Array array( 'add' => array( addablegroups ), 'remove' => array( removablegroups ) , 'add-self' => array( addablegroups to self), 'remove-self' => array( removable groups from self) )
	 */
	function changeableGroups() {
		global $wgUser;

		if( $wgUser->isAllowed( 'userrights' ) ) {
			// This group gives the right to modify everything (reverse-
			// compatibility with old "userrights lets you change
			// everything")
			// Using array_merge to make the groups reindexed
			$all = array_merge( User::getAllGroups() );
			return array(
				'add' => $all,
				'remove' => $all,
				'add-self' => array(),
				'remove-self' => array()
			);
		}

		// Okay, it's not so simple, we will have to go through the arrays
		$groups = array(
				'add' => array(),
				'remove' => array(),
				'add-self' => array(),
				'remove-self' => array() );
		$addergroups = $wgUser->getEffectiveGroups();

		foreach ($addergroups as $addergroup) {
			$groups = array_merge_recursive(
				$groups, $this->changeableByGroup($addergroup)
			);
			$groups['add']    = array_unique( $groups['add'] );
			$groups['remove'] = array_unique( $groups['remove'] );
			$groups['add-self'] = array_unique( $groups['add-self'] );
			$groups['remove-self'] = array_unique( $groups['remove-self'] );
		}
		
		// Run a hook because we can
		wfRunHooks( 'UserrightsChangeableGroups', array( $this, $wgUser, $addergroups, &$groups ) );
		
		return $groups;
	}

	/**
	 * Returns an array of the groups that a particular group can add/remove.
	 *
	 * @param $group String: the group to check for whether it can add/remove
	 * @return Array array( 'add' => array( addablegroups ), 'remove' => array( removablegroups ) , 'add-self' => array( addablegroups to self), 'remove-self' => array( removable groups from self) )
	 */
	private function changeableByGroup( $group ) {
		global $wgAddGroups, $wgRemoveGroups, $wgGroupsAddToSelf, $wgGroupsRemoveFromSelf;

		$groups = array( 'add' => array(), 'remove' => array(), 'add-self' => array(), 'remove-self' => array() );
		if( empty($wgAddGroups[$group]) ) {
			// Don't add anything to $groups
		} elseif( $wgAddGroups[$group] === true ) {
			// You get everything
			$groups['add'] = User::getAllGroups();
		} elseif( is_array($wgAddGroups[$group]) ) {
			$groups['add'] = $wgAddGroups[$group];
		}

		// Same thing for remove
		if( empty($wgRemoveGroups[$group]) ) {
		} elseif($wgRemoveGroups[$group] === true ) {
			$groups['remove'] = User::getAllGroups();
		} elseif( is_array($wgRemoveGroups[$group]) ) {
			$groups['remove'] = $wgRemoveGroups[$group];
		}
		
		// Re-map numeric keys of AddToSelf/RemoveFromSelf to the 'user' key for backwards compatibility
		if( empty($wgGroupsAddToSelf['user']) || $wgGroupsAddToSelf['user'] !== true ) {
			foreach($wgGroupsAddToSelf as $key => $value) {
				if( is_int($key) ) {
					$wgGroupsAddToSelf['user'][] = $value;
				}
			}
		}
		
		if( empty($wgGroupsRemoveFromSelf['user']) || $wgGroupsRemoveFromSelf['user'] !== true ) {
			foreach($wgGroupsRemoveFromSelf as $key => $value) {
				if( is_int($key) ) {
					$wgGroupsRemoveFromSelf['user'][] = $value;
				}
			}
		}
		
		// Now figure out what groups the user can add to him/herself
		if( empty($wgGroupsAddToSelf[$group]) ) {
		} elseif( $wgGroupsAddToSelf[$group] === true ) {
			// No idea WHY this would be used, but it's there
			$groups['add-self'] = User::getAllGroups();
		} elseif( is_array($wgGroupsAddToSelf[$group]) ) {
			$groups['add-self'] = $wgGroupsAddToSelf[$group];
		}
		
		if( empty($wgGroupsRemoveFromSelf[$group]) ) {
		} elseif( $wgGroupsRemoveFromSelf[$group] === true ) {
			$groups['remove-self'] = User::getAllGroups();
		} elseif( is_array($wgGroupsRemoveFromSelf[$group]) ) {
			$groups['remove-self'] = $wgGroupsRemoveFromSelf[$group];
		}
		
		return $groups;
	}

	/**
	 * Show a rights log fragment for the specified user
	 *
	 * @param $user User to show log for
	 * @param $output OutputPage to use
	 */
	protected function showLogFragment( $user, $output ) {
		$output->addHTML( Xml::element( 'h2', null, LogPage::logName( 'rights' ) . "\n" ) );
		LogEventsList::showLogExtract( $output, 'rights', $user->getUserPage()->getPrefixedText() );
	}
}
