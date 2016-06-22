<?php
/**
 * Theme add-on helpers
 *
 * @package     Fulcrum\Addon
 * @since       1.0.0
 * @author      hellofromTonya
 * @link        http://hellofromtonya.github.io/Fulcrum/
 * @license     GPL-2.0+
 */
namespace Fulcrum\Addon;

use Fulcrum\Config\Config;
use Fulcrum\Fulcrum;

/**
 * Let's initialize the theme.
 *
 * @since 1.0.0
 *
 * @param array|string $config
 * @param string $unique_id
 *
 * @return Theme
 */
function init_theme( $config, $unique_id = 'theme' ) {
	$fulcrum = Fulcrum::getFulcrum();

	$fulcrum[ $unique_id ] = $instance = new Theme();
	
	$instance->init( new Config( $config ), $fulcrum );

	return $instance;
}