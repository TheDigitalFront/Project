<?php
/**
 * The Digital Front Child Theme — functions and definitions.
 *
 * Everything a new collaborator needs is auto-configured on first
 * admin visit: pages, menu, plugins, categories, reading settings,
 * Yoast breadcrumbs, and rewrite rules.
 */

// ─── Styles ──────────────────────────────────────────────────────
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

// ─── Theme Support & Nav Menus ───────────────────────────────────
function tdf_theme_setup() {
	register_nav_menus( [
		'primary' => __( 'Primary Menu', 'the-digital-front-child' ),
	] );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'title-tag' );
}
add_action( 'after_setup_theme', 'tdf_theme_setup' );

/**
 * Fallback nav when no menu is assigned yet.
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

// ─── Article CPT ─────────────────────────────────────────────────
function tdf_register_article_cpt() {
	$labels = [
		'name'                  => esc_html__( 'Articles', 'the-digital-front-child' ),
		'singular_name'         => esc_html__( 'Article', 'the-digital-front-child' ),
		'menu_name'             => esc_html__( 'My Articles', 'the-digital-front-child' ),
		'all_items'             => esc_html__( 'All Articles', 'the-digital-front-child' ),
		'add_new'               => esc_html__( 'Add New', 'the-digital-front-child' ),
		'add_new_item'          => esc_html__( 'Add New Article', 'the-digital-front-child' ),
		'edit_item'             => esc_html__( 'Edit Article', 'the-digital-front-child' ),
		'new_item'              => esc_html__( 'Add Article', 'the-digital-front-child' ),
		'view_item'             => esc_html__( 'View Article', 'the-digital-front-child' ),
		'view_items'            => esc_html__( 'View Articles', 'the-digital-front-child' ),
		'search_items'          => esc_html__( 'Search Articles', 'the-digital-front-child' ),
		'not_found'             => esc_html__( 'No Articles Found', 'the-digital-front-child' ),
		'not_found_in_trash'    => esc_html__( 'No Articles Found in Trash', 'the-digital-front-child' ),
		'parent'                => esc_html__( 'Parent Article', 'the-digital-front-child' ),
		'featured_image'        => esc_html__( 'Featured image for this article', 'the-digital-front-child' ),
		'set_featured_image'    => esc_html__( 'Set featured image for this article', 'the-digital-front-child' ),
		'remove_featured_image' => esc_html__( 'Remove featured image for this article', 'the-digital-front-child' ),
		'use_featured_image'    => esc_html__( 'Use as featured image for this article', 'the-digital-front-child' ),
		'archives'              => esc_html__( 'Article Archives', 'the-digital-front-child' ),
		'insert_into_item'      => esc_html__( 'Insert into article', 'the-digital-front-child' ),
		'uploaded_to_this_item' => esc_html__( 'Uploaded to this article', 'the-digital-front-child' ),
		'filter_items_list'     => esc_html__( 'Filter articles list', 'the-digital-front-child' ),
		'items_list_navigation' => esc_html__( 'Article List Navigation', 'the-digital-front-child' ),
		'items_list'            => esc_html__( 'Articles List', 'the-digital-front-child' ),
		'attributes'            => esc_html__( 'Articles Attributes', 'the-digital-front-child' ),
		'name_admin_bar'        => esc_html__( 'Article', 'the-digital-front-child' ),
		'item_published'        => esc_html__( 'Article published.', 'the-digital-front-child' ),
		'item_published_privately' => esc_html__( 'Article published privately.', 'the-digital-front-child' ),
		'item_reverted_to_draft'   => esc_html__( 'Article reverted to draft.', 'the-digital-front-child' ),
		'item_trashed'          => esc_html__( 'Article trashed.', 'the-digital-front-child' ),
		'item_scheduled'        => esc_html__( 'Article scheduled.', 'the-digital-front-child' ),
		'item_updated'          => esc_html__( 'Article updated.', 'the-digital-front-child' ),
		'template_name'         => esc_html__( 'Single Item: Article', 'the-digital-front-child' ),
		'parent_item_colon'     => esc_html__( 'Parent Article', 'the-digital-front-child' ),
	];

	register_post_type( 'article', [
		'label'                => esc_html__( 'Articles', 'the-digital-front-child' ),
		'labels'               => $labels,
		'description'          => 'The main content type for publishing written content on The Digital Front.',
		'public'               => true,
		'publicly_queryable'   => true,
		'show_ui'              => true,
		'show_in_rest'         => true,
		'rest_base'            => '',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'rest_namespace'       => 'wp/v2',
		'has_archive'          => true,
		'show_in_menu'         => true,
		'show_in_nav_menus'    => true,
		'delete_with_user'     => false,
		'exclude_from_search'  => false,
		'capability_type'      => 'post',
		'map_meta_cap'         => true,
		'hierarchical'         => false,
		'can_export'           => false,
		'rewrite'              => [ 'slug' => 'article', 'with_front' => true ],
		'query_var'            => true,
		'menu_icon'            => 'dashicons-admin-page',
		'supports'             => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
		'taxonomies'           => [ 'category' ],
		'show_in_graphql'      => false,
	] );
}
add_action( 'init', 'tdf_register_article_cpt' );

// ─── ACF JSON Sync ───────────────────────────────────────────────
function tdf_acf_json_save_point( $path ) {
	return get_stylesheet_directory() . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'tdf_acf_json_save_point' );

function tdf_acf_json_load_point( $paths ) {
	$paths[] = get_stylesheet_directory() . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'tdf_acf_json_load_point' );

// ─── ACF Fields for Article ──────────────────────────────────────
function tdf_register_article_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
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

// ─── [tdf_category_filter] Shortcode ─────────────────────────────
function tdf_category_filter_shortcode( $atts ) {
	$atts    = shortcode_atts( [ 'per_page' => 6 ], $atts );
	$paged   = max( 1, get_query_var( 'paged', 1 ) );
	$cur_cat = isset( $_GET['article_cat'] ) ? sanitize_text_field( $_GET['article_cat'] ) : '';

	$query_args = [
		'post_type'      => 'article',
		'posts_per_page' => absint( $atts['per_page'] ),
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	if ( $cur_cat ) {
		$query_args['tax_query'] = [ [
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => $cur_cat,
		] ];
	}

	$articles = new WP_Query( $query_args );

	// All categories except Uncategorized.
	$cats = get_categories( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
	$cats = array_filter( $cats, function ( $c ) {
		return $c->slug !== 'uncategorized';
	} );

	ob_start();
	?>
	<div class="tdf-filter">
		<div class="tdf-filter__tabs">
			<a href="<?php echo esc_url( remove_query_arg( [ 'article_cat', 'paged' ] ) ); ?>"
			   class="tdf-filter__tab <?php echo ! $cur_cat ? 'is-active' : ''; ?>">All</a>
			<?php foreach ( $cats as $cat ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'article_cat', $cat->slug, remove_query_arg( 'paged' ) ) ); ?>"
			   class="tdf-filter__tab <?php echo $cur_cat === $cat->slug ? 'is-active' : ''; ?>">
				<?php echo esc_html( $cat->name ); ?>
			</a>
			<?php endforeach; ?>
		</div>

		<?php if ( $articles->have_posts() ) : ?>
		<div class="tdf-grid tdf-grid--3">
			<?php while ( $articles->have_posts() ) : $articles->the_post(); ?>
			<article class="tdf-card">
				<?php if ( has_post_thumbnail() ) : ?>
					<a href="<?php the_permalink(); ?>" class="tdf-card__image"><?php the_post_thumbnail( 'medium_large' ); ?></a>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>" class="tdf-card__image tdf-card__image--placeholder">
						<span><?php echo esc_html( mb_substr( get_the_title(), 0, 1 ) ); ?></span>
					</a>
				<?php endif; ?>
				<div class="tdf-card__body">
					<time class="tdf-card__date" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time>
					<h3 class="tdf-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
					<?php if ( has_excerpt() ) : ?>
						<p class="tdf-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 18 ); ?></p>
					<?php endif; ?>
					<?php $rt = get_field( 'reading_time' ); ?>
					<?php if ( $rt ) : ?>
						<span class="tdf-card__meta"><?php echo esc_html( $rt ); ?> min read</span>
					<?php endif; ?>
				</div>
			</article>
			<?php endwhile; ?>
		</div>

		<div class="tdf-pagination">
			<?php
			if ( function_exists( 'wp_pagenavi' ) ) {
				wp_pagenavi( [ 'query' => $articles ] );
			} else {
				echo paginate_links( [
					'total'   => $articles->max_num_pages,
					'current' => $paged,
				] );
			}
			?>
		</div>
		<?php else : ?>
		<div class="tdf-empty">
			<p>No articles found<?php echo $cur_cat ? ' in this category' : ''; ?>.</p>
		</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tdf_category_filter', 'tdf_category_filter_shortcode' );

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
// ONE-TIME SETUP — runs once per environment on first admin visit.
// Uses a single version flag so bumping TDF_SETUP_VERSION re-runs.
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

define( 'TDF_SETUP_VERSION', '5' );

function tdf_run_setup() {
	if ( get_option( 'tdf_setup_version' ) === TDF_SETUP_VERSION ) {
		return;
	}

	// 1. Activate required plugins.
	tdf_setup_activate_plugins();

	// 2. Create pages (Home, Blog, About Us, Team, Mission).
	$page_ids = tdf_setup_create_pages();

	// 3. Configure reading settings.
	if ( ! empty( $page_ids['Home'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $page_ids['Home'] );
	}
	// 4. Create seed categories for the filter.
	tdf_setup_create_categories();

	// 5. Build and assign nav menu.
	tdf_setup_create_menu( $page_ids );

	// 6. Enable Yoast breadcrumbs.
	$yoast = get_option( 'wpseo_titles' );
	if ( is_array( $yoast ) && empty( $yoast['breadcrumbs-enable'] ) ) {
		$yoast['breadcrumbs-enable'] = true;
		update_option( 'wpseo_titles', $yoast );
	}

	// 7. Flush rewrite rules (CPT archive).
	flush_rewrite_rules();

	// Mark complete.
	update_option( 'tdf_setup_version', TDF_SETUP_VERSION );
}
add_action( 'admin_init', 'tdf_run_setup' );

// Also run on theme activation for brand-new installs.
add_action( 'after_switch_theme', 'tdf_run_setup' );

// ─── Setup Helpers ───────────────────────────────────────────────

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

	// Child pages under About Us.
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

function tdf_setup_create_categories() {
	$seed_cats = [ 'Mobile Devices', 'Apple', 'Google', 'Samsung' ];
	foreach ( $seed_cats as $name ) {
		if ( ! term_exists( $name, 'category' ) ) {
			wp_insert_term( $name, 'category' );
		}
	}
}

function tdf_setup_create_menu( $page_ids ) {
	// Need all core pages.
	if ( empty( $page_ids['Home'] ) || empty( $page_ids['About Us'] ) ||
	     empty( $page_ids['Team'] ) || empty( $page_ids['Mission'] ) ) {
		return;
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

	// Trending (optional).
	$trending = get_page_by_path( 'trending-in-tech' );
	if ( $trending ) {
		wp_update_nav_menu_item( $menu_id, 0, [
			'menu-item-title' => 'Trending', 'menu-item-object' => 'page',
			'menu-item-object-id' => $trending->ID, 'menu-item-type' => 'post_type',
			'menu-item-status' => 'publish', 'menu-item-position' => $pos++,
		] );
	}

	// About (parent).
	$about_menu_id = wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'About', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['About Us'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-position' => $pos++,
	] );

	// Team (child).
	wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'Team', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['Team'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-parent-id' => $about_menu_id,
		'menu-item-position' => $pos++,
	] );

	// Mission (child).
	wp_update_nav_menu_item( $menu_id, 0, [
		'menu-item-title' => 'Mission', 'menu-item-object' => 'page',
		'menu-item-object-id' => $page_ids['Mission'], 'menu-item-type' => 'post_type',
		'menu-item-status' => 'publish', 'menu-item-parent-id' => $about_menu_id,
		'menu-item-position' => $pos++,
	] );

	// Assign to primary location.
	$locations = get_theme_mod( 'nav_menu_locations', [] );
	$locations['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $locations );
}
