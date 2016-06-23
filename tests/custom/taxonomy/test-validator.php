<?php
namespace Fulcrum\Tests\Custom\Taxonomy;

use Fulcrum\Config\Factory;
use Fulcrum\Custom\Taxonomy\Validator;
use WP_UnitTestCase;
use Fulcrum\Tests\Mocks\Empty_Config;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

class Validator_Test extends WP_UnitTestCase {

	protected $taxonomy = 'foo-category';
	protected $object_type = 'foo';
	protected $config;
	protected $defaults;
	protected $validator;

	function setUp() {
		parent::setUp();

		$this->config   = include( __DIR__ . '/config.php' );
		$this->defaults = include( FULCRUM_PLUGIN_DIR . 'src/custom/taxonomy/config/defaults.php' );

		$this->validator = new Validator();
	}

	function tearDown() {
		parent::tearDown();
	}

	function test_exception_thrown_for_taxonomy_name() {
		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Taxonomy Configuration, the taxonomy name cannot be empty.' );

		$this->validator->is_valid( '', $this->object_type, new Empty_Config );
	}

	function test_exception_thrown_for_object_type() {
		$this->setExpectedException( 'InvalidArgumentException', 'For Custom Taxonomy Configuration, the object_type in config cannot be empty.' );

		$this->validator->is_valid( $this->taxonomy, '', new Empty_Config );
	}

	function test_exception_thrown_for_no_config_file() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [args] cannot be empty.' );

		$this->validator->is_valid( $this->taxonomy, $this->object_type, new Empty_Config );
	}

	function test_exception_thrown_for_plural_name() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [plural_name] cannot be empty.' );

		unset( $this->config['plural_name'] );

		$config = Factory::create( $this->config, $this->defaults );
		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertNull( $actual );
	}

	function test_exception_thrown_for_invalid_plural_name() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [plural_name] cannot be empty.' );

		$this->config['plural_name'] = false;

		$config = Factory::create( $this->config, $this->defaults );
		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertNull( $actual );
	}

	function test_exception_thrown_for_singular_name() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [singular_name] cannot be empty.' );

		unset( $this->config['singular_name'] );

		$config = Factory::create( $this->config, $this->defaults );
		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertNull( $actual );
	}

	function test_exception_thrown_for_invalid_singular_name() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [singular_name] cannot be empty.' );

		$this->config['singular_name'] = null;

		$config = Factory::create( $this->config, $this->defaults );
		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertNull( $actual );
	}

	function test_exception_thrown_for_missing_args() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [args] cannot be empty.' );

		unset( $this->config['args'] );

		$config = Factory::create( $this->config, $this->defaults );

		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );
		$this->assertNull( $actual );
	}

	function test_exception_thrown_for_invalid_args() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'For Custom Taxonomy Configuration, the config for [args] cannot be empty.' );

		$this->config['args'] = '';

		$config = Factory::create( $this->config, $this->defaults );

		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertNull( $actual );
	}

	function test_for_is_valid() {
		$config = Factory::create( $this->config, $this->defaults );

		$actual = $this->validator->is_valid( $this->taxonomy, $this->object_type, $config );

		$this->assertTrue( $actual );

	}
}
