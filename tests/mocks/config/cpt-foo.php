<?php

return array(
	'plural_name'           => 'Foo',
	'singular_name'         => 'Foo',
	'args'                  => array(
		'public'       => true,
		'hierarchical' => false,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-video-alt',
		'rewrite'      => array(
			'slug'       => 'foo',
			'with_front' => false,
		),
	),
	'labels'                => array(
		'archive' => 'Labs',
	),
	'additional_supports'   => array(
		'author'          => false,
		'comments'        => false,
		'excerpt'         => true,
		'post-formats'    => false,
		'trackbacks'      => false,
		'custom-fields'   => false,
		'revisions'       => false,
		'page-attributes' => true,
		'ktc_library'     => true,
	),
	'rewrite_with_taxonomy' => false,

	'columns_filter' => array(
		'cb'     => true,
		'title'  => 'Foo Title',
		'author' => 'Author',
		'date'   => 'Date',
	),
	'columns_data'   => array(
		'date' => array(
			'callback' => 'the_date',
			'echo'     => false,
			'args'     => array(),
		),
	),
);
