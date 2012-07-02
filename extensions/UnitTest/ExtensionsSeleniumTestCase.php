<?php
/**
 * Wikimedia Foundation
 *
 * LICENSE
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * @author Jeremy Postlethwaite <jpostlethwaite@wikimedia.org>
 */

/**
 * ExtensionsSeleniumTestCase
 *
 * Abstract Selenium testing class
 *
 * Extend this class for any extension.
 */
abstract class ExtensionsSeleniumTestCase extends PHPUnit_Extensions_SeleniumTestCase
{

	/**
	 * Options loaded from the configuration file
	 *
	 * @param array $_options
	 */
	protected static $_options = array();

	/**
	 * Capture a screenshot on test failure. Screenshots may take a picture of
	 * your desktop and not just your browser.
	 *
	 * @param boolean $captureScreenshotOnFailure
	 */
	protected $captureScreenshotOnFailure = true;

	/**
	 * Capture screenshots for a slideshow
	 *
	 * @param boolean $captureScreenshotsForSlideshow
	 */
	protected $captureScreenshotsForSlideshow = true;

	/**
	 * Capture screenshots for a slideshow
	 *
	 * @param boolean $captureScreenshotsAfterAssertion
	 */
	protected $captureScreenshotsAfterAssertion = true;

	/**
	 * The extension name
	 *
	 * @param string $extensionName
	 */
	protected $extensionName = '';

	/**
	 * The default url to the mediawiki virtual host
	 *
	 * @param string $extensionsBrowsersUrl
	 */
	protected $extensionsBrowsersUrl = 'http://localhost/';

	/**
	 * The hostname for the mediawiki virtual host
	 *
	 * @param string $extensionsHostname
	 */
	protected $extensionsHostname = 'localhost';

	/**
	 * The extension test case stamp is based on the date and time. This is used
	 *
	 * @param string $extensionsTestCaseStamp
	 */
	protected $extensionsTestCaseStamp = '';

	/**
	 * The directory to save Selenese generated by Selenium IDE which you are
	 * able to run from Firefox. This is an absolute path like:
	 *
	 * /www/sites/localhost/wikimedia-commit.localhost.wikimedia.org/extensions/CentralNotice/tests/selenium/selenese
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param string $extensionsSeleneseDirectory
	 */
	protected $extensionsSeleneseDirectory = '';

	/**
	 * The Selenese files to load for testing
	 *
	 * @param array $seleneseFiles
	 */
	protected $seleneseFiles = array();

	/**
	 * This will be true if Selenese files have been loaded.
	 *
	 * @param boolean $hasSelenese
	 */
	protected $hasSelenese = false;

	/**
	 * If this is true, then selenese tests that have already been converted
	 * from scripts into test methods.
	 *
	 * @param boolean $runConvertedTests
	 */
	protected $runConvertedTests = true;

	/**
	 * If set to true, selenese files will be loaded.
	 *
	 * @param boolean $loadSeleneseFiles
	 */
	protected $loadSeleneseFiles = true;

	/**
	 * The selenese extensions permitted for testing.
	 * Currently, we only use htm.
	 *
	 * @param array $selenesExtensions
	 */
	protected $selenesExtensions = array(
		'.htm',
	);

	/**
	 * The screenshot count. Images captured with @see $this->browserScreenshot
	 * will be placed in the screenshot directory @see $this->screenshotPath.
	 * Each image will be a png and will the file will be prefixed with this
	 * variable. This will make it easier to generate slideshows for test
	 * replaying.
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param integer $screenshotCount
	 */
	protected $screenshotCount = 0;

	/**
	 * The directory to save screenshots for your extension. This is an absolute
	 * path like:
	 *
	 * /www/sites/localhost/wikimedia-commit.localhost.wikimedia.org/extensions/CentralNotice/tests/selenium/screenshots
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param string $screenshotPath
	 */
	protected $screenshotPath = '';

