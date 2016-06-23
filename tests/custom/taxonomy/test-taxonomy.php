<?php
namespace Fulcrum\Tests\Custom\Taxonomy;

use Fulcrum\Custom\Post_Type\Post_Type;
use Fulcrum\Custom\Post_Type\Post_Type_Supports;
use WP_UnitTestCase;
use Fulcrum\Config\Factory;
use Fulcrum\Custom\Taxonomy\Taxonomy;


class Taxonomy_Test extends WP_UnitTestCase {

	protected $taxonomy = 'foo-category';
	protected $object_type = 'foo';

	protected $config_array = array();
	protected $config;

	function setUp() {
		parent::setUp();

		_clean_term_filters();
		wp_cache_delete( 'last_changed', 'terms' );

		$this->config_array = include( __DIR__ . '/config.php' );
		$this->config       = Factory::create( $this->config_array );
		$this->init_cpt();
	}

	protected function init_cpt() {
		$config = Factory::create( FULCRUM_TESTS_DIR . 'custom/post-type/config.php' );
		$cpt    = new Post_Type( $config, 'foo', new Post_Type_Supports( $config ) );
		$cpt->register();
	}

	function tearDown() {
		parent::tearDown();
		_unregister_post_type( 'foo' );
		_unregister_taxonomy( $this->taxonomy );
	}

	function test_cpt_was_created_first() {
		$this->assertTrue( post_type_exists( 'foo' ) );
	}

	function test_registering_taxonomy() {
		$tax = new Taxonomy( $this->taxonomy, $this->object_type, $this->config );
		$tax->register();

		$taxonomies = get_taxonomies( array( 'name' => $this->taxonomy ), 'objects' );

		$obj = $taxonomies[ $this->taxonomy ];

		$this->assertInstanceOf( 'stdClass', $obj );
		$this->assertEquals( 'foo-category', $obj->name );
		$this->assertEquals( 'Foo Categories', $obj->labels->name );
		$this->assertEquals( 'Foo Categories', $obj->labels->menu_name );
		$this->assertEquals( 'foo-category', $obj->rewrite );
	}

	function test_registering_taxonomy_with_terms() {
		$tax = new Taxonomy( $this->taxonomy, $this->object_type, $this->config );
		$tax->register();

		$bar    = $this->factory->term->create( array( 'name' => 'Bar', 'taxonomy' => $this->taxonomy ) );
		$baz    = $this->factory->term->create( array( 'name' => 'Baz', 'parent' => $bar, 'taxonomy' => $this->taxonomy ) );
		$foobar = $this->factory->term->create( array( 'name' => 'FooBar', 'parent' => $bar, 'taxonomy' => $this->taxonomy ) );

		$terms  = get_terms( $this->taxonomy, array( 'hide_empty' => false ) );
		$this->assertNotInstanceOf( 'WP_Error', $terms );
		$this->assertEquals( array( 'Bar', 'Baz', 'FooBar' ), wp_list_pluck( $terms, 'name' ) );
	}
}
