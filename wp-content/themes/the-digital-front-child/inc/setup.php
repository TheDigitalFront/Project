<?php
/**
 * One-time environment setup — auto-configures everything on first admin visit.
 *
 * This runs once per environment, gated by TDF_SETUP_VERSION. Bumping
 * the version constant forces a re-run, useful when adding new setup
 * steps without requiring manual intervention from collaborators.
 *
 * What it configures:
 *   1. Activates all required plugins.
 *   2. Creates required pages (Home, About Us, Team, Mission).
 *   3. Sets the static front page in Settings > Reading.
 *   4. Seeds categories for the article filter (Mobile Devices, Apple, Google, Samsung).
 *   5. Builds and assigns the primary nav menu with correct hierarchy.
 *   6. Enables Yoast SEO breadcrumbs.
 *   7. Enables front-end user registration with Subscriber as default role.
 *   8. Enables comments site-wide with moderation on first comment.
 *   9. Flushes rewrite rules for the Article CPT archive.
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

define( 'TDF_SETUP_VERSION', '6' );

/**
 * Master setup function — orchestrates all one-time configuration.
 *
 * Runs on every admin_init but exits immediately if the stored version
 * matches TDF_SETUP_VERSION — so it only does real work once.
 *
 * @hooked admin_init
 * @hooked after_switch_theme
 * @return void
 */