	/**
	 * The url to the directory where your screenshots are saved for your
	 * extension. This is a URL like:
	 *
	 * http://wikimedia-commit.localhost.wikimedia.org/extensions/CentralNotice/tests/selenium/screenshots/
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param string $screenshotUrl
	 */
	protected $screenshotUrl = '';

	/**
	 * The test case methods
	 *
	 * @param array $testCaseMethods
	 */
	protected $testCaseMethods = array();

	/**
	 * The base path for the extension tests. This is an absolute path like:
	 *
	 * /www/sites/localhost/wikimedia-commit.localhost.wikimedia.org/extensions/CentralNotice/tests
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param string $unitTestBasePath
	 */
	protected $unitTestBasePath = '';

	/**
	 * The webroot for the site. This should correspond to $IP.
	 *
	 * /www/sites/localhost/wikimedia-commit.localhost.wikimedia.org/
	 *
	 * This will be populated in class instantiation.
	 *
	 * @param string $webroot
	 */
	protected $webroot = '';

	/**
	 * Test case instantiation
	 */

	/**
	 * @param  string $name
	 * @param  array  $data
	 * @param  string $dataName
	 * @param  array  $browser
	 * @throws InvalidArgumentException
	 */
	final public function __construct( $name = NULL, array $data = array(), $dataName = '', array $browser = array() )
	{
		parent::__construct( $name, $data, $dataName, $browser );

		$this->init();

		$this->loadSeleneseFiles();
		// $this->verifySetup();
		// SpecialCentralNoticeTestCase::$seleneseDirectory = $this->unitTestBasePath . '/selenium/selenese';
	}

	/**
	 * Initialize the Test case settings.
	 * Set up paths for testing
	 */
	final protected function init()
	{
		$this->setUnitTestBasePath();

		$this->setExtensionName();
		
		$this->extensionsTestCaseStamp = date( 'Y-m-d-Hi-s' );
		// Debug::dump($this->extensionsTestCaseStamp, eval(DUMP) . '\$this->extensionsTestCaseStamp', false);

		if ( empty( $this->unitTestBasePath ) ) {
			$message = 'You must specify a path for $this->unitTestBasePath by properly implementing $this->setUnitTestBasePath()';
			throw new Exception( $message );
		}

		$this->loadConfiguration();
	}

	/**
	 * getExtensionName
	 *
	 * @see getExtensionName
	 */
	public function getExtensionName()
	{
		return $this->extensionName;
	}

	/**
	 * setExtensionName
	 * 
	 * Set the extension name
	 *
	 * @see $extensionName
	 */
	protected function setExtensionName()
	{
		$this->extensionName = basename( dirname( $this->unitTestBasePath ) );
	}
	
	/**
	 * Load configuration file
	 */
	public function loadConfiguration()
	{
		// Check for customized configuration file
		$file = $this->unitTestBasePath . '/selenium.ini';

		//Debug::puke( $file, eval(DUMP) . "\$file" );

		if ( !is_file( $file ) ) {
			// Check for distributed configuration file
			$file .= '.dst';

			// Debug::dump($file, eval(DUMP) . '\$file', false);

			if ( !is_file( $file ) ) {
				// Check for distributed configuration file
				$file = false;
			}
		}

		//Debug::puke( $file, eval(DUMP) . "\$file" );

		// Load a configuration file if it exists.
		if ( $file ) {

			$options = parse_ini_file( $file, true );

			$this->setOptions( $options );
		}
	}

