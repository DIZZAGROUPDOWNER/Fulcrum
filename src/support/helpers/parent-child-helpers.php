<?php

/**
 * General Parent/Children Functions
 *
 * @package     Fulcrum
 * @since       1.1.2
 * @author      hellofromTonya
 * @link        https://UpTechLabs.io
 * @license     GNU General Public License 2.0+
 */

if ( ! function_exists( 'fulcrum_is_child_post' ) ) {
	/**
	 * Checks if the given post is a child.
	 *
	 * @since 1.1.2
	 *
	 * @param int|WP_Post|null $post_or_post_id Post Instance or Post ID to check
	 *
	 * @return boolean|null
	 */
	function fulcrum_is_child_post( $post_or_post_id = null ) {
		$post = get_post( $post_or_post_id );
		if ( ! $post || is_wp_error( $post ) ) {
			return null;
		}

		return $post->post_parent > 0;
	}
}

if ( ! function_exists( 'fulcrum_is_parent_post' ) ) {
	/**
	 * Checks if the given post is a parent.
	 *
	 * @since 1.1.2
	 *
	 * @param int|WP_Post|null $post_or_post_id Post Instance or Post ID to check
	 *
	 * @return boolean|null
	 */
	function fulcrum_is_parent_post( $post_or_post_id = null ) {
		$post = get_post( $post_or_post_id );
		if ( ! $post || is_wp_error( $post ) ) {
			return null;
		}

		return $post->post_parent === 0;
	}
}

if ( ! function_exists( 'fulcrum_post_has_children' ) ) {
	/**
	 * Checks if the given post has children
	 *
	 * @since 1.1.2
	 *
	 * @param int|WP_Post|null $post_or_post_id Post Instance or Post ID to check
	 *
	 * @return boolean|null
	 */
	function fulcrum_post_has_children( $post_or_post_id = null ) {
		$number_of_children = fulcrum_get_number_of_children( $post_or_post_id );

		return $number_of_children > 0;
	}
}

if ( ! function_exists( 'fulcrum_get_number_of_children' ) ) {
	/**
	 * Fetches the number of children for a given post or post ID.
	 * If no post/post ID is passed in, then it uses the current post.
	 *
	 * @since 1.1.2
	 *
	 * @param int|WP_Post|null $post_or_post_id Post Instance or Post ID to check
	 *
	 * @return int|false
	 */
	function fulcrum_get_number_of_children( $post_or_post_id = null ) {
		$post_id = fulcrum_extract_post_id( $post_or_post_id );
		if ( $post_id < 1 ) {
			return false;
		}

		global $wpdb;

		$sql_query = $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_parent = %d", $post_id );

		$number_of_children = $wpdb->get_var( $sql_query );

		return (int) $number_of_children;
	}
}

/**
 * Get the next adjacent parent post.
 *
 * This function extends the SQL WHERE query of the WordPress get_adjacent_post()
 * function. It registers a callback to the `get_next_post_where` event filter,
 * which then adds a new WHERE parameter.
 *
 * @uses get_next_post()
 * @uses `get_next_post_where` filter
 * @uses fulcrum_add_parent_post_to_adjacent_sql()
 *
 * @since 1.1.2
 *
 * @param bool         $in_same_term   Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Array or comma-separated list of excluded term IDs. Default empty.
 * @param string       $taxonomy       Optional. Taxonomy, if $in_same_term is true. Default 'category'.
 *
 * @return null|string|WP_Post Post object if successful. Null if global $post is not set. Empty string if no
 *                             corresponding post exists.
 */
function fulcrum_get_next_parent_post( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category') {
	add_filter( 'get_next_post_where', 'fulcrum_add_parent_post_to_adjacent_sql' );

	return get_next_post( $in_same_term, $excluded_terms, $taxonomy );
}

/**
 * Get the previous adjacent parent post.
 *
 * This function extends the SQL WHERE query of the WordPress get_adjacent_post()
 * function. It registers a callback to the `get_previous_post_where` event filter,
 * which then adds a new WHERE parameter.
 *
 * @uses get_previous_post()
 * @uses `get_previous_post_where` filter
 * @uses fulcrum_add_parent_post_to_adjacent_sql()
 *
 * @since 1.1.2
 *
 * @param bool         $in_same_term   Optional. Whether post should be in a same taxonomy term. Default false.
 * @param array|string $excluded_terms Optional. Array or comma-separated list of excluded term IDs. Default empty.
 * @param string       $taxonomy       Optional. Taxonomy, if $in_same_term is true. Default 'category'.
 *
 * @return null|string|WP_Post Post object if successful. Null if global $post is not set. Empty string if no
 *                             corresponding post exists.
 */
function fulcrum_get_previous_parent_post( $in_same_term = false, $excluded_terms = '', $taxonomy = 'category') {
	add_filter( 'get_previous_post_where', 'fulcrum_add_parent_post_to_adjacent_sql' );

	return get_previous_post( $in_same_term, $excluded_terms, $taxonomy );
}

/**
 * Adds a post parent WHERE SQL check to the adjacent SQL.
 *
 * In WordPress, the column `post_parent` is 0 when the content is
 * the root parent.
 *
 * Callback for the WordPress filter events `get_previous_post_where` and
 * `get_next_post_where`.
 *
 * @since 1.1.2
 *
 * @param string $where_sql
 *
 * @return string
 */
function fulcrum_add_parent_post_to_adjacent_sql( $where_sql ) {
	$where_sql .= ' AND p.post_parent = 0';

	return $where_sql;
}

/**
 * Get the post ID from the given post or post ID.
 * If none is passed in, then it grabs the current ID.
 *
 * @since 1.0.0
 *
 * @param WP_Post|int|null $post_or_post_id Given post or post ID
 *
 * @return int
 */
function fulcrum_extract_post_id( $post_or_post_id = null ) {
	if ( is_object( $post_or_post_id ) ) {
		return (int) $post_or_post_id->ID;
	}

	if ( $post_or_post_id > 0 ) {
		return (int) $post_or_post_id;
	}

	return get_the_ID();
}
