<?php
/**
 * Description
 *
 * @package     ${NAMESPACE}
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://knowthecode.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Custom\Post_Type;

use Fulcrum\Config\Config_Contract;

class Feed {

	/**
	 * Configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;
	
	/**
	 * Internal flag if the query_vars has a post_type key
	 *
	 * @var bool
	 */
	protected $query_vars_has_post_types = false;


	/****************************
	 * Instantiate & Initialize
	 ***************************/

	/**
	 * Instantiate the Feed for a Custom Post Type
	 *
	 * @since 1.0.0
	 *
	 * @param Config_Contract $config Runtime configuration parameters.
	 * @param string $post_type_name Post type name (all lowercase & no spaces)
	 */
	public function __construct( Config_Contract $config, $post_type_name ) {
		$this->config    = $config;
		$this->post_type = $post_type_name;

		if ( true !== $this->config->add_feed ) {
			$this->config->add_feed = false;
		}

		$this->init_events();
	}

	/**
	 * Initialize the event.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function init_events() {
		add_filter( 'request', array( $this, 'add_or_remove_to_from_rss_feed' ) );
	}

	/**
	 * Handles adding (or removing) this CPT to/from the RSS Feed
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars Query variables from parse_request
	 *
	 * @return array $query_vars
	 */
	public function add_or_remove_to_from_rss_feed( $query_vars ) {
		if ( ! isset( $query_vars['feed'] ) ) {
			return $query_vars;
		}

		$this->add_or_remove_post_type_tofrom_feed_handler( $query_vars );

		return $query_vars;
	}

	/**
	 * Checks whether to add or remove the post type from feed. If yes,
	 * then it either adds or removes it.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars
	 */
	protected function add_or_remove_post_type_tofrom_feed_handler( &$query_vars ) {
		$post_type_index = false;

		if ( ! $this->is_post_type_in_query_var( $query_vars ) && $this->query_vars_has_post_types ) {
			$post_type_index = array_search( $this->post_type, (array) $query_vars['post_type'] );
		}

		if ( $this->is_set_to_add_to_feed( $post_type_index ) ) {
			return $this->add_post_type_to_feed( $query_vars );
		}

		if ( $this->is_set_to_remove_from_feed( $post_type_index ) ) {
			return $this->remove_post_type_from_feed( $query_vars, $post_type_index );
		}
	}

	/**
	 * Add post type to the feed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars
	 *
	 * @return void
	 */
	protected function add_post_type_to_feed( array &$query_vars ) {
		if ( ! $this->query_vars_has_post_types ) {
			$query_vars['post_type'] = array( 'post', $this->post_type );
		} else {
			$query_vars['post_type'][] = $this->post_type;
		}
	}

	/**
	 * Remove the post type from the feed.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars
	 * @param bool|int $post_type_index
	 *
	 * @return void
	 */
	protected function remove_post_type_from_feed( array &$query_vars, $post_type_index ) {
		unset( $query_vars['post_type'][ $post_type_index ] );

		$query_vars['post_type'] = array_values( $query_vars['post_type'] );
	}

	/**
	 * Checks if this post type is in the `$query_vars['post_type']`.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars
	 *
	 * @return bool
	 */
	protected function is_post_type_in_query_var( array $query_vars ) {
		if ( ! $this->does_query_vars_have_post_types( $query_vars ) ) {
			return false;
		}

		return in_array( $this->post_type, (array) $query_vars['post_type'] );
	}

	/**
	 * Checks if the query_vars already has `post_type` key and it is an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $query_vars
	 *
	 * @return bool
	 */
	public function does_query_vars_have_post_types( array $query_vars ) {
		$this->query_vars_has_post_types = array_key_exists( 'post_type', $query_vars ) && is_array( $query_vars['post_type'] );

		return $this->query_vars_has_post_types;
	}

	/**
	 * Checks if conditions are set to add the custom post type from the feed.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|int $index
	 *
	 * @return bool
	 */
	protected function is_set_to_add_to_feed( $index ) {
		return false === $index && $this->config->add_feed;
	}

	/**
	 * Checks if conditions are set to remove the custom post type from the feed.
	 *
	 * @since 1.0.0
	 *
	 * @param bool|int $index
	 *
	 * @return bool
	 */
	protected function is_set_to_remove_from_feed( $index ) {
		return $this->query_vars_has_post_types &&
		       false !== $index &&
		       ! $this->config->add_feed;
	}
}