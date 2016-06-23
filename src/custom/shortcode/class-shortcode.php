<?php

/**
 * Base Shortcode
 *
 * @package     Fulcrum\Custom\Shortcodes
 * @since       1.0.1
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */

namespace Fulcrum\Custom\Shortcode;

use Fulcrum\Config\Config_Contract;

class Shortcode implements Shortcode_Contract {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * No view is required flag
	 *
	 * @var bool
	 */
	protected $no_view_is_required = false;

	/**
	 * Shortcode attributes
	 *
	 * @var array
	 */
	protected $atts = array();

	/**
	 * Shortcode content
	 *
	 * @var string|null
	 */
	protected $content;

	/**
	 * Instantiate the Shortcode object
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 * @param Config_Validator Validator
	 */
	public function __construct( Config_Contract $config, Config_Validator $validator ) {
		if ( ! $validator->is_config_valid( $config ) ) {
			return;
		}

		$this->config = $config;

		add_shortcode( $this->config->shortcode, array( $this, 'render_callback' ) );
	}

	/**
	 * Shortcode callback which merges the attributes, calls the render() method to build
	 * the HTML, and then returns it.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts Shortcode attributes
	 * @param string $content Content between the opening & closing shortcode declarations
	 *
	 * @return string               Shortcode HTML
	 */
	public function render_callback( $atts, $content = null ) {
		$this->atts    = shortcode_atts( $this->config->defaults, $atts, $this->config->shortcode );
		$this->content = $content;

		return $this->render();
	}

	/**************
	 * Helpers
	 *************/

	/**
	 * Build the Shortcode HTML and then return it.
	 *
	 * NOTE: This is the method to extend for enhanced shortcodes (i.e. which extend this class).
	 *
	 * @since 1.0.0
	 *
	 * @return string Shortcode HTML
	 */
	protected function render() {
		$content = $this->get_content();

		ob_start();
		include( $this->config->view );

		return ob_get_clean();
	}

	/**
	 * Get the content.  This method processes the content by passing it
	 * through the shortcode function and escaping through a sanitizing filter.
	 *
	 * @since 1.1.1
	 *
	 * @return string
	 */
	protected function get_content() {
		if ( ! $this->content ) {
			return '';
		}

		$content = do_shortcode( $this->content );

		$filter  = $this->config->content_filter;
		$content = $filter( $content );

		return $content;
	}

	/**
	 * Get the ID from the attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_id() {
		if ( ! $this->atts['id'] ) {
			return '';
		}

		return sprintf( ' id="%s"', esc_attr( $this->atts['id'] ) );
	}

	/**
	 * Get the classname from the attributes.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_class() {
		if ( ! $this->atts['class'] ) {
			return '';
		}

		return ' ' . esc_attr( $this->atts['class'] );
	}
}
