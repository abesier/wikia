<?php

/**
 * MenuButton with facebook styling
 *
 * @author macbre
 */
class FacebookButtonController extends WikiaController {

	public function index() {
		$this->class = $this->request->getVal('class', '');
		$this->text = $this->request->getVal('text', '');
		$this->tooltip = $this->request->getVal('tooltip');

		if ( Wikia::isWikiaMobile() ) {
			$this->response->setVal( 'context', $this->request->getVal( 'context' ) );
			$this->overrideTemplate( 'WikiaMobileIndex' );
		}
	}
}