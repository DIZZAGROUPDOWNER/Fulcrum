<?php
namespace Fulcrum\Tests\Custom\Post_Type;

use Fulcrum\Custom\Post_Type\Feed;
use Fulcrum\Custom\Post_Type\Post_Type_Supports;
use WP_UnitTestCase;
use Fulcrum\Config\Factory;
use Fulcrum\Custom\Post_Type\Post_Type;

include_once FULCRUM_MOCKS_DIR . 'mock-empty-config.php';

class Feed_Test extends WP_UnitTestCase {

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

	function test_add_to_feed() {
		$qv_mock = array(
			'feed'      => '',
			'post_type' => get_post_types(),
		);

		$this->config->push( 'add_feed', true );

		$this->init_cpt( true );

		$feed = new Feed( $this->config, $this->post_type );

		$qv = $feed->add_or_remove_to_from_rss_feed( $qv_mock );
		$this->assertTrue( in_array( 'foo', $qv['post_type'] ) );
	}

	function test_add_to_feed_when_no_post_types_in_feed() {
		$this->config->push( 'add_feed', true );
		$this->init_cpt( true );

		$feed = new Feed( $this->config, $this->post_type );

		$qv_mock = array(
			'feed'      => '',
			'post_type' => '',
		);
		$qv      = $feed->add_or_remove_to_from_rss_feed( $qv_mock );

		$this->assertTrue( in_array( 'foo', $qv['post_type'] ) );
	}

	function test_remove_from_feed() {
		$qv_mock = array(
			'feed'      => '',
			'post_type' => get_post_types(),
		);

		$this->config->push( 'add_feed', false );
		$this->init_cpt( true );
		$feed = new Feed( $this->config, $this->post_type );

		$qv                     = $feed->add_or_remove_to_from_rss_feed( $qv_mock );
		$this->assertFalse( in_array( 'foo', $qv['post_type'] ) );
	}
}
