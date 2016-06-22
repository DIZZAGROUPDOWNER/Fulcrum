<?php
namespace Fulcrum\Tests\Config;

use WP_UnitTestCase;
use Fulcrum\Config\Factory;

class Config__Factory_Test extends WP_UnitTestCase {

	protected $config_filename;
	protected $default_config_filename;

	function setUp() {
		parent::setUp();

		$this->config_filename         = FULCRUM_CONFIGS_DIR . 'foo.php';
		$this->default_config_filename = FULCRUM_CONFIGS_DIR . 'foo-defaults.php';
	}

	function test_exception_thrown_for_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'A config filename must not be empty.' );

		Factory::create( '' );
	}

	function test_exception_thrown_for_invalid_config_file() {
		$config_file_path_name  = 'foo.php';
		$this->setExpectedException( 'RuntimeException', sprintf( '%s %s', __( 'The specified config file is not readable', 'fulcrum' ), $config_file_path_name ) );

		Factory::create( $config_file_path_name );
	}

	function test_config_created() {
		$config = Factory::create( $this->config_filename );

		$this->assertInstanceOf( 'Fulcrum\Config\Config', $config );
	}

	function test_has_key() {
		$config = Factory::create( $this->config_filename );

		$this->assertTrue( $config->has( 'foo' ) );
		$this->assertTrue( $config->has( 'who' ) );
		$this->assertFalse( $config->has( 'bar' ) );
		$this->assertTrue( $config->has( 'foo.site' ) );
		$this->assertTrue( $config->has( 'foo.platform' ) );
		$this->assertFalse( $config->has( 'foo.baz' ) );
	}
}