<?php
/**
 * Validate the runtime configuration
 *
 * @package     Fulcrum\Custom\Shortcode
 * @since       1.1.1
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Custom\Shortcode;

use Fulcrum\Config\Config_Contract;
use InvalidArgumentException;
use RuntimeException;

class Validator {

	/**
	 * Runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * View is not required for this shortcode flag.
	 *
	 * @var bool
	 */
	protected $view_is_not_required = false;

	/**
	 * Runs the validation to check if the configuration is valid.
	 *
	 * @since 1.1.1
	 *
	 * @param Config_Contract $config Runtime configuration parameters to validate
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function is_config_valid( Config_Contract $config ) {
		$this->init( $config );


		$is_valid = false;
		if ( $this->are_parameters_configured() ) {
			$is_valid = $this->view_is_not_required || $this->is_view_file_valid();
		}

		$this->set_initial_state();

		return $is_valid;
	}

	/**
	 * Initializes the properties for running.
	 *
	 * @since 1.1.1
	 *
	 * @param Config_Contract $config Runtime configuration parameters to validate
	 *
	 * @return void
	 */
	protected function init( Config_Contract $config ) {
		$this->config = $config;
		$this->check_if_view_is_required();
	}

	/**
	 * Sets the initial state of the validator, which also releases memory.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function set_initial_state() {
		$this->config               = null;
		$this->view_is_not_required = false;
	}

	/**
	 * Checks if the parameters are configured.
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 *
	 * @throws InvalidArgumentException
	 */
	protected function are_parameters_configured() {
		if ( $this->is_shortcode_configured() &&
		     $this->is_defaults_configured() &&
		     $this->is_view_configured() &&
		     $this->is_content_filter_configured()
		) {
			return true;
		}

		throw new InvalidArgumentException( __( 'Invalid config for shortcode.', 'fulcrum' ) );
	}

	/**
	 * Checks if the config is valid to start
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 * @throws RuntimeException
	 */
	protected function is_view_file_valid() {

		if ( is_readable( $this->config->view ) ) {
			return true;
		}

		throw new RuntimeException( sprintf( __( 'The specified view file [%s] is not readable.', 'fulcrum' ), $this->config->view ) );
	}

	/**
	 * Checks if the shortcode is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_shortcode_configured() {
		return $this->config->has( 'shortcode' ) &&
		       $this->config->shortcode;
	}

	/**
	 * Checks if the defaults are configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_defaults_configured() {
		return $this->config->has( 'defaults' ) &&
		       $this->config->is_array( 'defaults' );
	}

	/**
	 * Checks if the view is required and, if it is, then it checks if it's configured.
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 */
	protected function is_view_configured() {
		if ( $this->view_is_not_required ) {
			return true;
		}

		return $this->config->has( 'view' ) &&
		       $this->config->view;
	}

	/**
	 * Is the content filter parameter configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_content_filter_configured() {
		return $this->config->has( 'content_filter' ) &&
		       $this->config->content_filter;
	}

	/**
	 * Checks if a view is required.
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 */
	protected function check_if_view_is_required() {
		$this->view_is_not_required = $this->config->has( 'no_view' ) &&
		                              true === $this->config->no_view;
	}
}