<?php

return array(
	'autoload' => true,
	'class'    => '',
	'config'   => array(
		'shortcode' => 'shortcode_foo',
		'view'      => __DIR__ . '/views/foo.php',
		'defaults'  => array(
			'class' => 'some-class',
			'type'  => 'bar',
		),
	),
);