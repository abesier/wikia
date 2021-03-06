<?php

class VimeoVideoHandler extends VideoHandler {
	
	protected $apiName = 'VimeoApiWrapper';
	protected static $urlTemplate = 'http://player.vimeo.com/video/$1';
	protected static $providerDetailUrlTemplate = 'http://vimeo.com/$1';
	protected static $providerHomeUrl = 'http://vimeo.com/';
	protected static $autoplayParam = "autoplay";
	protected static $autoplayValue = "1";

	public function getPlayerAssetUrl() {
		return '';
	}
	
	public function getEmbed( $articleId, $width, $autoplay = false, $isAjax = false, $postOnload=false ) {
		return $this->getEmbedNative( $width, $autoplay );
	}
		
	private function getEmbedNative( $width, $autoplay = false ) {
		$height =  $this->getHeight( $width );
		$autoplayStrParam = self::$autoplayParam;
		$autoplayStrValue = $autoplay ? self::$autoplayValue : '0';
		$url = $this->getEmbedUrl();
		return '<iframe src="'.$url.'?'.$autoplayStrParam.'='.$autoplayStrValue.'" width="'.$width.'" height="'.$height.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
	}

}