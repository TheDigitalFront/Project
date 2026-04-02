<?php

/* This file acts as the main loader. All functionality is organized
 * into separate files under the inc/ directory:
 *
 *   inc/article-cpt.php               — Article CPT registration (Phase 2A, R1).
 *   inc/opinion-cpt.php               — Opinion CPT registration (Phase 2B).
 *   inc/acf-fields.php                — ACF JSON sync + Article & Opinion fields (Phase 2A/2B, R2).
 *   inc/shortcode-category-filter.php — [tdf_category_filter] shortcode / Query 2 (Phase 4, R5/R6).
 *   inc/setup.php                     — One-time environment setup (pages, menu, plugins, etc.).
 *
 * Everything a new collaborator needs is auto-configured on first
 * admin visit via inc/setup.php: pages, menu, plugins, categories,
 * reading settings, Yoast breadcrumbs, registration, comments, and
 * rewrite rules.
 *
 * @package  TheDigitalFront
 * @since    1.0.0
 */

// =====================================================================
// 1. STYLES — Enqueue parent and child theme stylesheets.
// =====================================================================

function tdf_child_enqueue_styles()
{
	wp_enqueue_style(
		'parent-style',
		get_template_directory_uri() . '/style.css' /* loads the parent TwentyTwentyFive stylesheet first so the child theme can override it */
	);
	wp_enqueue_style(
		'child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('parent-style'), /* declares parent-style as a dependency so it always loads before the child */
		wp_get_theme()->get('Version') /* pulls the version from the theme header for cache busting on updates */
	);
}
add_action('wp_enqueue_scripts', 'tdf_child_enqueue_styles'); /* hooks into wp_enqueue_scripts so styles are loaded on the front end */

// =====================================================================
// 2. THEME SUPPORT — Register nav menus, post thumbnails, title tag.
// =====================================================================

function tdf_theme_setup()
{
	register_nav_menus([
		'primary' => __('Primary Menu', 'the-digital-front-child'), /* registers the primary menu location so it appears in Appearance > Menus */
	]);
	add_theme_support('post-thumbnails'); /* enables featured image support across all post types */
	add_theme_support('title-tag'); /* lets WordPress manage the <title> tag in the document head */
}
add_action('after_setup_theme', 'tdf_theme_setup'); /* runs after the theme is loaded so supports and menus are registered at the right time */

function tdf_fallback_menu()
{
	echo '<ul class="tdf-nav">'; /* outputs a basic nav list if no menu has been assigned to the primary location yet */
	echo '<li><a href="' . esc_url(home_url('/')) . '">Home</a></li>'; /* always shows a Home link pointing to the site root */
	$about = get_page_by_path('about-us'); /* checks if the About Us page exists before trying to link to it */
	if ($about) {
		echo '<li><a href="' . esc_url(get_permalink($about)) . '">About</a></li>'; /* outputs the About Us link only if the page exists */
	}
	echo '</ul>';
}

// =====================================================================
// 3. INCLUDES — Load modular functionality from inc/ directory.
// =====================================================================

$tdf_inc = get_stylesheet_directory() . '/inc'; /* builds the absolute path to the inc/ folder inside the child theme */

require_once $tdf_inc . '/article-cpt.php';               /* loads Article CPT registration (Phase 2A) */
require_once $tdf_inc . '/opinion-cpt.php';               /* loads Opinion CPT registration (Phase 2B) */
require_once $tdf_inc . '/acf-fields.php';                /* loads ACF JSON sync and Article & Opinion field groups (Phase 2A/2B) */
require_once $tdf_inc . '/shortcode-category-filter.php'; /* loads the [tdf_category_filter] shortcode and Query 2 (Phase 4) */
require_once $tdf_inc . '/setup.php';                     /* loads the one-time environment setup for pages, menus, roles, and settings (Phase 1-3) */

// =====================================================================
// 4. REGISTRATION — Auto-create the front-end registration page.
// =====================================================================

function tdf_create_registration_page()
{
	$page = get_page_by_path('register'); /* checks if a page with the slug 'register' already exists to avoid duplicates */

	if (! $page) {
		wp_insert_post([ /* creates the Registration page programmatically if it doesn't exist yet */
			'post_title'    => 'Register',
			'post_name'     => 'register', /* sets the URL slug to /register/ */
			'post_content'  => '',
			'post_status'   => 'publish', /* publishes immediately so it's accessible on the front end */
			'post_type'     => 'page',
			'page_template' => 'page-register.php', /* assigns the custom registration template from the child theme */
		]);
	}
}
add_action('after_switch_theme', 'tdf_create_registration_page'); /* runs once when the theme is activated so the page is created automatically */