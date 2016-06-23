<?php
namespace Fulcrum\Test\Custom\Widget;



use Fulcrum\Config\Config;

return array(
	'register_concretes' => array(
		'Fulcrum\Test\Custom\Widget\Foo_Widget' => array(
			'autoload' => false,
			'concrete' => function ( $container ) {
	
				return new Config( FULCRUM_TESTS_DIR . 'custom/widget/foo-config.php' );
			}
		),
	),
	
	'widget.foo' => array(
		'provider' => 'provider.widget',
		'config'   => array(
			'Fulcrum\Test\Custom\Widget\Foo_Widget',
		),
	),
);