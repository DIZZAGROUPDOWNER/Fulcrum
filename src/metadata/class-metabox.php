<?php

/**
 * Metadata Metabox
 *
 * @package     Fulcrum\Metadata
 * @since       1.0.6
 * @author      hellofromTonya
 * @link        https://UpTechLabs.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Metadata;

use Fulcrum\Config\Config_Contract;

class Metabox implements MetaboxContract {

	/**
	 * Instance of the runtime configuration parameters
	 *
	 * @var Config_Contract
	 */
	protected $config;

	/**
	 * Array of metadata
	 *
	 * @var array
	 */
	protected $metadata = array();

	/**
	 * Post ID
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Post Type
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Post Parent's ID, if any.
	 *
	 * @var int
	 */
	protected $parent_id = 0;

	/***************************
	 * Initializers
	 **************************/

	/**
	 * Metabox constructor.
	 *
	 * @since 1.0.6
	 *
	 * @param Config_Contract $config
	 */
	public function __construct( Config_Contract $config ) {

		$this->init_post();
		$this->config  = $config;

		if ( $this->needs_this_metabox() === false ) {
			return;
		}

		$this->init_events();
	}

	/**
	 * Initialize the post.
	 *
	 * @since 1.0.6
	 *
	 * @return null
	 */
	protected function init_post() {
		$this->post_id = fulcrum_get_post_id_when_in_backend();

		if ( isset( $_GET['post'] ) ) {
			return $this->init_from_post();
		}

		if ( ! isset( $_POST['save'] ) ) {
			return;
		}

		if ( isset( $_POST['post_type'] ) ) {
			$this->post_type = strip_tags( $_POST['post_type'] );
		}

		if ( isset( $_POST['parent_id'] ) ) {
			$this->parent_id = (int) $_POST['parent_id'];
		}
	}

	/**
	 * Initialize the event hooks.
	 *
	 * @since 1.0.6
	 *
	 * @return void
	 */
	protected function init_events() {
		add_action( 'admin_menu', array( $this, 'register_metabox' ) );
		add_action( 'save_post', array( $this, 'save' ), 10, 2 );
	}

	/***************************
	 * Public Methods
	 **************************/

	/**
	 * Register a new meta box to the post or page edit screen.
	 *
	 * @since 1.0.6
	 *
	 * @retun void
	 */
	public function register_metabox() {
		$callback_args = $this->metabox_callback_args();

		add_meta_box(
			$this->config->id,
			$this->config->title,
			array( $this, 'render' ),
			$this->config->screen,
			$this->config->context,
			$this->config->priority,
			$callback_args
		);
	}

	/**
	 * Render the metabox
	 *
	 * @since 1.0.6
	 *
	 * @param WP_Post $post Instance of the post for this metabox
	 * @param array $metabox Array of metabox arguments
	 *
	 * @return void
	 */
	public function render( $post, $metabox ) {
		wp_nonce_field( $this->config->nonce_action, $this->config->nonce_name );

		$this->get_metadata( $post->ID );

		$this->pre_render( $post, $metabox );

		include( $this->config->metabox_view );
	}

	/**
	 * Save the metadata when we saving a post type.
	 *
	 * @since 1.1.2
	 *
	 * @param integer $post_id Post ID.
	 * @param stdClass $post Post object.
	 *
	 * @return void
	 */
	public function save( $post_id, $post ) {

		if ( ! wp_verify_nonce( $_POST[ $this->config->nonce_name ], $this->config->nonce_action ) ) {
			return;
		}

		foreach ( $this->config->metadata as $meta_key => $default ) {
			if ( ! isset( $_POST[ $meta_key ] ) ) {
				continue;
			}

			$data_preparation_method_name = is_array( $default )
				? 'prepare_array_metadata_for_db'
				: 'prepare_single_metadata_for_db';

			$data = $this->$data_preparation_method_name( $post_id, $post, $meta_key, $default );

			update_post_meta( $post_id, $meta_key, $data );
		}
	}

	/**
	 * Process and prepare the single metadata for saving to the database.
	 *
	 * @since 1.0.6
	 *
	 * @param integer $post_id Post ID.
	 * @param string $meta_key
	 * @param mixed $default_metadata
	 *
	 * @return void
	 */
	protected function prepare_single_metadata_for_db( $post_id, $post, $meta_key, $default_metadata ) {
		$data = fulcrum_sanitize_metadata(
				$_POST[ $meta_key ],
				$this->config->filters[ $meta_key ]
			);

		return $data;
	}

	/**
	 * Process and prepare the array metadata for saving to the database.
	 *
	 * @since 1.0.6
	 *
	 * @param integer $post_id Post ID.
	 * @param string $meta_key
	 * @param mixed $default_metadata
	 *
	 * @return void
	 */
	protected function prepare_array_metadata_for_db( $post_id, $post, $meta_key, $default_metadata ) {
		$data = array();
		foreach( $default_metadata as $meta_subkey => $default_value ) {

			$data[ $meta_subkey ] = fulcrum_sanitize_metadata(
				$_POST[ $meta_key ][ $meta_subkey ],
				$this->config->filters[ $meta_key ][ $meta_subkey ]
			);
		}

		return $data;
	}

	/***************************
	 * Prep & Helpers
	 **************************/

	/**
	 * Description.
	 *
	 * @since 1.0.6
	 *
	 * @return void
	 */
	protected function init_from_post() {
		$post = get_post( $this->post_id );
		if ( ! $post ) {
			return;
		}

		$this->post_type = $post->post_type;
		$this->parent_id = $post->post_parent;
	}

	/**
	 * Metabox callback arguments.  Extensible by
	 * the child class; else null is returned.
	 *
	 * @since 1.0.6
	 *
	 * @return mixed
	 */
	protected function metabox_callback_args() {
		return null;
	}

	/**
	 * Pre render metabox.
	 *
	 * @since 1.0.6
	 *
	 * @param WP_Post $post Instance of the post for this metabox
	 * @param array $metabox Array of metabox arguments
	 *
	 * @return void
	 */
	protected function pre_render( $post, $metabox ) {
		// child class can use this function, if needed.
	}

	/**
	 * Get metadata for this metabox.
	 *
	 * @since 1.0.6
	 *
	 * @param int $post_id
	 *
	 * @return array
	 */
	protected function get_metadata( $post_id ) {

		foreach ( $this->config->metadata as $meta_key => $defaults ) {
			$is_single = ! is_array( $defaults ) || ! empty( $defaults );
			$metadata = get_post_meta( $post_id, $meta_key, $is_single );

			$init_method = $is_single ? 'init_single_metadata' : 'init_array_metadata';

			$this->metadata[ $meta_key ] = $this->$init_method( $metadata, $defaults );
		}
	}

	/**
	 * Initialize array metadata.
	 *
	 * @since 1.0.6
	 *
	 * @param mixed $metadata
	 * @param array $defaults
	 *
	 * @return array
	 */
	protected function init_array_metadata( $metadata, $defaults ) {
		return ! empty( $metadata )
			? wp_parse_args( $metadata[0], $defaults )
			: $defaults;
	}

	/**
	 * Initialize single metadata.
	 *
	 * @since 1.0.6
	 *
	 * @param mixed $metadata
	 * @param array $defaults
	 *
	 * @return array
	 */
	protected function init_single_metadata( $metadata, $defaults ){
		return $metadata !== ''
			? $metadata
			: $defaults;
	}

	/********************
	 * Validators
	 *******************/

	/**
	 * Check to determine if this particular page needs this metabox.
	 *
	 * @since 1.0.6
	 *
	 * @return boolean
	 */
	protected function needs_this_metabox() {
		if ( $this->post_id < 1 ) {
			return false;
		}

		if ( $this->has_restrictions() ) {
			return $this->valid_restrictions();
		}
	}

	/**
	 * Check if this metabox has restrictions, meaning it
	 * can only be added to certain types of interfaces.
	 *
	 * @since 1.0.6
	 *
	 * @return bool
	 */
	protected function has_restrictions() {
		if ( ! $this->config->has( 'restrict' ) ) {
			return false;
		}

		return $this->config->is_array( 'restrict' );
	}

	/**
	 * Validates the restrictions to determine if this metabox
	 * should be built for this interface.
	 *
	 * @since 1.0.6
	 *
	 * @return bool
	 */
	protected function valid_restrictions() {
		if ( ! $this->valid_post_type() ) {
			return false;
		}

		if ( ! $this->valid_parent_post() ) {
			return false;
		}

		if ( ! $this->valid_child_post() ) {
			return false;
		}
	}

	/**
	 * Checks if the post type is valid for this metabox.
	 *
	 * @since 1.0.6
	 *
	 * @return bool
	 */
	protected function valid_post_type() {
		if ( ! $this->config->has( 'restrict.post_type' ) ) {
			return true;
		}

		if ( ! $this->post_type ) {
			return false;
		}

		return in_array( $this->post_type, $this->config->restrict['post_type'] );
	}

	/**
	 * Validates if the interface is a parent post.
	 *
	 * @since 1.0.6
	 *
	 * @return bool
	 */
	protected function valid_parent_post() {
		if ( ! $this->config->has( 'restrict.parent_post' ) ) {
			return true;
		}

		return $this->parent_id === 0;
	}

	/**
	 * Validates if the interface is a child post.
	 *
	 * @since 1.0.6
	 *
	 * @return bool
	 */
	protected function valid_child_post() {
		if ( ! $this->config->has( 'restrict.child_post' ) ) {
			return true;
		}

		return $this->parent_id > 0;
	}
}
