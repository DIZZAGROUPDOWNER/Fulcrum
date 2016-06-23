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
namespace Fulcrum\Custom\Post_Type;

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
	 * Post type name (all lowercase & no spaces)
	 *
	 * @var string
	 */
	protected $post_type;

	/**
	 * Runs the validation to check if the configuration is valid.
	 *
	 * @since 1.1.1
	 *
	 * @param Config_Contract $config Runtime configuration parameters to validate
	 * @param string $post_type Post type name (all lowercase & no spaces)
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function is_valid( Config_Contract $config, $post_type ) {

		if ( ! $this->is_post_type_valid( $post_type ) ) {
			return false;
		}

		$this->init( $config, $post_type );

		$is_valid = $this->are_parameters_configured();

		$this->set_initial_state();

		return $is_valid;
	}

	/**
	 * Initializes the properties for running.
	 *
	 * @since 1.1.1
	 *
	 * @param Config_Contract $config Runtime configuration parameters to validate
	 * @param string $post_type
	 *
	 * @return void
	 */
	protected function init( Config_Contract $config, $post_type ) {
		$this->config    = $config;
		$this->post_type = $post_type;

	}


	/**
	 * Sets the initial state of the validator, which also releases memory.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function set_initial_state() {
		$this->config    = null;
		$this->post_type = '';
	}

	/**
	 * Checks if the post type is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $post_type
	 *
	 * @return bool
	 * @throw InvalidArgumentException
	 */
	protected function is_post_type_valid( $post_type ) {
		if ( $post_type ) {
			return true;
		}

		throw new InvalidArgumentException( __( 'When declaring a custom post type, the post type cannot be empty.', 'fulcrum' ) );
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
		if ( ! empty( $this->config->all() )
		) {
			return true;
		}

		throw new InvalidArgumentException( sprintf( __( 'For Custom Post Type Configuration, the config for [%s] cannot be empty.', 'fulcrum' ), $this->post_type ) );
	}
}