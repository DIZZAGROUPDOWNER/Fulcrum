<?php


return array(

	/*********************
	 * Widget Construct Args
	 ********************/
	'id_base'         => 'foo-widget',
	'name'            => __( 'Foo', 'fulcrum' ),
	'widget_options'  => array(
		'classname'   => 'foo-widget',
		'description' => __( 'Testing Foo.', 'fulcrum' ),
	),
	'control_options' => array(
		'width'  => 400,
		'height' => 350,
	),

	/*********************
	 * Defaults
	 ********************/

	'defaults' => array(
		'class' => '',
	),

	/*********************
	 * Views
	 ********************/

	'view'      => FULCRUM_TESTS_DIR . 'custom/widget/views/foo.php',
	'form_view' => FULCRUM_TESTS_DIR . 'custom/widget/views/foo-form.php',
);