	/**
	 * Check to see if we should load a selenese files for testing.
	 *
	 * @param string $file	The name of the file will correspond to a test
	 * method:
	 * test-centralnotice-special-banner.htm
	 * ->
	 * testCentralnoticeSpecialBanner()
	 */
	public function loadSeleneseFile( $file )
	{
		$return = true;

		$label = basename( $file, '.htm' );
		// Debug::dump($label, eval(DUMP) . '\$label', false);
		// $method = ( strtolower( substr( $str, 0, 1 ) ) ) . substr( $str, 1 ) );
		$method = lcfirst( str_replace( ' ', '', ucwords( str_replace( '-', ' ', $label ) ) ) );
		// Debug::dump($method, eval(DUMP) . '\$method', false);

		// Check to see if we are running converted tests.
		if ( in_array( $method, $this->testCaseMethods ) ) {

			// Debug::dump($this->runConvertedTests, eval(DUMP) . '\$this->runConvertedTests', false);
			$return = $this->runConvertedTests;
		}

		return $return;
	}

	/**
	 * Load selenese files for testing.
	 */
	public function loadSeleneseFiles()
	{
		$this->testCaseMethods = get_class_methods( $this );

		// Make sure the directory has been set
		if ( empty( $this->extensionsSeleneseDirectory )) {
			$message = '$this->extensionsSeleneseDirectory cannot be empty.';
			throw new Exception( $message );
		}

		// Make sure the directory is valid
		if ( !( $this->extensionsSeleneseDirectory )) {
			$message = '$this->extensionsSeleneseDirectory cannot be empty.';
			throw new Exception( $message );
		}
		
		//Debug::puke( $this->extensionsSeleneseDirectory, eval(DUMP) . "\$this->extensionsSeleneseDirectory" );
		$files = scandir( $this->extensionsSeleneseDirectory );
		// Debug::dump($this->extensionsSeleneseDirectory, eval(DUMP) . '\$this->extensionsSeleneseDirectory', false);
		//Debug::puke( $files, eval(DUMP) . '\$files' );

		foreach ( $files as $file ) {

			// Check the extension of the file to see if it is an htm file.
			$extension = substr( $file, -4 );

			if ( in_array( $extension, $this->selenesExtensions ) ) {

				if ( $this->loadSeleneseFile( $file ) ) {

					$this->seleneseFiles[] = $file;
				}
			}
		}
		//Debug::puke( $this->seleneseFiles, eval(DUMP) . "\$this->seleneseFiles" );

		$this->hasSelenese = count( $this->seleneseFiles );
		// Debug::dump($this->hasSelenese, eval(DUMP) . '\$this->hasSelenese', true);
	}

	/**
	 * Set options for this test case.
	 */
	public function setOptions( $options = array() )
	{
		self::$_options = $options;

		$this->setExtensionsHost();

		// If screenshots are enabled, this will create a new directory.
		$this->setScreenshotPath();

		// If screenshots are enabled, this will create a new directory.
		$this->setSelenesePath();

		// Debug::dump($this, eval(DUMP) . '\$this', true);
		// Debug::dump($this->screenshotPath, eval(DUMP) . '\$this->screenshotPath', true);
	}

