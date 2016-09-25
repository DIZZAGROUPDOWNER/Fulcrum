<?php
/**
 * Database helpers
 *
 * @package     Fulcrum\Support\Helpers
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        https://UpTechLabs.io
 * @license     GNU General Public License 2.0+
 */
namespace Fulcrum\Support\Helpers;

if ( ! function_exists( 'hard_get_option_value' ) ) {
	/**
	 * Gets the option value from the `wp_options` database.  This is a hard
	 * get, as it queries the database directly to avoid any caching.
	 *
	 * @since 1.0.0
	 *
	 * @param string $option_name
	 * @param int $default_value Default value to return if the option does not
	 * exist.  The default value is 0.
	 *
	 * @return int|null|string
	 */
	function hard_get_option_value( $option_name, $default_value = 0 ) {
		global $wpdb;

		$sql_query = $wpdb->prepare(
			"
				SELECT option_value
				FROM {$wpdb->prefix}options
				WHERE option_name=%s
			", $option_name
		);

		$option_value = $wpdb->get_var( $sql_query );
		
		return $option_name === null ? $default_value : $option_value; 
	}
}
