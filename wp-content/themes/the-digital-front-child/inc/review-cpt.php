<?php
/**
 * Review Custom Post Type registration (Phase 2C).
 *
 * Registers the 'review' CPT for product and tech reviews on The Digital Front.
 * Supports the block editor, featured images, excerpts, and REST API.
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

/**
 * Register the 'review' custom post type.
 *
 * @hooked init
 * @return void
 */
function tdf_register_review_cpt() {
	$labels = [
		'name'               => esc_html__( 'Reviews', 'the-digital-front-child' ),
		'singular_name'      => esc_html__( 'Review', 'the-digital-front-child' ),
		'menu_name'          => esc_html__( 'Reviews', 'the-digital-front-child' ),
		'all_items'          => esc_html__( 'All Reviews', 'the-digital-front-child' ),
		'add_new'            => esc_html__( 'Add New', 'the-digital-front-child' ),
		'add_new_item'       => esc_html__( 'Add New Review', 'the-digital-front-child' ),
		'edit_item'          => esc_html__( 'Edit Review', 'the-digital-front-child' ),
		'new_item'           => esc_html__( 'New Review', 'the-digital-front-child' ),
		'view_item'          => esc_html__( 'View Review', 'the-digital-front-child' ),
		'search_items'       => esc_html__( 'Search Reviews', 'the-digital-front-child' ),
		'not_found'          => esc_html__( 'No Reviews Found', 'the-digital-front-child' ),
		'not_found_in_trash' => esc_html__( 'No Reviews Found in Trash', 'the-digital-front-child' ),
	];

	register_post_type( 'review', [
		'label'                 => esc_html__( 'Reviews', 'the-digital-front-child' ),
		'labels'                => $labels,
		'description'           => 'The main content type for publishing reviews on The Digital Front.',
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_rest'          => true,
		'rest_base'             => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'        => 'wp/v2',
		'has_archive'           => false,
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'delete_with_user'      => false,
		'exclude_from_search'   => false,
		'capability_type'       => 'post',
		'map_meta_cap'          => true,
		'hierarchical'          => false,
		'can_export'            => false,
		'rewrite'               => [ 'slug' => 'review', 'with_front' => true ],
		'query_var'             => true,
		'supports'              => [ 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ],
		'show_in_graphql'       => false,
	] );
}
add_action( 'init', 'tdf_register_review_cpt' );
