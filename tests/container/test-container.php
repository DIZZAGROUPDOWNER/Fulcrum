<?php
namespace Fulcrum\Tests\Container;

use Fulcrum\Container\Container;
use WP_UnitTestCase;

class Container_Test extends WP_UnitTestCase {

	protected $initial_parameters;

	function setUp() {
		parent::setUp();

		$this->initial_parameters = array(
			'foo' => 'Hello World',
			'bar' => 10,
			'baz' => 'Fulcrum',
		);
	}

	function test_initial_paramters_loaded_in_container() {
		$container = new Container( $this->initial_parameters );

		$this->assertEquals( 'Hello World', $container['foo'] );
		$this->assertEquals( 10, $container['bar'] );
		$this->assertEquals( 'Fulcrum', $container['baz'] );

		foreach( $this->initial_parameters as $unique_id => $value ) {
			$this->assertEquals( $value, $container[ $unique_id ] );
		}
	}

	function test_has() {
		$container = new Container( $this->initial_parameters );

		$this->assertTrue( $container->has('foo') );
		$this->assertTrue( $container->has('bar') );
		$this->assertTrue( $container->has('baz') );
	}

	function test_get() {
		$container = new Container( $this->initial_parameters );

		foreach( $this->initial_parameters as $unique_id => $value ) {
			$this->assertEquals( $value, $container->get( $unique_id ) );
		}
	}

	function test_adding_values_to_container() {
		$container = new Container();

		$container['fulcrum'] = 'some value';
		$this->assertEquals( 'some value', $container['fulcrum'] );

		$container['some_number'] = 52;
		$this->assertEquals( 52, $container['some_number'] );

		$container['some_array'] = array(
			'foo' => 'foobar'
		);
		$this->assertEquals( array(
			'foo' => 'foobar'
		), $container['some_array'] );
	}

	function test_adding_closure_to_container() {
		$container = new Container();

		$container['some_closure'] = function() {
			return new \stdClass();
		};

		$this->assertInstanceOf( 'stdClass', $container['some_closure']);
	}
	
	function test_invalid_concrete_config() {
		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'The configuration provided for the unique ID of [ foo ] is not valid.' );

		$container = new Container();
		
		$concrete_config = array(
//			'autoload' => false,
			'concrete' => '',
		);
		$container->register_concrete( $concrete_config, 'foo' );

		$concrete_config = array(
			'autoload' => false,
//			'concrete' => '',
		);
		$container->register_concrete( $concrete_config, 'foo' );

		$concrete_config = array(
//			'autoload' => false,
			'concrete' => function( $container ) {
				return new \stdClass();
			},
		);
		$container->register_concrete( $concrete_config, 'foo' );
	}

	function test_concrete_is_not_callable() {
		$container = new Container();

		$concrete_config = array(
			'autoload' => false,
			'concrete' => '',
		);

		$this->setExpectedException( 'Fulcrum\Support\Exceptions\Configuration_Exception', 'The concrete for the unique ID of [ foo ] is not callable.' );
		$container->register_concrete( $concrete_config, 'foo' );
	}

	function test_concrete_is_not_autoloaded() {
		$container = new Container();

		$concrete_config = array(
			'autoload' => false,
			'concrete' => function( $container ) {
				$instance = new \stdClass();
				$instance->bar = 'some value';

				return $instance;
			},
		);

		$foo = $container->register_concrete( $concrete_config, 'foo' );

		$this->assertTrue( $container->has( 'foo' ) );
		$this->assertNull( $foo );
	}

	function test_instantiating_out_of_container() {
		$container = new Container();

		$concrete_config = array(
			'autoload' => false,
			'concrete' => function( $container ) {
				$instance = new \stdClass();
				$instance->bar = 'some value';

				return $instance;
			},
		);

		$container->register_concrete( $concrete_config, 'foo' );

		//Now instantiate it.
		$foo = $container['foo'];
		$this->assertInstanceOf( 'stdClass', $foo );
		$this->assertEquals( 'some value', $foo->bar );
	}

	function test_concrete_autoload() {
		$container = new Container();

		$concrete_config = array(
			'autoload' => true,
			'concrete' => function( $container ) {
				$instance = new \stdClass();
				$instance->bar = 'some value';

				return $instance;
			},
		);

		$foo = $container->register_concrete( $concrete_config, 'foo' );

		$this->assertTrue( $container->has( 'foo' ) );
		$this->assertInstanceOf( 'stdClass', $foo );
		$this->assertEquals( 'some value', $foo->bar );
	}
}