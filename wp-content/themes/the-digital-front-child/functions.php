<?php
/**
 * The Digital Front Child Theme functions and definitions.
 */

function tdf_child_enqueue_styles() {
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'parent-style' ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'tdf_child_enqueue_styles' );

/**
	 * Register Review custom post type
*/


function tdf_register_review_cpt() {

	
	$labels = [
		"name" => esc_html__( "Reviews", "the-digital-front-child" ),
		"singular_name" => esc_html__( "Review", "the-digital-front-child" ),
	];

	$args = [
		"label" => esc_html__( "Reviews", "the-digital-front-child" ),
		"labels" => $labels,
		"description" => "The main content type for publishing reviews on The Digital Front.",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"rest_namespace" => "wp/v2",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"can_export" => false,
		"rewrite" => [ "slug" => "review", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail", "excerpt", "custom-fields" ],
		"show_in_graphql" => false,
	];

	register_post_type( "review", $args );
}

add_action( 'init', 'tdf_register_review_cpt' );

/**
    * Register ACF fields for Review post type
 */

 function tdf_register_review_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
	'key' => 'group_69bd4f0d9c1fc',
	'title' => 'Review',
	'fields' => array(
		array(
			'key' => 'field_69bd4f0f82782',
			'label' => 'rating',
			'name' => 'rating',
			'aria-label' => '',
			'type' => 'number',
			'instructions' => '',
			'required' => false,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'min' => '',
			'max' => '',
			'step' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_69bd4fd682783',
			'label' => 'product_name',
			'name' => 'product_name',
			'aria-label' => '',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'maxlength' => '',
			'allow_in_bindings' => 0,
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
		),
		array(
			'key' => 'field_69bd514482784',
			'label' => 'image_gallery',
			'name' => 'image_gallery',
			'aria-label' => '',
			'type' => 'image',
			'instructions' => '',
			'required' => false,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'preview_size' => 'medium',
			'library' => 'all',
			'min_width' => 0,
			'min_height' => 0,
			'min_size' => 0,
			'max_width' => 0,
			'max_height' => 0,
			'max_size' => 0,
			'mime_types' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'review',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
	'display_title' => '',
) );
}

add_action('act/init', 'tdf_register_review_fields');


/**
    * Create trending in tech page
 */

add_action('init', 'create_trending_in_tech_page');

function create_trending_in_tech_page() {

    // Check if page already exists
    $page = get_page_by_path('trending-in-tech');

    if (!$page) {
        wp_insert_post([
            'post_title'   => 'Trending in Tech',
            'post_name'    => 'trending-in-tech', 
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => 'This is the Trending in Tech page.',
        ]);
    }

}



