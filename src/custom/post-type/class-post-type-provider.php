<?php

/**
 * Post Type Service Provider
 *
 * @package     Fulcrum\Custom\Post_Type
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */

namespace Fulcrum\Custom\Post_Type;

use Fulcrum\Config\Config_Contract;
use Fulcrum\Foundation\Service_Provider\Provider;
use Fulcrum\Config\Config;

class Post_Type_Provider extends Provider {

	/**
	 * Flag to indicate whether to skip the queue and register directly into the Container.
	 *
	 * @var bool
	 */
	protected $skip_queue = true;

	/**
	 * Get the concrete based upon the configuration supplied.
	 *
	 * @since 1.1.1
	 *
	 * @param array $config Runtime configuration parameters.
	 * @param string $unique_id Container's unique key ID for this instance.
	 *
	 * @return array
	 */
	public function get_concrete( array $config, $unique_id = '' ) {

		$service_provider = array(
			'autoload' => $config['autoload'],
			'concrete' => function ( $container ) use ( $config, $unique_id ) {
				$config_obj = $this->instantiate_config( $config );

				if ( ! $this->is_post_type_config_valid( $config_obj, $config['post_type_name'] ) ) {
					return;
				}

				$post_type = new Post_Type(
					$config_obj,
					$config['post_type_name'],
					new Post_Type_Supports( $config_obj )
				);

				$container[ $unique_id . '_feed' ] = new Feed( $config_obj, $config['post_type_name'] );

				return $post_type;
			},
		);

		return $service_provider;
	}

	/**
	 * Checks if the post type's configuration is valid by running it through the validator.
	 *
	 * @since 1.1.1
	 *
	 * @param $config
	 * @param $post_type
	 *
	 * @return bool
	 */
	protected function is_post_type_config_valid( $config, $post_type ) {
		$validator = new Validator();

		return $validator->is_valid( $config, $post_type );
	}

	/**
	 * Flush rewrite rules for custom post type.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function flush_rewrite_rules() {
		foreach ( $this->unique_ids as $unique_id ) {
			$this->fulcrum[ $unique_id ]->register();
		}

		flush_rewrite_rules();
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
			'autoload'                  => false,
			'post_type_name'            => '',
			'config'                    => array(),
			'enable_permalink_handlers' => false,
			'permalink_handlers'        => array(),
		);
	}
}
