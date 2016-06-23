<?php

/**
 * Taxonomy Service Provider
 *
 * @package     Fulcrum\Custom\Taxonomy
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */

namespace Fulcrum\Custom\Taxonomy;

use Fulcrum\Foundation\Service_Provider\Provider;
use Fulcrum\Config\Config;

class Taxonomy_Provider extends Provider {

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = true;

	/**
	 * Get the concrete based upon the configuration supplied.
	 *
	 * @since 1.0.0
	 *
	 * @param array $config Runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @return array
	 */
	public function get_concrete( array $config, $unique_id = '' ) {
		$service_provider = array(
			'autoload' => $config['autoload'],
			'concrete' => function ( $container ) use ( $config ) {
				$config_obj = $this->instantiate_config( $config );

				if ( ! $this->is_taxonomy_config_valid( $config['taxonomy_name'], $config['object_type'], $config_obj ) ) {
					return;
				}

				return new Taxonomy(
					$config['taxonomy_name'],
					$config['object_type'],
					$config_obj
				);
			},
		);

		return $service_provider;
	}

	/**
	 * Checks if the configuration is valid by running it through the validator.
	 *
	 * @since 1.1.1
	 *
	 * @param string $taxonomy_name Taxonomy name (all lowercase & no spaces)
	 * @param string|array $object_type Name of the object type for the taxonomy object
	 * @param Config_Contract $config Runtime configuration parameters
	 *
	 * @return bool
	 */
	protected function is_taxonomy_config_valid( $taxonomy_name, $object_type, Config_Contract $config ) {
		$validator = new Validator();

		return $validator->is_valid( $taxonomy_name, $object_type, $config );
	}

	/**
	 * Get the default structure for the concrete.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function get_concrete_default_structure() {
		return array(
			'autoload'      => false,
			'taxonomy_name' => '',
			'object_type'   => '',
			'config'        => '',
		);
	}
}
