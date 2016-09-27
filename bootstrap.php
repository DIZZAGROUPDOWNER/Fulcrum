<?php
/**
 * Fulcrum Plugin
 *
 * @package         Fulcrum
 * @author          hellofromTonya
 * @license         GPL-2.0+
 * @link            https://UpTechLabs.io
 *
 * @wordpress-plugin
 * Plugin Name:     Fulcrum Plugin
 * Plugin URI:      https://github.com/hellofromtonya/Fulcrum
 * Description:     Fulcrum - The customization central repository to extend and custom WordPress. This plugin provides the centralized infrastructure for the custom plugins and theme.
 * Version:         1.1.2
 * Author:          hellofromTonya
 * Author URI:      https://UpTechLabs.io
 * Text Domain:     fulcrum
 * Requires WP:     4.5
 * Requires PHP:    5.4
 */

/*
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

namespace Fulcrum;

use Fulcrum\Config\Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Cheatin&#8217; uh?' );
}

require_once( __DIR__ . '/assets/vendor/autoload.php' );

fulcrum_declare_plugin_constants( 'FULCRUM', __FILE__ );

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	$config = __DIR__ . '/config/plugin.php';

	return new Fulcrum(
		new Config( $config )
	);
}

add_filter( 'pre_update_option_rewrite_rules', __NAMESPACE__ . '\init_plugin_rewrites_and_flush', 1 );
/**
 * If a flush_rewrite_rules is in process, we run our rewrite event
 * to ensure the rewrite rules and tasks are included.
 *
 * Why not use the activation/deactivation process?  There are far too
 * many places in WordPress where flush_rewrite_rules() is triggered.
 * When that happens, it may not include our rewrite rules because of the
 * way we are adding them. To account for all possible scenarios (like
 * saving permalinks, upgrade, and plugin management), we are registered
 * to the actual flush_rewrite_rules mechanism.
 *
 * @since 1.0.0
 *
 * @param mixed $value Rewrite rules value
 *
 * @return mixed
 */
function init_plugin_rewrites_and_flush( $value) {
	if ( ! $value ) {
		do_action( 'fulcrum_init_rewrites' );
	}
	return $value;
}