	/**
	 * Set available browsers for this unit test.
	 * Set the default browser for this unit test.
	 * 
	 * @deprecated
	 */
	protected function setExtensionsBrowsers()
	{
		
		$message = __FUNCTION__ . ' is disabled. Set browsers in phpunit.xml';
		throw new Exception( $message );
		
		// See if any browsers have been specified in the configuration file.
		if ( !isset( self::$_options['browsers'] ) ) {
			$message = 'You must specify at least one browser to run Selenium tests.';
			throw new Exception( $message );
		}

		/**
		 * Check to see if any browsers are enabled.
		 * If they are, add them to @see $this->extensionsBrowsers
		 */
		foreach ( self::$_options['browsers'] as $browser => $meta ) {

			$enable = isset( $meta['enable'] ) ? (boolean) $meta['enable'] : false;

			if ( $enable ) {
				$this->extensionsBrowsers[ $browser ] = $meta;
			}
		}

		// If no browsers are enabled, terminate the test.
		if ( empty( $this->extensionsBrowsers ) ) {
			$message = 'You must specify at least one browser to run Selenium tests. Please make sure the browser has been enabled in the configuration file.';
			throw new Exception( $message );
		}

		// Set the default browser
		// Debug::dump($this->extensionsBrowsersDefault, eval(DUMP) . '\$this->extensionsBrowsersDefault', false);
		$this->extensionsBrowsersDefault = isset( self::$_options['selenium']['browser'] ) ? self::$_options['selenium']['browser'] : $this->extensionsBrowsersDefault;
		// Debug::dump($this->extensionsBrowsersDefault, eval(DUMP) . '\$this->extensionsBrowsersDefault', false);

		/**
		 * If the default browser does not exist in the available browsers,
		 * choose the first enabled browser.
		 */
		if ( !isset( $this->extensionsBrowsers[ $this->extensionsBrowsersDefault ] ) ) {
			$this->extensionsBrowsersDefault = key( $this->extensionsBrowsers );
		}

		if ( !isset( $this->extensionsBrowsers[ $this->extensionsBrowsersDefault ]['application'] ) ) {
			$message = 'The browser application';
			throw new Exception( $message );
		}

		$this->extensionsBrowsersDefaultApplication = $this->extensionsBrowsers[ $this->extensionsBrowsersDefault ]['application'];

		// Debug::dump($this->extensionsBrowsersDefault, eval(DUMP) . '\$this->extensionsBrowsersDefault', false);
		// Debug::dump($this->extensionsBrowsers, eval(DUMP) . '\$this->extensionsBrowsers', true);
	}

	/**
	 * Set the hostname for this unit test.
	 * Set the browser Url for this unit test.
	 */
	protected function setExtensionsHost()
	{
		// See if there is a host section set in the configuration
		if ( isset( self::$_options['host'] ) ) {
			$this->extensionsHostname		= isset( self::$_options['host']['hostname'] )		? self::$_options['host']['hostname'] : $this->extensionsHostname;
			$this->extensionsBrowsersUrl	= isset( self::$_options['host']['browserUrl'] )	? self::$_options['host']['browserUrl']	 : $this->extensionsBrowsersUrl;
		}

		//Debug::dump( self::$_options, eval(DUMP) . "self::\$_options" );

		// Make sure there is a trailing slash on the url
		$this->extensionsBrowsersUrl = rtrim( $this->extensionsBrowsersUrl, '/' ) . '/';
	}

	/**
	 * Get the browser Url for this unit test.
	 */
	protected function getExtensionsBrowsersUrl()
	{
		return $this->extensionsBrowsersUrl;
	}

