<?php
namespace Fulcrum\Tests\Custom\Shortcode;

use Fulcrum\Config\Factory;
use Fulcrum\Custom\Shortcode\Config_Validator;
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
		$this->defaults = include( FULCRUM_PLUGIN_DIR . 'src/custom/shortcode/config/defaults.php' );

		$this->validator = new Config_Validator();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_exception_thrown_for_no_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'Invalid config for shortcode.' );

		$this->validator->is_config_valid( new Empty_Config );
	}

	function test_exception_thrown_for_missing_element_in_config_file() {
		$this->setExpectedException( 'InvalidArgumentException', 'Invalid config for shortcode.' );

		$configs = array(
			array(
				'shortcode' => 'shortcode_foo',
				'defaults'  => array(
					'class' => 'some-class',
					'type'  => 'bar',
				),
			),
			array(
				'view'     => __DIR__ . '/views/foo.php',
				'defaults' => array(
					'class' => 'some-class',
					'type'  => 'bar',
				),
			),
			array(
				'shortcode' => 'shortcode_foo',
				'view'      => __DIR__ . '/views/foo.php',
				'defaults'  => '',
			),
			array(
				'shortcode'      => 'shortcode_foo',
				'view'           => __DIR__ . '/views/foo.php',
				'defaults'       => array(
					'class' => 'some-class',
					'type'  => 'bar',
				),
				'content_filter' => false,
			)
		);

		foreach ( $configs as $config_array ) {
			$config = Factory::create( $config_array, $this->defaults );
			$actual = $this->validator->is_config_valid( $config );

			$this->assertNull( $actual );
		}
	}

	function test_exception_thrown_for_invalid_view() {
		$invalid_view = __DIR__ . '/views/some-bogus-view-file.php';

		$this->setExpectedException( 'RuntimeException', sprintf( 'The specified view file [%s] is not readable', $invalid_view ) );

		$config_array = array(
			'shortcode' => 'shortcode_foo',
			'view'      => $invalid_view,
			'defaults'  => array(
				'class' => 'some-class',
				'type'  => 'bar',
			),
		);

		$actual = $this->validator->is_config_valid( Factory::create( $config_array, $this->defaults ) );

		$this->assertNull( $actual );
	}

	function test_no_view_parameter_set() {
		$config_array = array(
			'shortcode' => 'shortcode_foo',
			'no_view'   => true,
			'defaults'  => array(
				'class' => 'some-class',
				'type'  => 'bar',
			),
		);

		$actual = $this->validator->is_config_valid( Factory::create( $config_array, $this->defaults ) );

		$this->assertTrue( $actual );
	}
}