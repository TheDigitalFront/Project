<?php
/**
 * The Digital Front Child Theme — functions and definitions.
 *
 * This file acts as the main loader. All functionality is organized
 * into separate files under the inc/ directory:
 *
 *   inc/article-cpt.php               — Article CPT registration (Phase 2A, R1).
 *   inc/opinion-cpt.php               — Opinion CPT registration (Phase 2B).
 *   inc/review-cpt.php                — Review CPT registration (Phase 2C).
 *   inc/acf-fields.php                — ACF JSON sync + Article, Opinion & Review fields (Phase 2, R2).
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

/**
 * Enqueue the parent (TwentyTwentyFive) and child theme stylesheets.
 *
 * The parent stylesheet loads first so the child can override styles.
 * Child version is pulled from the theme header for cache busting.
 *
 * @hooked wp_enqueue_scripts
 * @return void
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

// =====================================================================
// 2. THEME SUPPORT — Register nav menus, post thumbnails, title tag.
// =====================================================================

/**
 * Set up theme features and register the 'primary' menu location.
 *
 * @hooked after_setup_theme
 * @return void
 */
function tdf_theme_setup() {
	register_nav_menus( [
		'primary' => __( 'Primary Menu', 'the-digital-front-child' ),
	] );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'tdf_theme_setup' );

/**
 * Fallback navigation when no menu is assigned yet.
 *
 * Used as fallback_cb in wp_nav_menu() (header.php) so the site
 * always has visible navigation even before setup runs.
 *
 * @return void
 */
function tdf_fallback_menu() {
	echo '<ul class="tdf-nav">';
	echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
	$about = get_page_by_path( 'about-us' );
	if ( $about ) {
		echo '<li><a href="' . esc_url( get_permalink( $about ) ) . '">About</a></li>';
	}
	echo '</ul>';
}

// =====================================================================
// 3. POST VIEW COUNTER — Track views for the Trending page query.
// =====================================================================

/**
 * Increment a simple view counter stored in post meta (_tdf_post_views)
 * each time a single post is viewed on the front end.
 * Used by Query 1 (Trending in Tech) to order posts by popularity.
 */
function tdf_track_post_views() {
	if ( is_single() && ! is_admin() ) {
		$post_id = get_the_ID();
		$views   = (int) get_post_meta( $post_id, '_tdf_post_views', true );
		update_post_meta( $post_id, '_tdf_post_views', $views + 1 );
	}
}
add_action( 'wp', 'tdf_track_post_views' );

// =====================================================================
// 4. INCLUDES — Load modular functionality from inc/ directory.
// =====================================================================

$tdf_inc = get_stylesheet_directory() . '/inc';

require_once $tdf_inc . '/article-cpt.php';               // Article CPT (Phase 2A).
require_once $tdf_inc . '/opinion-cpt.php';               // Opinion CPT (Phase 2B).
require_once $tdf_inc . '/review-cpt.php';                // Review CPT (Phase 2C).
require_once $tdf_inc . '/acf-fields.php';                 // ACF sync + Article, Opinion & Review fields.
require_once $tdf_inc . '/shortcode-category-filter.php';  // [tdf_category_filter] / Query 2 (Phase 4).
require_once $tdf_inc . '/setup.php';                      // One-time environment setup (Phase 1-3).
