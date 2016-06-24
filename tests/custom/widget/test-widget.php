<?php
namespace Fulcrum\Tests\Custom\Widget;

use Fulcrum\Config\Config;
use Fulcrum\Fulcrum;
use Fulcrum\Test\Custom\Widget\Foo_Widget;
use WP_UnitTestCase;
use Fulcrum\Config\Factory;

include_once( 'class-foo-widget.php' );

class Widget_Test extends WP_UnitTestCase {

	protected $config;
	protected $fulcrum;
	protected $unique_id;

	function setUp() {
		parent::setUp();

		$this->init();
	}

	function tearDown() {
		parent::tearDown();

		unregister_widget( $this->unique_id );
		unset( $this->fulcrum[ $this->unique_id ] );
	}
	
	function init() {
		$this->fulcrum = Fulcrum::getFulcrum();

		$this->config = include( 'config.php' );
		foreach( $this->config['register_concretes'] as $unique_id => $concrete_config ) {
			$this->unique_id = $unique_id;

			$this->fulcrum->register_concrete( $concrete_config, $unique_id );
		}

	}

	function test_registering_and_unregistering_widget() {
		global $wp_widget_factory;

		register_widget( $this->unique_id );

		$this->assertArrayHasKey( $this->unique_id, $wp_widget_factory->widgets );

		unregister_widget( $this->unique_id );
		$this->assertArrayNotHasKey( $this->unique_id, $wp_widget_factory->widgets );
	}

	function test_widget_constructor() {
		$widget = new Foo_Widget();

		$this->assertEquals( 'foo-widget', $widget->id_base );
		$this->assertEquals( 'Foo', $widget->name );
		$this->assertArrayHasKey( 'classname', $widget->widget_options );
		$this->assertEquals( 'foo-widget', $widget->widget_options['classname'] );
	}

	function test_widget_form() {
		$widget = new Foo_Widget();

		ob_start();
		$widget->form( array() );
		$html = ob_get_clean();

		$this->assertEquals( '<p>Foo Form</p>', $html );
		$this->assertContains( 'Foo Form', $html );
	}

	function test_rendering() {
		register_widget( $this->unique_id );

		ob_start();
		the_widget(
			$this->unique_id,
			array( 'title' => 'Foo', 'text' => 'Sample text', 'class' => 'foo' )
		);
		$html = ob_get_clean();

		unregister_widget( $this->unique_id );

		$this->assertRegExp( '/<p class="foo">Sample text<\/p>/', $html );
		$expected = '<div class="foo widget foo-widget"><h2 class="widgettitle">Foo</h2><p class="foo">Sample text</p>';
		$this->assertContains( $expected, $html );
	}
}
