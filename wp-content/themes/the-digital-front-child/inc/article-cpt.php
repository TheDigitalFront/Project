<?php
/**
 * Article Custom Post Type registration (Phase 2A).
 *
 * Registers the 'article' CPT — the primary content type for The Digital Front.
 * Supports the block editor, featured images, excerpts, and REST API.
 * The 'category' taxonomy is attached so articles can be filtered on the front end.
 *
 * Archive is enabled at /article/ and uses archive-article.php.
 *
 * Satisfies requirement R1 (custom post types with custom fields).
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

/**
 * Register the 'article' custom post type.
 *
 * @hooked init
 * @return void
 */
function tdf_register_article_cpt() {
	$labels = [
		'name'                     => esc_html__( 'Articles', 'the-digital-front-child' ),
		'singular_name'            => esc_html__( 'Article', 'the-digital-front-child' ),
		'menu_name'                => esc_html__( 'My Articles', 'the-digital-front-child' ),
		'all_items'                => esc_html__( 'All Articles', 'the-digital-front-child' ),
		'add_new'                  => esc_html__( 'Add New', 'the-digital-front-child' ),
		'add_new_item'             => esc_html__( 'Add New Article', 'the-digital-front-child' ),
		'edit_item'                => esc_html__( 'Edit Article', 'the-digital-front-child' ),
		'new_item'                 => esc_html__( 'Add Article', 'the-digital-front-child' ),
		'view_item'                => esc_html__( 'View Article', 'the-digital-front-child' ),
		'view_items'               => esc_html__( 'View Articles', 'the-digital-front-child' ),
		'search_items'             => esc_html__( 'Search Articles', 'the-digital-front-child' ),
		'not_found'                => esc_html__( 'No Articles Found', 'the-digital-front-child' ),
		'not_found_in_trash'       => esc_html__( 'No Articles Found in Trash', 'the-digital-front-child' ),
		'parent'                   => esc_html__( 'Parent Article', 'the-digital-front-child' ),
		'featured_image'           => esc_html__( 'Featured image for this article', 'the-digital-front-child' ),
		'set_featured_image'       => esc_html__( 'Set featured image for this article', 'the-digital-front-child' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image for this article', 'the-digital-front-child' ),
		'use_featured_image'       => esc_html__( 'Use as featured image for this article', 'the-digital-front-child' ),
		'archives'                 => esc_html__( 'Article Archives', 'the-digital-front-child' ),
		'insert_into_item'         => esc_html__( 'Insert into article', 'the-digital-front-child' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this article', 'the-digital-front-child' ),
		'filter_items_list'        => esc_html__( 'Filter articles list', 'the-digital-front-child' ),
		'items_list_navigation'    => esc_html__( 'Article List Navigation', 'the-digital-front-child' ),
		'items_list'               => esc_html__( 'Articles List', 'the-digital-front-child' ),
		'attributes'               => esc_html__( 'Articles Attributes', 'the-digital-front-child' ),
		'name_admin_bar'           => esc_html__( 'Article', 'the-digital-front-child' ),
		'item_published'           => esc_html__( 'Article published.', 'the-digital-front-child' ),
		'item_published_privately' => esc_html__( 'Article published privately.', 'the-digital-front-child' ),
		'item_reverted_to_draft'   => esc_html__( 'Article reverted to draft.', 'the-digital-front-child' ),
		'item_trashed'             => esc_html__( 'Article trashed.', 'the-digital-front-child' ),
		'item_scheduled'           => esc_html__( 'Article scheduled.', 'the-digital-front-child' ),
		'item_updated'             => esc_html__( 'Article updated.', 'the-digital-front-child' ),
		'template_name'            => esc_html__( 'Single Item: Article', 'the-digital-front-child' ),
		'parent_item_colon'        => esc_html__( 'Parent Article', 'the-digital-front-child' ),
	];

	register_post_type( 'article', [
		'label'                 => esc_html__( 'Articles', 'the-digital-front-child' ),
		'labels'                => $labels,
		'description'           => 'The main content type for publishing written content on The Digital Front.',
		'public'                => true,
		'publicly_queryable'    => true,
		'show_ui'               => true,
		'show_in_rest'          => true,
		'rest_base'             => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'        => 'wp/v2',
		'has_archive'           => true,         // Archive at /article/.
		'show_in_menu'          => true,
		'show_in_nav_menus'     => true,
		'delete_with_user'      => false,
		'exclude_from_search'   => false,
		'capability_type'       => 'post',       // Uses standard post capabilities.
		'map_meta_cap'          => true,
		'hierarchical'          => false,
		'can_export'            => false,
		'rewrite'               => [ 'slug' => 'article', 'with_front' => true ],
		'query_var'             => true,
		'menu_icon'             => 'dashicons-admin-page',
		'supports'              => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'taxonomies'            => [ 'category' ], // Share WP categories for filtering.
		'show_in_graphql'       => false,
	] );
}
add_action( 'init', 'tdf_register_article_cpt' );
