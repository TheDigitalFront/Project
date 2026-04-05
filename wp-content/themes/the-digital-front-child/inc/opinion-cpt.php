<?php

/* Opinion Custom Post Type registration
 * Registers the 'opinion' CPT for editorial opinion pieces on The Digital Front.
 * Supports the block editor, featured images, excerpts, and REST API.
 * Archive is disabled — opinions appear only under their related article.
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

/**
 * Register the 'opinion' custom post type.
 * @hooked init
 * @return void
 */
function tdf_register_opinion_cpt()
{
	$labels = [
		'name'               => esc_html__('Opinions', 'the-digital-front-child'),
		'singular_name'      => esc_html__('Opinion', 'the-digital-front-child'),
		'menu_name'          => esc_html__('Opinions', 'the-digital-front-child'),
		'all_items'          => esc_html__('All Opinions', 'the-digital-front-child'),
		'add_new'            => esc_html__('Add New', 'the-digital-front-child'),
		'add_new_item'       => esc_html__('Add New Opinion', 'the-digital-front-child'),
		'edit_item'          => esc_html__('Edit Opinion', 'the-digital-front-child'),
		'new_item'           => esc_html__('New Opinion', 'the-digital-front-child'),
		'view_item'          => esc_html__('View Opinion', 'the-digital-front-child'),
		'search_items'       => esc_html__('Search Opinions', 'the-digital-front-child'),
		'not_found'          => esc_html__('No Opinions Found', 'the-digital-front-child'),
		'not_found_in_trash' => esc_html__('No Opinions Found in Trash', 'the-digital-front-child'),
	];

	register_post_type('opinion', [
		'label'                 => esc_html__('Opinions', 'the-digital-front-child'),
		'labels'                => $labels,
		'description'           => 'Editorial opinion pieces published on The Digital Front.',
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_rest'          => true,
		'rest_base'             => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'        => 'wp/v2',
		'has_archive'           => false, /* opinions are not standalone — they only appear linked under their related article */
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'delete_with_user'      => false,
		'exclude_from_search'   => false,
		'capability_type'       => 'post',
		'map_meta_cap'          => true,
		'hierarchical'          => false,
		'can_export'            => false,
		'rewrite'               => ['slug' => 'opinion', 'with_front' => true],
		'query_var'             => true,
		'supports'              => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
		'taxonomies'            => ['post_tag'], /* attaches the standard WP tag taxonomy so tags appear in the editor sidebar and Query 3 can match shared tags */
		'show_in_graphql'       => false,
	]);
}
add_action('init', 'tdf_register_opinion_cpt');
