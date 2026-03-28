<?php
/**
 * ACF configuration — JSON sync and Article field group (Phase 2A).
 *
 * ACF field groups are saved to / loaded from the child theme's acf-json/
 * directory so they are version-controlled alongside theme code.
 *
 * The Article field group is also registered in PHP (not just ACF GUI)
 * so fields exist on every environment without a database import.
 *
 * Satisfies requirement R2 (each CPT features 1-2 kinds of digital content).
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

// ─── ACF JSON Sync ───────────────────────────────────────────────

/**
 * Override ACF's JSON save path to the child theme's acf-json/ directory.
 *
 * @hooked acf/settings/save_json
 * @param  string $path Default save path.
 * @return string       Child theme acf-json/ directory.
 */
function tdf_acf_json_save_point( $path ) {
	return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'tdf_acf_json_save_point' );

/**
 * Add the child theme's acf-json/ directory as an ACF JSON load point.
 *
 * @hooked acf/settings/load_json
 * @param  array $paths Existing load paths.
 * @return array        Paths with child theme directory appended.
 */
function tdf_acf_json_load_point( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'tdf_acf_json_load_point' );

// ─── Article Field Group ─────────────────────────────────────────

/**
 * Register ACF field group for the Article post type.
 *
 * Fields:
 *   - video_embed   (text)   — YouTube/Vimeo embed URL for multimedia content.
 *   - reading_time  (number) — Estimated reading time in minutes, min 1.
 *   - source_url    (url)    — Link to the original source article.
 *
 * @hooked acf/init
 * @return void
 */
function tdf_register_article_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return; // ACF plugin not active — skip gracefully.
	}

	acf_add_local_field_group( [
		'key'             => 'group_article_fields',
		'title'           => 'Article Fields',
		'show_in_rest'    => true,
		'fields'          => [
			[
				'key'          => 'field_video_embed',
				'label'        => 'Video Embed',
				'name'         => 'video_embed',
				'type'         => 'text',
				'instructions' => 'Paste a video embed URL (YouTube, Vimeo, etc.)',
				'required'     => 0,
			],
			[
				'key'          => 'field_reading_time',
				'label'        => 'Reading Time',
				'name'         => 'reading_time',
				'type'         => 'number',
				'instructions' => 'Estimated reading time in minutes.',
				'required'     => 0,
				'min'          => 1,
				'append'       => 'min',
			],
			[
				'key'          => 'field_source_url',
				'label'        => 'Source URL',
				'name'         => 'source_url',
				'type'         => 'url',
				'instructions' => 'Link to the original source.',
				'required'     => 0,
			],
		],
		'location'        => [ [ [
			'param'    => 'post_type',
			'operator' => '==',
			'value'    => 'article',
		] ] ],
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
	] );
}
add_action( 'acf/init', 'tdf_register_article_fields' );

// ─── Opinion Field Group ────────────────────────────────────────

/**
 * Register ACF field group for the Opinion post type.
 *
 * Fields:
 *   - author_bio       (textarea)     — Short bio for the opinion author.
 *   - pull_quote       (textarea)     — Highlighted pull quote from the piece.
 *   - related_article  (relationship) — Link to a related Article post.
 *
 * @hooked acf/init
 * @return void
 */
function tdf_register_opinion_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_opinion_fields',
		'title'           => 'Opinion Fields',
		'show_in_rest'    => true,
		'fields'          => [
			[
				'key'      => 'field_author_bio',
				'label'    => 'Author Bio',
				'name'     => 'author_bio',
				'type'     => 'textarea',
				'required' => 0,
			],
			[
				'key'      => 'field_pull_quote',
				'label'    => 'Pull Quote',
				'name'     => 'pull_quote',
				'type'     => 'textarea',
				'required' => 0,
			],
			[
				'key'       => 'field_related_article',
				'label'     => 'Related Article',
				'name'      => 'related_article',
				'type'      => 'relationship',
				'post_type' => [ 'article' ],
				'required'  => 0,
			],
		],
		'location'        => [ [ [
			'param'    => 'post_type',
			'operator' => '==',
			'value'    => 'opinion',
		] ] ],
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
	] );
}
add_action( 'acf/init', 'tdf_register_opinion_fields' );

// ─── Review Field Group ─────────────────────────────────────────

/**
 * Register ACF field group for the Review post type.
 *
 * Fields:
 *   - rating        (number) — Product rating from 1 to 5.
 *   - product_name  (text)   — Name of the product being reviewed.
 *   - product_image (image)  — Product image for the review.
 *
 * @hooked acf/init
 * @return void
 */
function tdf_register_review_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( [
		'key'             => 'group_review_fields',
		'title'           => 'Review Fields',
		'show_in_rest'    => true,
		'fields'          => [
			[
				'key'          => 'field_review_rating',
				'label'        => 'Rating',
				'name'         => 'rating',
				'type'         => 'number',
				'instructions' => 'Product rating (1–5).',
				'required'     => 0,
				'min'          => 1,
				'max'          => 5,
				'step'         => 1,
			],
			[
				'key'      => 'field_review_product_name',
				'label'    => 'Product Name',
				'name'     => 'product_name',
				'type'     => 'text',
				'required' => 0,
			],
			[
				'key'            => 'field_review_product_image',
				'label'          => 'Product Image',
				'name'           => 'product_image',
				'type'           => 'image',
				'required'       => 0,
				'return_format'  => 'array',
				'preview_size'   => 'medium',
				'library'        => 'all',
			],
		],
		'location'        => [ [ [
			'param'    => 'post_type',
			'operator' => '==',
			'value'    => 'review',
		] ] ],
		'position'        => 'normal',
		'style'           => 'default',
		'label_placement' => 'top',
		'active'          => true,
	] );
}
add_action( 'acf/init', 'tdf_register_review_fields' );