	/**
	 * Set screenshot path
	 * Set screenshots for after assertion option.
	 * Set screenshots for slideshow option.
	 */
	protected function setScreenshotPath()
	{
		// See if there is a selenium section set in the configuration
		if ( isset( self::$_options['selenium'] ) ) {

			// Set screenshots for slideshow option
			$this->captureScreenshotsForSlideshow = isset( self::$_options['selenium']['screenshots'] ) ? self::$_options['selenium']['screenshots'] : $this->captureScreenshotsForSlideshow;
			$this->captureScreenshotsAfterAssertion = isset( self::$_options['selenium']['screenshotAfterAssertion'] ) ? self::$_options['selenium']['screenshotAfterAssertion'] : $this->captureScreenshotsForSlideshow;
		}

		if ( $this->captureScreenshotsForSlideshow ) {

			// Set screenshot path
			$this->screenshotPath = $this->unitTestBasePath . DS . $this->getExtensionName() . '/selenium/screenshots';

			if ( !is_dir( $this->screenshotPath ) ) {
				$message = 'Please create the directory for screenshots, it does not exist: ' . $this->screenshotPath;
				throw new Exception( $message );
			}

			if ( !is_writable( $this->screenshotPath ) ) {
				$message = 'Please make sure the directory for screenshots is writable: ' . $this->screenshotPath;
				throw new Exception( $message );
			}

			// Make sure there is a trailing slash on the path
			$this->screenshotPath = rtrim( $this->screenshotPath, '/' ) . '/';

			/**
			 * Make sure that @see $this->extensionsTestCaseStamp has been set.
			 */
			if ( empty( $this->extensionsTestCaseStamp ) ) {
				$message = 'Unable to create a screenshot directory, the extensionsTestCaseStamp has not been set.';
				throw new Exception( $message );
			}

			$this->screenshotPath .= $this->extensionsTestCaseStamp;
			// Debug::dump($this->screenshotPath, eval(DUMP) . '\$this->screenshotPath', false);

			if ( !is_dir( $this->screenshotPath ) ) {
				if ( !mkdir( $this->screenshotPath ) ) {
					$message = 'Please make the directory for screenshots is writable: ' . $this->screenshotPath;
					throw new Exception( $message );
				}
			}

			$pathToTemplate = dirname( __FILE__ ) . '/ExtensionsSeleniumSlideshowTemplate.php';
			if ( !copy( $pathToTemplate, $this->screenshotPath . '/index.php' ) ) {
				$message = 'Unable to copy slideshow template to: ' . $this->screenshotPath;
				throw new Exception( $message );
			}

			// Begin building relative web root for path to slideshow.
			$relativeWebroot = dirname( __FILE__ );
			//www/github/Mediawiki-Extensions-UnitTest/tests/UnitTest/selenium/screenshots/2012-01-22-1129-18
			//Debug::dump( $relativeWebroot, eval(DUMP) . "\$relativeWebroot" );
			if ( strpos( $this->screenshotPath, $relativeWebroot ) === 0 ) {
				// Debug::dump($this->screenshotUrl, eval(DUMP) . '\$this->screenshotUrl', false);

				$relativeWebroot = substr( $this->screenshotPath, strlen( $relativeWebroot ) + 1 );
				//Debug::dump( $relativeWebroot, eval(DUMP) . "\$relativeWebroot" );
				
				$relativeWebroot = 'extensions' . DS . $this->getExtensionName() . DS . $relativeWebroot;
				
				$this->screenshotUrl = $this->extensionsBrowsersUrl . $relativeWebroot;
				// Debug::dump($this->screenshotUrl, eval(DUMP) . '\$this->screenshotUrl', false);
			}
			//Debug::dump( $this->screenshotUrl, eval(DUMP) . "\$this->screenshotUrl" );
			//Debug::puke( $this->screenshotPath, eval(DUMP) . "\$this->screenshotPath" );

			$message = "\n" . 'Go to slide show: ' . $this->screenshotUrl . "\n";
			echo( $message );
		}
		else {

			// Disable screenshots on failures
			$this->captureScreenshotOnFailure = false;
		}
	}

	/**
	 * Set selenese path
	 */
	protected function setSelenesePath()
	{
		// See if there is a selenium section set in the configuration
		if ( isset( self::$_options['selenium'] ) ) {

			// Set screenshots for slideshow option
			$this->loadSeleneseFiles = isset( self::$_options['selenium']['selenese'] ) ? self::$_options['selenium']['selenese'] : $this->loadSeleneseFiles;
		}

		if ( $this->loadSeleneseFiles ) {

			// Set selenese path
			$this->extensionsSeleneseDirectory = $this->unitTestBasePath . DS . $this->getExtensionName() . '/selenium/selenese';

			if ( !is_dir( $this->extensionsSeleneseDirectory ) ) {
				$message = 'Please create the directory for selenese, it does not exist: ' . $this->extensionsSeleneseDirectory;
				throw new Exception( $message );
			}

			// Make sure there is a trailing slash on the path
			$this->extensionsSeleneseDirectory = rtrim( $this->extensionsSeleneseDirectory, '/' ) . '/';

			// Debug::dump($this->extensionsSeleneseDirectory, eval(DUMP) . '\$this->extensionsSeleneseDirectory', false);
		}
	}

