<?php

/**
 * Admin Helpers
 *
 * @package     Fulcrum\Support\Helpers
 * @since       1.1.1
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */
namespace Fulcrum\Support\Helpers;

add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\temporary_admin_chrome_fix' );
/**
 * Temporary admin fix for Chrome per Trac ticket
 *
 * @since       1.1.1
 *
 * @link https://core.trac.wordpress.org/ticket/33199
 * @return null
 */
function temporary_admin_chrome_fix() {
	if ( false !== strpos( $_SERVER['HTTP_USER_AGENT'], 'Chrome' ) ) {
		wp_add_inline_style( 'wp-admin', '#adminmenu { transform: translateZ(0); }' );
	}
}


/**
 * Fetches the post ID in the admin area from the $_REQUEST.
 *
 * @since 1.0.0
 *
 * @return int
 */
function get_post_id_in_admin() {
	if ( ! is_admin() ) {
		return 0;
	}

	if ( array_key_exists( 'post_ID', $_REQUEST ) ) {
		return (int) $_REQUEST['post_ID'];
	}

	if ( array_key_exists( 'post', $_REQUEST ) ) {
		return (int) $_REQUEST['post'];
	}

	return 0;
}

/**
 * Is the front page in the admin back-end.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function is_admin_front_page() {
	if ( ! is_admin() ) {
		return false;
	}

	$post_id = get_post_id_in_admin();
	if ( ! $post_id ) {
		return false;
	}

	return get_option( 'page_on_front', 0 ) == $post_id;
}
