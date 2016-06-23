<?php 

/**
 * Dependency Injection Container - extends the functionality of Pimple DI Container.
 *
 * @package     Fulcrum\Container
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */
namespace Fulcrum\Container;

use Fulcrum\Support\Exceptions\Configuration_Exception;
use Pimple\Container as Pimple;

class Container extends Pimple implements Container_Contract {

	/**
	 * Instance of Container
	 *
	 * @var Container_Contract
	 */
	static $instance;

	/**************************
	 * Instantiate & Initialize
	 *************************/

	/**
	 * Instantiate the container
	 *
	 * @since 1.0.0
	 *
	 * @param array $values Array of values to load into the container upon instantiation
	 * 
	 * @return Container
	 */ 
	public function __construct( array $values = array() ) {
		self::$instance = $this;
		
		parent::__construct( $values );
	}

	/****************************
	 * Public Methods
	 ***************************/

	/**
	 * Get the Core Instance
	 *
	 * @since 1.1.0
	 *
	 * @return self
	 */
	public static function getContainer() {
		return self::$instance;
	}

	/**
	 * Gets a parameter or an object.
	 *
	 * @since 1.0.0
	 *
	 * @param string $unique_id The unique identifier for the parameter or object
	 * @return mixed The value of the parameter or an object
	 *
	 * @throws \InvalidArgumentException if the identifier is not defined
	 */
	public function get( $unique_id ) {
		return $this->offsetGet( $unique_id );
	}

	/**
	 * Checks if a parameter or an object is set.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $unique_id The unique identifier for the parameter or object
	 * @return bool
	 */
	public function has( $unique_id ) {
		return $this->offsetExists( $unique_id );
	}

	/**
	 * Register Concrete closures into the Container
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 * @param string $unique_id
	 * @return mixed
	 */
	public function register_concrete( array $config, $unique_id ) {
		if ( ! $this->is_concrete_config_valid( $config, $unique_id ) ) {
			return false;
		}

		$this[ $unique_id ] = $config['concrete'];

		if ( $config['autoload'] ) {
			return $this->autoload_the_concrete( $config, $unique_id );
		}
	}

	/**
	 * Autoload the concrete into the container.
	 *
	 * @since 1.1.1
	 *
	 * @param array $config
	 * @param string $unique_id
	 *
	 * @return mixed
	 */
	protected function autoload_the_concrete( array $config, $unique_id ) {
		if ( true === $config['autoload'] ) {
			return $this[ $unique_id ];
		}

		if ( is_callable( $config['autoload'] ) ) {
			call_user_func( $config['autoload'], $this[ $unique_id ] );
		}
	}

	/**
	 * Checks if the concrete's config is valid.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config
	 *
	 * @return bool
	 *
	 * @throws Configuration_Exception
	 */
	protected function is_concrete_config_valid( array $config, $unique_id ) {
		$default = array(
			'autoload' => false,
			'concrete' => '',
		);

		$differences = array_diff_key( $default, $config );

		if ( ! empty( $differences ) ) {
			throw new Configuration_Exception(
				sprintf( __( 'The configuration provided for the unique ID of [ %s ] is not valid.', 'fulcrum' ),
					$unique_id
				)
			);
		}

		if ( ! array_key_exists( 'concrete', $config ) || ! is_callable( $config['concrete'] ) ) {
			throw new Configuration_Exception(
				sprintf( __( 'The concrete for the unique ID of [ %s ] is not callable.', 'fulcrum' ),
					$unique_id
				)
			);
		}

		return true;
	}
}
