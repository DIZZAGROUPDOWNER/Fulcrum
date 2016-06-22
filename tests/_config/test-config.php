<?php
namespace Fulcrum\Tests\Config;

use Fulcrum\Config\Config_Contract;
use WP_UnitTestCase;
use Fulcrum\Config\Config;
use Fulcrum\Config\Factory;

class Config_Test extends WP_UnitTestCase {

	protected $config_filename;
	protected $default_config_filename;

	function setUp() {
		parent::setUp();

		$this->config_filename   = FULCRUM_CONFIGS_DIR . 'foo.php';
		$this->default_config_filename = FULCRUM_CONFIGS_DIR . 'foo-defaults.php';
	}

	function test_is_file_valid() {
		$config = new Config( $this->config_filename );
		$this->assertTrue( $config->is_file_valid( FULCRUM_CONFIGS_DIR . 'foo.php' ) );

		$this->setExpectedException( 'InvalidArgumentException', 'A config filename must not be empty.' );
		$config->is_file_valid( '' );

		$config_file_path_name  = FULCRUM_CONFIGS_DIR . 'file-does-not-exist.php';
		$this->setExpectedException( 'RuntimeException', sprintf( '%s %s', __( 'The specified config file is not readable', 'fulcrum' ), $config_file_path_name ) );
		$config->is_file_valid( $config_file_path_name );
	}

	function test_exception_thrown_for_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'A config filename must not be empty.' );

		new Config( '' );
	}

	function test_exception_thrown_for_invalid_config_file() {
		$config_file_path_name  = 'foo.php';
		$this->setExpectedException( 'RuntimeException', sprintf( '%s %s', __( 'The specified config file is not readable', 'fulcrum' ), $config_file_path_name ) );

		new Config( $config_file_path_name );
	}

	function test_has_key() {
		$config = new Config( $this->config_filename );

		$this->assertTrue( $config->has( 'foo' ) );
		$this->assertTrue( $config->has( 'who' ) );
		$this->assertFalse( $config->has( 'bar' ) );
		$this->assertTrue( $config->has( 'foo.site' ) );
		$this->assertTrue( $config->has( 'foo.platform' ) );
		$this->assertFalse( $config->has( 'foo.baz' ) );
	}

	function test_is_array() {
		$config = new Config( $this->config_filename );

		$this->assertTrue( $config->is_array( 'foo' ) );
		$this->assertFalse( $config->is_array( 'foo.theme' ) );
	}

	function test_properties() {
		$config = new Config( $this->config_filename );

		$this->lets_test_the_config_properties( $config );
	}

	function test_get() {
		$config = new Config( $this->config_filename );

		$this->assertEquals( 'knowthecode', $config->get( 'foo.site' ) );
		$this->assertEquals( 'hellofromTonya', $config->get( 'who' ) );

		$this->assertNull( $config->get( 'bar' ) );
		$this->assertNull( $config->get( '' ) );

		$this->setExpectedException( 'PHPUnit_Framework_Error_Notice', 'Undefined index: bar' );
		$this->assertNull( $config->bar );
	}

	function test_all() {
		$expected = include( $this->config_filename );
		$config = new Config( $this->config_filename );
		
		$this->assertTrue( is_array( $config->all() ) );
		$this->assertEquals( $expected, $config->all() );
	}

	function test_set() {
		$config = new Config( $this->config_filename );

		$config->who = 'John';
		$this->assertEquals( 'John', $config->who );

		$config->foo = 'Software development is fun!';
		$this->assertEquals( 'Software development is fun!', $config->foo );

		$config->offsetSet( 'bar', 'Genesis rocks!' );
		$this->assertEquals( 'Genesis rocks!', $config->bar );

		$config->push( 'foobar', 'Lemon water is awesome' );
		$this->assertEquals( 'Lemon water is awesome', $config->foobar );

		$config->set( 'baz', 'Know the fundamentals first' );
		$this->assertEquals( 'Know the fundamentals first', $config->baz );
	}

	function test_has_after_set() {
		$config = new Config( $this->config_filename );

		$config->foo = 'Software development is fun!';
		$this->assertEquals( 'Software development is fun!', $config->foo );
		$this->assertTrue( $config->has( 'foo' ) );

		$config->push( 'bar', 'Lemon water is awesome' );
		$this->assertEquals( 'Lemon water is awesome', $config->bar );
		$this->assertTrue( $config->has( 'bar' ) );

		// Dynamically adding a new property does not add it to
		// the config property.  Therefore, `has` will not find it.
		$config->foobar = 'New property';
		$this->assertEquals( 'New property', $config->foobar );
		$this->assertFalse( $config->has( 'foobar' ) );
	}

	function test_defaults() {
		$config = new Config( $this->config_filename, $this->default_config_filename );

		$this->lets_test_the_config_properties( $config, true );
	}

	function test_loading_array_directly() {
		$foo = include( $this->config_filename );
		$defaults = include( $this->default_config_filename );

		$config = new Config( $foo );
		$this->lets_test_the_config_properties( $config );

		$config = new Config( $foo, $this->default_config_filename );
		$this->lets_test_the_config_properties( $config );

		$config = new Config( $foo, $defaults );
		$this->lets_test_the_config_properties( $config, true );

		$config = new Config( $this->config_filename, $defaults );
		$this->lets_test_the_config_properties( $config, true );
	}

	function test_merge() {
		$config = new Config( $this->config_filename );
		$new_array = array(
			'baz'           => array(
				'barbar'    => 1,
			),
			'foo'           => array(
				'num_posts' => 10,
				'meta'      => 'foobar',
				'platform'  => 'js',
			),
		);

		$config->merge( $new_array );

		$this->assertTrue( $config->has( 'baz.barbar' ) );
		$this->assertEquals( 1, $config->baz['barbar'] );
		$this->assertEquals( 10, $config->foo['num_posts'] );
		$this->assertEquals( 'js', $config->foo['platform'] );
		$this->assertEquals( 'genesis', $config->foo['theme'] );
		$this->assertEquals( 'hellofromTonya', $config->who );

		$config = new Config( $this->config_filename );
		$new_array = array(
			'foo'           => array(
				'platform'  => array(
					'js', 'wordpress', 'php', 'css',
				),
			),
		);
		$config->merge( $new_array );
		$this->assertTrue( $config->has( 'foo.platform' ) );
		$this->assertTrue( $config->is_array( 'foo.platform' ) );
		$this->assertEquals( 'js', $config->foo['platform'][0] );
		$this->assertEquals( 'wordpress', $config->foo['platform'][1] );
		$this->assertEquals( array(
			'js', 'wordpress', 'php', 'css',
		), $config->foo['platform'] );
	}

	/*********************
	 * Helpers
	 ********************/

	/**
	 * Keeping it DRY by doing our testing right here.
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config
	 * @param bool $with_defaults
	 *
	 * @return void
	 */
	public function lets_test_the_config_properties( Config_Contract $config, $with_defaults = false ) {

		$this->assertTrue( is_array( $config->foo ) );
		$this->assertEquals( 'WordPress', $config->foo['platform'] );
		$this->assertEquals( 'genesis', $config->foo['theme'] );
		$this->assertEquals( 'hellofromTonya', $config->who );


		if ( $with_defaults ) {
			$this->assertEquals( 0, $config->is_enabled );
			$this->assertArrayHasKey( 'num_posts', $config->foo['post'] );
			$this->assertEquals( 2, $config->foo['post']['num_columns'] );
		}
	}
}