	/**
	 * Set unitTestBasePath
	 *
	 * Implement this prototype with the following line:
	 *
	 * @example $this->unitTestBasePath = realpath(dirname(dirname(__FILE__)));
	 *
	 */
	abstract protected function setUnitTestBasePath();

	/**
	 * Test case setup
	 *
	 * Only one browser may be tested at a time.
	 */
	protected function setUp()
	{
		// Debug::dump($this->extensionsBrowsersUrl, eval(DUMP) . '\$this->extensionsBrowsersUrl', true);

		// Set the base url.
		$this->setBrowserUrl( $this->extensionsBrowsersUrl );

	}

	/**
	 * Run Selenese files if they have been loaded.
	 *
	 */
	protected function runSeleneseFiles()
	{
		if ( !$this->loadSeleneseFiles ) {
			
			// Running selenese files is disabled.
			return;
		}
		
		foreach ( $this->seleneseFiles as $seleneseFile ) {

			$this->runSelenese( $this->extensionsSeleneseDirectory . $seleneseFile );
		}
	}

	/**
	 * Browser functions
	 */

	/**
	 * Browser logout
	 */
	public function browserLogout() {
		$this->open( '/index.php?title=Special:UserLogout' );
	}

	/**
	 * Browser screen shot
	 *
	 * @param string  $function	  The function name in which the screenshot issued.
	 */
	public function browserScreenshot( $function ) {

		if ( !$this->captureScreenshotsForSlideshow ) {
			// Screenshots for slideshows has been disabled.
			return;
		}

		// Increment the screenshot count
		$this->screenshotCount++;

		$file = $this->screenshotPath . '/' . sprintf( "%05d", $this->screenshotCount ) . '_' . $function . '.png';

		$directory = realpath( dirname( $file ) );

		// Verify the directory exists for a screenshot
		if ( !is_writable( $directory ) ) {
			$message = 'The directory is not writable for screenshots: ' . $directory;
			throw new Exception( $message );
		}

		$this->captureEntirePageScreenshot( $file );

	}

	/**
	 * Get the take screenshot after assertion flag
	 *
	 * @param boolean $capture	Set to true to take a screenshot after assertion.
	 */
	protected function setCaptureScreenshotsAfterAssertion( $capture = true )
	{
		$this->captureScreenshotsAfterAssertion = (boolean) $capture;
	}

	/**
	 * Get the take screenshot after assertion flag
	 *
	 * @return boolean
	 */
	protected function getCaptureScreenshotsAfterAssertion()
	{
		return $this->captureScreenshotsAfterAssertion;
	}

	/**
	 * Template Method that is called after Selenium actions.
	 *
	 * @param  string $action
	 * @since  Method available since Release 3.1.0
	 */
	protected function defaultAssertions( $action )
	{

		// By default, screenshots will be made after each assertion.
		if ( $this->getCaptureScreenshotsAfterAssertion() ) {
			$this->browserScreenshot( __FUNCTION__ );
		}
	}
	
	/**
	 * getData
	 *
	 * This is used to generate posted form data
	 *
	 * Everything should be returned as strings, in the array, since that is how
	 * they will be sent by the form.
	 *
	 * Anything set in $data will be converted to a string.
	 *
	 * If a value is null, in $data, it will be removed from $return.
	 *
	 * @param array $return	This is a reference to return.
	 * @param array $data	Anything set in this array will be returned.
	 *
	 * @return array
	 */
	public function getData( &$return, $data = array() ) {
		
		if ( is_array( $data ) ) {

			foreach( $data as $key => $value ) {
				
				// Remove values from return if $value is null.
				if ( is_null( $value ) ) {
				
					if ( isset( $return[ $key ] ) ) {
						
						unset( $return[ $key ] );
					}
				}
				else {
					$return[ $key ] = (string) $value;
				}
			}
		}
	}
}