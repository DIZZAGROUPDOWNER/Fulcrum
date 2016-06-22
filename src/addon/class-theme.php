<?php

/**
 * Theme Initialization and Setup Addon.  Fulcrum takes care of initializing and setting up the Genesis
 * child theme for you.  All you do is pass a configuration file to it.
 *
 * @package     Fulcrum\Addon
 * @since       1.1.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */

namespace Fulcrum\Addon;

use Fulcrum\Fulcrum;
use Fulcrum\Fulcrum_Contract;
use Fulcrum\Config\Config_Contract;

class Theme {

	/**
	 * Runtime Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Instance of Fulcrum
	 *
	 * @var Fulcrum_Contract
	 */
	protected $fulcrum;

	/**
	 * Instance of the theme.
	 *
	 * @var \WP_Theme
	 */
	protected $child_theme;

	/**
	 * Path to the child theme root folder
	 * 
	 * @var string
	 */
	protected $child_theme_dir;

	/**
	 * Child theme root URL
	 *
	 * @var string
	 */
	protected $child_theme_url;

	/*************************
	 * Instantiate & Init
	 ************************/

	/**
	 * Instantiate the plugin
	 *
	 *
	 * So all we want to do here is get the theme up and running by
	 * setting up the base properties and constants.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->init_properties();
		$this->init_constants();
	}

	/**
	 * Instantiate the plugin
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters
	 * @param Fulcrum_Contract $fulcrum Instance of Fulcrum
	 */
	public function init( Config_Contract $config, Fulcrum_Contract $fulcrum = null ) {
		$this->config  = $config;
		$this->fulcrum = is_null( $fulcrum ) ? Fulcrum::getFulcrum() : $fulcrum;

		$this->init_pre();
		$this->init_events();
	}

	/**
	 * Initialize the properties.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_properties() {
		$this->child_theme = wp_get_theme();
		$this->child_theme_dir = get_stylesheet_directory();
		$this->child_theme_url = get_stylesheet_directory_uri();
	}

	/**
	 * Initialize the constants.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_constants() {
		define( 'CHILD_THEME_NAME', $this->child_theme->Name );
		define( 'CHILD_THEME_URL', $this->child_theme_url );
		define( 'CHILD_THEME_VERSION', $this->get_theme_version() );

		if ( ! defined( 'CHILD_THEME_DIR' ) ) {
			define( 'CHILD_THEME_DIR', $this->child_theme_dir );
		}
		define( 'CHILD_DIST_URL', $this->child_theme_url . '/assets/dist/' );
	}

	/**
	 * Get the theme version based upon if this is a development environment or not.
	 *
	 * @since 1.0.0
	 *
	 * @return int|string
	 */
	protected function get_theme_version() {
		return fulcrum_is_dev_environment()
			? filemtime( $this->child_theme_dir . '/style.css' )
			: $this->child_theme->Version;
	}

	/**
	 * Pre-initialization, meaning these tasks go first.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_pre() {
		$this->task_loader( 'init_pre' );
	}

	/**
	 * Pre-initialization, meaning these tasks go first.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	protected function init_events() {
		add_action( 'genesis_setup', array( $this, 'setup' ), 15 );

		if ( $this->config->load_min_stylesheet ) {
			add_filter( 'stylesheet_uri', array( $this, 'change_stylesheet_uri_to_min' ) );
		}
	}

	/**
	 * Setup the theme.
	 *
	 * @since 1.0.0
	 *
	 * @return null
	 */
	public function setup() {
		$this->task_loader( 'setup' );
	}

	/**
	 * Change the stylesheet to the minified version.
	 *
	 * @since 1.0.0
	 *
	 * @param string $stylesheet_uri
	 *
	 * @return string
	 */
	public function change_stylesheet_uri_to_min( $stylesheet_uri ) {
		return CHILD_URL . '/style.min.css';
	}

	/**********************
	 * Pre Tasks
	 *********************/

	/**
	 * Let's enqueue the theme's script(s)
	 *
	 * @since 1.0.0
	 *
	 * @param array $scripts_config
	 *
	 * @return void
	 */
	protected function enqueue_scripts( array $scripts_config ) {
		foreach( $scripts_config as $handle => $config ) {
			$this->fulcrum['provider.asset']->register( $config, $handle );
		}
	}

