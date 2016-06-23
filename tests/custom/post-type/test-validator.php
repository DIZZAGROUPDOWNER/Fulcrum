<?php
namespace Fulcrum\Tests\Custom\Post_Type;

use Fulcrum\Config\Factory;
use Fulcrum\Custom\Post_Type\Validator;
use WP_UnitTestCase;
use Fulcrum\Tests\Mocks\Empty_Config;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

class Config_Validator_Test extends WP_UnitTestCase {

	protected $config;
	protected $defaults;
	protected $validator;

	function setUp() {
		parent::setUp();

		$this->config   = include( __DIR__ . '/config.php' );
		$this->defaults = include( FULCRUM_PLUGIN_DIR . 'src/custom/post-type/config/defaults.php' );

		$this->validator = new Validator();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_exception_thrown_for_no_post_type() {
		$this->setExpectedException( 'InvalidArgumentException', 'When declaring a custom post type, the post type cannot be empty.' );

		$this->validator->is_valid( new Empty_Config, '' );
	}

	function test_exception_thrown_for_no_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Post Type Configuration, the config for [foo] cannot be empty.' );

		$this->validator->is_valid( new Empty_Config, 'foo' );
	}

	function test_for_is_valid() {
		$config = Factory::create( $this->config, $this->defaults );

		$actual = $this->validator->is_valid( $config, 'foo' );

		$this->assertTrue( $actual );

	}
}