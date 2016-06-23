<?php
namespace Fulcrum\Tests\Custom\Shortcode;

use Fulcrum\Custom\Shortcode\Config_Validator;
use Fulcrum\Custom\Shortcode\Shortcode;
use WP_UnitTestCase;
use Fulcrum\Tests\Mocks\Empty_Config;
use Fulcrum\Config\Factory;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

class Shortcode_Test extends WP_UnitTestCase {

	protected $config;
	protected $defaults;
	protected $validator;

	function setUp() {
		parent::setUp();

		$this->config   = include( __DIR__ . '/config.php' );
		$this->defaults = include( FULCRUM_PLUGIN_DIR . 'src/custom/shortcode/config/defaults.php' );

		$this->validator = new Config_Validator();
	}

	function tearDown() {
		parent::tearDown();
	}

	// Validator has the remaining tests.
	function test_exception_thrown_for_no_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'Invalid config for shortcode.' );

		new Shortcode( new Empty_Config, $this->validator );
	}


	function test_rendering() {
		$config = Factory::create( $this->config['config'], $this->defaults );
		$foo = new Shortcode( $config, $this->validator );;

		$this->assertEquals( '<p class="bar"></p>', $foo->render_callback( array( 'class' => 'bar' ) ) );
		$this->assertEquals( '<p class="bar">Some content</p>', $foo->render_callback( array( 'class' => 'bar' ), 'Some content' ) );
		$this->assertEquals( '<p class="some-class"></p>', $foo->render_callback( '' ) );
	}

}