	/**
	 * Register Google Fonts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function google_fonts( array $config ) {

		$unique_id = $this->config->theme_slug . '_' . $config['handle'];

		if ( $this->fulcrum->has( $unique_id ) ) {
			return;
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $config['font_families'] ) ),
			'subset' => urlencode( 'latin,latin-ext' ),
		);

		$asset_config = array(
			'is_script' => false,
			'handle'    => $config['handle'],
			'config'    => array(
				'file'    => add_query_arg( $query_args, '//fonts.googleapis.com/css' ),
				'deps'    => array(),
				'version' => $this->config->version,
			),
		);

		$this->fulcrum['provider.asset']->register( $asset_config, $unique_id );
	}

	/**
	 * Login form styling handler.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function login_form( array $config ) {
		fulcrum_load_login_form_styling( $config );
	}

	/**
	 * Let's load up the favicon from the theme.
	 *
	 * @since 1.0.0
	 *
	 * @param string Favicon URL
	 *
	 * @return string
	 */
	protected function favicon( $favicon_url ) {
		add_filter( 'genesis_pre_load_favicon', function () use ( $favicon_url ) {
			return $favicon_url;
		} );
	}

	/**********************
	 * Theme Setup Tasks
	 *********************/

	/**
	 * Add image sizes.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function add_image_size( array $config ) {
		foreach ( $config as $name => $parameters ) {
			if ( ! is_array( $parameters ) ) {
				continue;
			}

			if ( ! isset( $parameters['width'] ) || ! isset( $parameters['height'] ) ) {
				continue;
			}

			$width  = (int) $parameters['width'];
			$height = (int) $parameters['height'];
			$crop   = isset( $parameters['crop'] ) ? $parameters['crop'] : false;

			add_image_size( $name, $width, $height, $crop );
		}
	}

	/**
	 * Add theme supports.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function add_theme_support( array $config ) {
		foreach ( $config as $feature => $parameters ) {
			if ( is_null( $parameters ) ) {
				add_theme_support( $feature );
			} else {
				add_theme_support( $feature, $parameters );
			}
		}
	}

	/**
	 * Unregister default Genesis layouts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function genesis_unregister_layout( array $config ) {
		foreach ( $config as $layout ) {
			genesis_unregister_layout( $layout );
		}
	}

	/**
	 * Disable the edit link on the front-end (as it drives me crazy).
	 *
	 * @since 1.0.0
	 *
	 * @param bool $ok_to_disable_it When set to true, the `edit_post_link` is disabled.
	 * 
	 * @return void
	 */
	protected function disable_edit_link( $ok_to_disable_it = false ) {
		if ( ! $ok_to_disable_it ) {
			return;
		}
		add_filter( 'edit_post_link', '__return_empty_string' );
	}

	/**
	 * Remove Genesis Blog page template.
	 *
	 * @since 1.0.0
	 *
	 * @param array $page_templates Existing recognised page templates.
	 *
	 * @return array Amended recognised page templates.
	 */
	protected function remove_page_templates( array $page_templates ) {
		add_filter( 'theme_page_templates', function () use ( $page_templates ) {
			unset( $page_templates['page_blog.php'] );

			return $page_templates;
		} );
	}

	/**
	 * Register sidebars.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return void
	 */
	protected function register_sidebars( array $config ) {
		foreach ( $config as $sidebar ) {
			genesis_register_sidebar( $sidebar );
		}
	}

	/**
	 * Unregister sidebars.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Array of sidebars to unregister
	 *
	 * @return void
	 */
	protected function unregister_sidebars( array $config ) {
		foreach ( $config as $sidebar ) {
			unregister_sidebar( $sidebar );
		}
	}

	/**
	 * Enable shortcodes in the WordPress default text widget when configured.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $is_enabled When true, shortcodes are enabled for the text widget.
	 *
	 * @return void
	 */
	protected function do_shortcodes_in_text_widget( $is_enabled = false ) {
		if ( ! $is_enabled ) {
			return;
		}
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**********************
	 * Helpers
	 *********************/

	/**
	 * Load up the tasks by calling each task method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $config_key
	 * @param string $method_prefix
	 *
	 * @return bool
	 */
	protected function task_loader( $config_key, $method_prefix = '' ) {
		if ( ! $this->config->has( $config_key ) ) {
			return false;
		}

		foreach ( $this->config->$config_key as $task => $task_config ) {
			$method_name = $method_prefix . $task;

			$this->$method_name( $task_config );
		}
	}
}
