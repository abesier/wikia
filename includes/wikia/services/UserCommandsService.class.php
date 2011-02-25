<?php

	class UserCommandsService {
		
		protected $cache = array();
		
		public function __construct( $skinData ) {
			UserCommand::setSkinData($skinData);
		}
		
		public function clearCache() {
			self::$cache = array();
		}
		
		public function get( $name, $options = array() ) {
			$hash = $name . ($options ? serialize($options) : '');
			if (empty($this->cache[$hash])) {
				list($type,$data) = explode(':',$name,2);
				
				$className = false;
				switch ($type) {
					case "SpecialPage":
						$className = "SpecialPageUserCommand";
						break;
					case "PageAction":
						if (in_array($data,array("Share","Follow"))) {
							$className = "{$data}UserCommand";
						} else {
							$className = "PageActionUserCommand";
						}
						break;
					case "Action":
						if (in_array($data,array("CustomizeToolbar"))) {
							$className = "{$data}UserCommand";
						}
						break;
				}
				
				$this->cache[$hash] = $className ? new $className( $name, $options ) : null;
			}
			return $this->cache[$hash];
		}
		
		public function createMenu( $id, $caption, $options = array() ) {
			return new MenuUserCommand( $id, $caption, $options );
		}
		
	}
	
$wgAutoloadClasses['UserCommand'] = dirname(__FILE__) . '/usercommands/UserCommand.php';
$wgAutoloadClasses['PageActionUserCommand'] = dirname(__FILE__) . '/usercommands/PageActionUserCommand.php';
$wgAutoloadClasses['FollowUserCommand'] = dirname(__FILE__) . '/usercommands/FollowUserCommand.php';
$wgAutoloadClasses['ShareUserCommand'] = dirname(__FILE__) . '/usercommands/ShareUserCommand.php';
$wgAutoloadClasses['SpecialPageUserCommand'] = dirname(__FILE__) . '/usercommands/SpecialPageUserCommand.php';
$wgAutoloadClasses['CustomizeToolbarUserCommand'] = dirname(__FILE__) . '/usercommands/CustomizeToolbarUserCommand.php';
$wgAutoloadClasses['MenuUserCommand'] = dirname(__FILE__) . '/usercommands/MenuUserCommand.php';
