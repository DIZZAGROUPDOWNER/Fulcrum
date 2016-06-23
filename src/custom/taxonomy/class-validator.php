<?php
/**
 * Validate the runtime configuration
 *
 * @package     Fulcrum\Custom\Taxonomy
 * @since       1.1.1
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Custom\Taxonomy;

use Fulcrum\Config\Config_Contract;
use Fulcrum\Support\Exceptions\Configuration_Exception;
use InvalidArgumentException;
use RuntimeException;

class Validator {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Taxonomy name (all lowercase & no spaces)
	 *
	 * @var string
	 */
	protected $taxonomy_name;

	/**
	 * Name of the object type for the taxonomy object
	 *
	 * @var string|array
	 */
	protected $object_type;

	/**
	 * Runs the validation to check if the configuration is valid.
	 *
	 * @since 1.1.1
	 *
	 * @param string $taxonomy_name Taxonomy name (all lowercase & no spaces)
	 * @param string|array $object_type Name of the object type for the taxonomy object
	 * @param Config_Contract $config Runtime configuration parameters
	 *
	 * @return bool
	 * @throws InvalidArgumentException
	 * @throws RuntimeException
	 */
	public function is_valid( $taxonomy_name, $object_type, Config_Contract $config ) {

		if ( ! $this->is_taxonomy_name_valid( $taxonomy_name ) ) {
			return false;
		}

		if ( ! $this->is_object_type_valid( $object_type ) ) {
			return false;
		}

		$this->init( $taxonomy_name, $object_type, $config );

		$is_valid = $this->are_parameters_configured();

		$this->set_initial_state();

		return $is_valid;
	}

	/**
	 * Initializes the properties for running.
	 *
	 * @since 1.1.1
	 *
	 * @param string $taxonomy_name Taxonomy name (all lowercase & no spaces)
	 * @param string|array $object_type Name of the object type for the taxonomy object
	 * @param Config_Contract $config Runtime configuration parameters
	 *
	 * @return void
	 */
	protected function init( $taxonomy_name, $object_type, Config_Contract $config ) {
		$this->taxonomy_name = $taxonomy_name;
		$this->object_type   = $object_type;
		$this->config        = $config;
	}


	/**
	 * Sets the initial state of the validator, which also releases memory.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function set_initial_state() {
		$this->taxonomy_name = '';
		$this->object_type   = '';
		$this->config        = null;
	}

	/**
	 * Checks if the taxonomy is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy_name
	 *
	 * @return bool
	 * @throw InvalidArgumentException
	 */
	protected function is_taxonomy_name_valid( $taxonomy_name ) {
		if ( $taxonomy_name ) {
			return true;
		}

		throw new InvalidArgumentException( __( 'For Custom Taxonomy Configuration, the taxonomy name cannot be empty.', 'fulcrum' ) );
	}


	/**
	 * Checks if the taxonomy is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param string $object_type
	 *
	 * @return bool
	 * @throw InvalidArgumentException
	 */
	protected function is_object_type_valid( $object_type ) {
		if ( $object_type && ! empty( $object_type ) ) {
			return true;
		}

		throw new InvalidArgumentException( __( 'For Custom Taxonomy Configuration, the object_type in config cannot be empty.', 'fulcrum' ) );
	}

	/**
	 * Checks if the parameters are configured.
	 *
	 * @since 1.1.1
	 *
	 * @return bool
	 */
	protected function are_parameters_configured() {
		return (
			$this->are_args_configured() &&
		    $this->is_plural_name_configured() &&
		    $this->is_singular_name_configured()
		);
	}

	/**
	 * Checks if the `args` parameter is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function are_args_configured() {
		if ( $this->config->has( 'args' ) &&
		       $this->config->is_array( 'args' )
		) {
			return true;
		}

		$this->throw_an_error( 'args' );
	}

	/**
	 * Checks if the `plural_name` parameter is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_plural_name_configured() {
		if (    $this->config->has( 'plural_name' ) &&
		        $this->config->plural_name
		) {
			return true;
		}

		$this->throw_an_error( 'plural_name' );
	}

	/**
	 * Checks if the `singular_name` parameter is configured.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_singular_name_configured() {
		if (    $this->config->has( 'singular_name' ) &&
		        $this->config->singular_name
		) {
			return true;
		}

		$this->throw_an_error( 'singular_name' );
	}


	/**
	 * Throws an error for the specified parameter in the config file.
	 *
	 * @since 1.0.0
	 *
	 * @param string $parameter Problem parameter in the config
	 *
	 * @return void
	 *
	 * @throws Configuration_Exception
	 */
	protected function throw_an_error( $parameter ) {
		throw new Configuration_Exception(
			sprintf( __( 'For Custom Taxonomy Configuration, the config for [%s] cannot be empty.', 'fulcrum' ), $parameter )
		);
	}
}