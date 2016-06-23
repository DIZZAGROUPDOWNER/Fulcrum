<?php
namespace Fulcrum\Tests\Custom\Post_Type;

use Fulcrum\Custom\Post_Type\Post_Type_Supports;
use WP_UnitTestCase;
use Fulcrum\Config\Factory;
use Fulcrum\Custom\Post_Type\Post_Type;

class Post_Type_Test extends WP_UnitTestCase {

	protected $post_type = 'foo';
	protected $config_array = array();
	protected $cpt;
	protected $config;
	protected $supports;

	function setUp() {
		parent::setUp();

		$this->config_array = include( __DIR__ . '/config.php' );
		$this->config       = Factory::create( $this->config_array );
		$this->supports     = new Post_Type_Supports( $this->config );
	}

	function tearDown() {
		parent::tearDown();

		_unregister_post_type( $this->post_type );
	}

	protected function init_cpt() {
		$this->cpt = new Post_Type( $this->config, $this->post_type, $this->supports );

		$this->cpt->register();
	}

	function test_registering() {
		$this->init_cpt();

		$this->assertTrue( post_type_exists( $this->post_type ) );

		$post_obj = get_post_type_object( $this->post_type );
		$this->assertInstanceOf( 'stdClass', $post_obj );
	}

	function test_cpt_default_labels() {
		$this->init_cpt();

		$post_obj = get_post_type_object( $this->post_type );

		$this->assertInstanceOf( 'stdClass', $post_obj );
		$this->assertEquals( $this->post_type, $post_obj->name );
		$this->assertEquals( 'Foo', $post_obj->label );
		$this->assertEquals( 'Foo', $post_obj->labels->name );
		$this->assertEquals( 'Add New Foo', $post_obj->labels->add_new_item );
	}

	function test_unregistering_cpt() {
		$this->init_cpt();

		$post_obj = get_post_type_object( $this->post_type );
		$this->assertInstanceOf( 'stdClass', $post_obj );

		$this->cpt->__destruct();

		$this->assertNull( get_post_type_object( $this->post_type ) );
		$this->assertEmpty( get_post_types( array( '_builtin' => false ) ) );
		$this->assertFalse( post_type_exists( $this->post_type ) );
	}

	function test_columns_filter() {
		$this->init_cpt();

		$actual         = $this->cpt->columns_filter( array() );
		$expected       = $this->config['columns_filter'];
		$expected['cb'] = '<input type="checkbox" />';

		$this->assertSame( $actual, $expected );
	}

	function test_columns_data_throws_error() {
		$this->setExpectedException(
			'Fulcrum\Support\Exceptions\Configuration_Exception',
			'The callback [invalid_function_name], for the custom post type [foo], was not found, as call_user_func_array() expects a valid callback function/method.' );

		$this->config->columns_data['date'] = array(
			'callback' => 'invalid_function_name',
			'echo'     => false,
			'args'     => array( 5 ),
		);

		$this->init_cpt();

		$this->cpt->columns_data( 'date', 5 );
	}

	function test_columns_data_and_callback() {
		$this->config->columns_data = array(
			'col1' => array(
				'callback' => array( $this, 'callback_test_columns_data_and_callback' ),
				'echo'     => true,
				'args'     => array( 5, 'somedata' ),
			),
		);

		$this->init_cpt();

		ob_start();
		$this->cpt->columns_data( 'col1', 5 );
		$actual = ob_get_clean();

		$this->assertSame( '5, somedata', $actual );
	}

	public function callback_test_columns_data_and_callback( $post_id, $data ) {
		return $post_id . ', ' . $data;
	}
}