function tdf_run_setup() {
	if ( get_option( 'tdf_setup_version' ) === TDF_SETUP_VERSION ) {
		return;
	}

	// 1. Activate required plugins (safe to call repeatedly).
	tdf_setup_activate_plugins();

	// 2. Create pages (idempotent — skips if page already exists).
	$page_ids = tdf_setup_create_pages();

	// 3. Set the Home page as the static front page.
	if ( ! empty( $page_ids['Home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['Home'] );
	}

	// 4. Seed categories so the filter UI has tabs on a fresh install.
	tdf_setup_create_categories();

	// 5. Build nav menu: Home | Trending (optional) | About > Team, Mission.
	tdf_setup_create_menu( $page_ids );

	// 6. Enable Yoast SEO breadcrumbs (Phase 2B, Step 13).
	$yoast = get_option( 'wpseo_titles' );
	if ( is_array( $yoast ) && empty( $yoast['breadcrumbs-enable'] ) ) {
		$yoast['breadcrumbs-enable'] = true;
		update_option( 'wpseo_titles', $yoast );
	}

	// 7. Enable front-end registration (Phase 3, Step 3).
	//    Default role = Subscriber (read + comment only).
	update_option( 'users_can_register', '1' );
	update_option( 'default_role', 'subscriber' );

	// 8. Enable comments site-wide (Phase 3, Step 4).
	//    Require name + email; first comment must be manually approved.
	update_option( 'default_comment_status', 'open' );
	update_option( 'default_ping_status', 'open' );
	update_option( 'require_name_email', '1' );
	update_option( 'comment_moderation', '1' );

	// 9. Flush rewrite rules so /article/ archive URL works.
	flush_rewrite_rules();

	// Mark setup complete for this version.
	update_option( 'tdf_setup_version', TDF_SETUP_VERSION );
}
add_action( 'admin_init', 'tdf_run_setup' );
add_action( 'after_switch_theme', 'tdf_run_setup' );

// ─── Helper: Activate Required Plugins ───────────────────────────

/**
 * Activate all required plugins if they exist but aren't active.
 *
 * Checks the filesystem for each plugin's main file before adding
 * it to the active_plugins option. Safe to call multiple times.
 *
 * @return void
 */
function tdf_setup_activate_plugins() {
	$required = [
		'advanced-custom-fields/acf.php',
		'custom-post-type-ui/custom-post-type-ui.php',
		'members/members.php',
		'query-monitor/query-monitor.php',
		'tdf-breaking-news/tdf-breaking-news.php',
		'wordpress-seo/wp-seo.php',
		'wp-migrate-db/wp-migrate-db.php',
		'wp-pagenavi/wp-pagenavi.php',
		'wpforms-lite/wpforms.php',
	];

	$active  = get_option( 'active_plugins', [] );
	$changed = false;

	foreach ( $required as $plugin ) {
		if ( ! in_array( $plugin, $active, true )
			&& file_exists( WP_PLUGIN_DIR . '/' . $plugin ) ) {
			$active[] = $plugin;
			$changed  = true;
		}
	}

	if ( $changed ) {
		sort( $active );
		update_option( 'active_plugins', $active );
	}
}

// ─── Helper: Create Required Pages ───────────────────────────────

/**
 * Create the required pages for the site structure.
 *
 * Pages: Home, About Us, Team (child), Mission (child).
 * Each identified by a unique post_meta key so the function is
 * idempotent — existing pages (even if renamed) won't be duplicated.
 *
 * @return array Associative array of page title => page ID.
 */
function tdf_setup_create_pages() {
	$pages = [
		'Home'     => [ 'content' => '', 'meta' => '_tdf_is_home' ],
		'About Us' => [ 'content' => '<!-- wp:paragraph --><p>Learn more about The Digital Front.</p><!-- /wp:paragraph -->', 'meta' => '_tdf_is_about' ],
	];

	$ids = [];

	foreach ( $pages as $title => $cfg ) {
		$existing = get_posts( [
			'post_type' => 'page', 'meta_key' => $cfg['meta'], 'meta_value' => '1',
			'numberposts' => 1, 'post_status' => 'any',
		] );

		if ( $existing ) {
			$ids[ $title ] = $existing[0]->ID;
			continue;
		}

		$id = wp_insert_post( [
			'post_title'   => $title,
			'post_content' => $cfg['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
		] );

		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, $cfg['meta'], '1' );
			$ids[ $title ] = $id;
		}
	}

	// Child pages under About Us (2-level hierarchy for R4).
	$about_id = $ids['About Us'] ?? 0;
	$children = [
		'Team'    => [ 'content' => '<!-- wp:paragraph --><p>Meet the team behind The Digital Front.</p><!-- /wp:paragraph -->', 'meta' => '_tdf_is_team' ],
		'Mission' => [ 'content' => '<!-- wp:paragraph --><p>Our mission at The Digital Front.</p><!-- /wp:paragraph -->', 'meta' => '_tdf_is_mission' ],
	];

	foreach ( $children as $title => $cfg ) {
		$existing = get_posts( [
			'post_type' => 'page', 'meta_key' => $cfg['meta'], 'meta_value' => '1',
			'numberposts' => 1, 'post_status' => 'any',
		] );

		if ( $existing ) {
			$ids[ $title ] = $existing[0]->ID;
			continue;
		}

		$id = wp_insert_post( [
			'post_title'   => $title,
			'post_content' => $cfg['content'],
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $about_id,
		] );

		if ( ! is_wp_error( $id ) ) {
			update_post_meta( $id, $cfg['meta'], '1' );
			$ids[ $title ] = $id;
		}
	}

	return $ids;
}

// ─── Helper: Seed Categories ─────────────────────────────────────

/**
 * Create seed categories so the filter UI has tabs on a fresh install.
 *
 * Uses term_exists() to avoid duplicates if categories already exist.
 *
 * @return void
 */
function tdf_setup_create_categories() {
	$seed_cats = [ 'Mobile Devices', 'Apple', 'Google', 'Samsung' ];
	foreach ( $seed_cats as $name ) {
		if ( ! term_exists( $name, 'category' ) ) {
			wp_insert_term( $name, 'category' );
		}
	}
}

// ─── Helper: Create & Assign Nav Menu ────────────────────────────

/**
 * Build the primary navigation menu and assign it to the 'primary' location.
 *
 * Structure: Home | Trending (if exists) | About > Team, Mission
 * Deletes and rebuilds if the menu already exists to ensure correct IDs.
 *
 * @param array $page_ids Associative array from tdf_setup_create_pages().
 * @return void
 */
function tdf_setup_create_menu( $page_ids ) {
	if ( empty( $page_ids['Home'] ) || empty( $page_ids['About Us'] ) ||
	     empty( $page_ids['Team'] ) || empty( $page_ids['Mission'] ) ) {
		return; // Require all core pages before building.
	}

	$menu_name = 'Primary Menu';
	$existing  = wp_get_nav_menu_object( $menu_name );
	if ( $existing ) {
		wp_delete_nav_menu( $existing->term_id );
	}

	$menu_id = wp_create_nav_menu( $menu_name );
	if ( is_wp_error( $menu_id ) ) {
		return;
	}

	$pos = 1;

	// Home.
	wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'Home', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['Home'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-position' => $pos++,
	] );

	// Trending in Tech (optional — only added if the page exists).
	$trending = get_page_by_path( 'trending-in-tech' );
	if ( $trending ) {
		wp_update_nav_menu_item( $menu_id, 0, [
			'menu-item-title' => 'Trending', 'menu-item-object' => 'page',
			'menu-item-object-id' => $trending->ID, 'menu-item-type' => 'post_type',
			'menu-item-status' => 'publish', 'menu-item-position' => $pos++,
		] );
	}

	// About (parent menu item with dropdown children).
	$about_menu_id = wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'About', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['About Us'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-position' => $pos++,
	] );

	// Team (child of About).
	wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'Team', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['Team'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-parent-id' => $about_menu_id,
		'menu-item-position' => $pos++,
	] );

	// Mission (child of About).
	wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'Mission', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['Mission'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-parent-id' => $about_menu_id,
		'menu-item-position' => $pos++,
	] );

	// Assign the menu to the 'primary' theme location.
	$locations = get_theme_mod( 'nav_menu_locations', [] );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
