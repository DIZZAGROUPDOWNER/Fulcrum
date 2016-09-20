<?php

/**
 * Metadata Metabox Contract
 *
 * @package     Fulcrum\Metadata
 * @since       1.1.2
 * @author      hellofromTonya
 * @link        https://UpTechLabs.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Metadata;

interface MetaboxContract {
	/**
	 * Register a new meta box to the post or page edit screen.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function register_metabox();

	/**
	 * Render the metabox
	 *
	 * @since 1.1.2
	 *
	 * @param WP_Post $post Instance of the post for this metabox
	 * @param array $metabox Array of metabox arguments
	 *
	 * @return void
	 */
	public function render( $post, $metabox );

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
	public function save( $post_id, $post );

}
