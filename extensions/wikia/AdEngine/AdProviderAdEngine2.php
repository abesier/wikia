<?php

class AdProviderAdEngine2 extends AdProviderIframeFiller implements iAdProvider {

	public $enable_lazyload = true;
	private $isMainPage, $useIframe = false;

	protected static $instance = false;

	protected function __construct() {
		$this->isMainPage = ArticleAdLogic::isMainPage();
	}

	public static function getInstance() {
		if(self::$instance == false) {
			self::$instance = new AdProviderAdEngine2();
		}
		return self::$instance;
	}

	private $slotsToCall = array();
	public function addSlotToCall($slotname) {
		$this->slotsToCall[]=$slotname;
	}

  public function batchCallAllowed(){ return false; }
  public function getSetupHtml() { return false; }
  public function getBatchCallHtml(){ return false; }

	public function getAd($slotname, $slot, $params = null) {
		wfProfileIn(__METHOD__);

		$out = '';
		$out .= '<div id="' . htmlspecialchars($slotname) . '" class="wikia-ad noprint default-height">';
		$out .= '<script type="text/javascript">';

		$out .= 'if (!window.adslots2) { window.adslots2 = []; }';
		$out .= 'window.adslots2.push(["' . $slotname . '", "' . $slot['size'] . '", "AdEngine2", "' . $slot['load_priority'] . '"]);';

		$out .= '</script>';
		$out .= '</div>';
		
		wfProfileOut(__METHOD__);
		
		return $out;
	}
	
	protected function getIframeFillFunctionDefinition($function_name, $slotname, $slot) { return ''; }
}