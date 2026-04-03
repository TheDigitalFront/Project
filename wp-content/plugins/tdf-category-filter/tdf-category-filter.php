<?php
/**
 * Plugin Name:       TDF AJAX Category Filter
 * Description:       Renders a filterable, paginated grid of Article posts. Users select a category tab and results update via AJAX — no full page reload required. Supports date range filtering. Built for The Digital Front.
 * Version:           1.0.0
 * Author:            Jeremiah Clinton
 * Text Domain:       tdf-category-filter
 *
 * Shortcode: [tdf_category_filter per_page="6"]
 *
 * AJAX action:  tdf_filter_posts (public + authenticated)
 * Nonce:        tdf_filter_nonce (verified server-side on every request)
 *
 * This plugin satisfies:
 *   R5 — Content pagination + curation on 3+ pages
 *   R6 — Query 2: custom complex query (category + date range), fully documented
 *   R7 — Plugin 2: custom interactive plugin with AJAX
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TDF_CF_VERSION', '1.0.0' );

// =====================================================================
// 1. SHORTCODE — Renders the filter UI and initial post grid.
// =====================================================================

/**
 * [tdf_category_filter] shortcode callback.
 *
 * Outputs: category tab buttons, date range form, post grid, and pagination.
 * The initial load is server-rendered. Subsequent filter clicks use AJAX
 * via fetch() to swap the grid content without a page reload.
 *
 * @param array $atts Shortcode attributes. Accepts 'per_page' (int, default 6).
 * @return string HTML output.
 */
function tdf_cf_shortcode( $atts ) {
	$atts = shortcode_atts( [ 'per_page' => 6 ], $atts );

	$paged   = max( 1, get_query_var( 'paged', 1 ) );
	$cur_cat = isset( $_GET['article_cat'] ) ? sanitize_key( $_GET['article_cat'] ) : '';
	$from    = isset( $_GET['from'] ) ? sanitize_text_field( $_GET['from'] ) : '';
	$to      = isset( $_GET['to'] )   ? sanitize_text_field( $_GET['to'] )   : '';

	if ( $from && ! strtotime( $from ) ) { $from = ''; }
	if ( $to   && ! strtotime( $to ) )   { $to   = ''; }

	$articles = new WP_Query( tdf_cf_build_query_args( $cur_cat, $from, $to, $paged, absint( $atts['per_page'] ) ) );

	$cats = get_categories( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
	$cats = array_filter( $cats, function ( $c ) {
		return $c->slug !== 'uncategorized';
	} );

	ob_start();
	?>
	<div class="tdf-filter" id="tdf-filter" data-per-page="<?php echo absint( $atts['per_page'] ); ?>">

		<div class="tdf-filter__controls">
			<!-- Category tabs — clicking triggers AJAX via JS -->
			<div class="tdf-filter__tabs" role="tablist">
				<button type="button" class="tdf-filter__tab <?php echo ! $cur_cat ? 'is-active' : ''; ?>"
					data-cat="" role="tab">All</button>
				<?php foreach ( $cats as $cat ) : ?>
				<button type="button" class="tdf-filter__tab <?php echo $cur_cat === $cat->slug ? 'is-active' : ''; ?>"
					data-cat="<?php echo esc_attr( $cat->slug ); ?>" role="tab">
					<?php echo esc_html( $cat->name ); ?>
				</button>
				<?php endforeach; ?>
			</div>

			<form class="tdf-filter__dates" id="tdf-filter-dates">
				<label class="tdf-filter__date-label">
					From
					<input type="date" name="from" value="<?php echo esc_attr( $from ); ?>" class="tdf-filter__date-input">
				</label>
				<label class="tdf-filter__date-label">
					To
					<input type="date" name="to" value="<?php echo esc_attr( $to ); ?>" class="tdf-filter__date-input">
				</label>
				<button type="submit" class="tdf-btn tdf-btn--sm tdf-btn--outline">Filter</button>
				<?php if ( $from || $to ) : ?>
					<button type="button" class="tdf-filter__clear" id="tdf-filter-clear">&times;</button>
				<?php endif; ?>
			</form>
		</div>

		<!-- Loading spinner — hidden by default, shown during AJAX requests -->
		<div class="tdf-filter__loading" id="tdf-filter-loading" aria-hidden="true">
			<div class="tdf-filter__spinner"></div>
		</div>

		<!-- Post grid container — content swapped via AJAX -->
		<div id="tdf-filter-results">
			<?php echo tdf_cf_render_results( $articles ); ?>
		</div>

	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'tdf_category_filter', 'tdf_cf_shortcode' );

// =====================================================================
// 2. QUERY BUILDER — Constructs WP_Query args for Query 2.
// =====================================================================

/**
 * Build WP_Query arguments for the category + date range filter.
 *
 * Query 2 breakdown:
 *   - post_type:  article (the main content CPT)
 *   - tax_query:  filters by category slug if provided (AND logic)
 *   - date_query: filters by after/before dates if provided (inclusive)
 *   - orderby:    date DESC (newest first)
 *   - paged:      supports WP-PageNavi pagination
 *
 * Sanitization:
 *   - $cat:  already passed through sanitize_key() — safe for slug matching
 *   - $from/$to: passed through sanitize_text_field() + strtotime() validation
 *
 * @param string $cat      Category slug (empty string = all categories).
 * @param string $from     Start date YYYY-MM-DD (empty = no lower bound).
 * @param string $to       End date YYYY-MM-DD (empty = no upper bound).
 * @param int    $paged    Current page number.
 * @param int    $per_page Posts per page.
 * @return array WP_Query args.
 */
function tdf_cf_build_query_args( $cat, $from, $to, $paged, $per_page ) {
	$args = [
		'post_type'      => 'article',
		'posts_per_page' => $per_page,
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	/* tax_query — filters by category slug using the shared WP 'category' taxonomy. */
	if ( $cat ) {
		$args['tax_query'] = [ [
			'taxonomy' => 'category',
			'field'    => 'slug',
			'terms'    => $cat,
		] ];
	}

	/* date_query — filters by date range. 'inclusive' => true includes boundary dates. */
	if ( $from || $to ) {
		$date_query = [ 'inclusive' => true ];
		if ( $from ) { $date_query['after']  = $from; }
		if ( $to )   { $date_query['before'] = $to; }
		$args['date_query'] = [ $date_query ];
	}

	return $args;
}

// =====================================================================
// 3. RESULTS RENDERER — Outputs the grid + pagination HTML.
// =====================================================================

/**
 * Render the article grid and pagination for a given WP_Query.
 * Used both on initial page load and in the AJAX response.
 *
 * @param WP_Query $query The query to render.
 * @return string HTML output.
 */
function tdf_cf_render_results( $query ) {
	ob_start();

	if ( $query->have_posts() ) : ?>
		<div class="tdf-grid tdf-grid--3">
			<?php while ( $query->have_posts() ) : $query->the_post(); ?>
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
				wp_pagenavi( [ 'query' => $query ] );
			} else {
				echo paginate_links( [
					'total'   => $query->max_num_pages,
					'current' => max( 1, $query->get( 'paged' ) ),
				] );
			}
			?>
		</div>
	<?php else : ?>
		<div class="tdf-empty">
			<p>No articles found matching your filters.</p>
		</div>
	<?php endif;

	return ob_get_clean();
}

// =====================================================================
// 4. AJAX HANDLER — Processes filter requests without page reload.
// =====================================================================

/**
 * AJAX handler for tdf_filter_posts action.
 *
 * Receives: cat (string), from (string), to (string), paged (int), per_page (int), nonce (string).
 * Returns:  JSON with 'html' key containing the rendered grid + pagination.
 *
 * Security: wp_verify_nonce() checks the tdf_filter_nonce token to prevent
 * CSRF attacks. Requests with invalid or missing nonces are rejected with 403.
 *
 * Registered for both wp_ajax_ (logged-in) and wp_ajax_nopriv_ (logged-out)
 * so the filter works for all visitors.
 */
function tdf_cf_ajax_handler() {
	/* Verify nonce — protects against cross-site request forgery. */
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tdf_filter_nonce' ) ) {
		wp_send_json_error( 'Invalid nonce.', 403 );
	}

	$cat      = isset( $_POST['cat'] )      ? sanitize_key( $_POST['cat'] ) : '';
	$from     = isset( $_POST['from'] )     ? sanitize_text_field( $_POST['from'] ) : '';
	$to       = isset( $_POST['to'] )       ? sanitize_text_field( $_POST['to'] ) : '';
	$paged    = isset( $_POST['paged'] )    ? absint( $_POST['paged'] ) : 1;
	$per_page = isset( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 6;

	if ( $from && ! strtotime( $from ) ) { $from = ''; }
	if ( $to   && ! strtotime( $to ) )   { $to   = ''; }

	$query = new WP_Query( tdf_cf_build_query_args( $cat, $from, $to, $paged, $per_page ) );
	$html  = tdf_cf_render_results( $query );
	wp_reset_postdata();

	wp_send_json_success( [ 'html' => $html ] );
}
add_action( 'wp_ajax_tdf_filter_posts', 'tdf_cf_ajax_handler' );
add_action( 'wp_ajax_nopriv_tdf_filter_posts', 'tdf_cf_ajax_handler' );

// =====================================================================
// 5. ENQUEUE — Load CSS and JS assets on the front end.
// =====================================================================

/**
 * Enqueue plugin styles and scripts.
 * wp_localize_script passes the AJAX URL and nonce to the JS file.
 */
function tdf_cf_enqueue() {
	wp_enqueue_style(
		'tdf-category-filter',
		plugin_dir_url( __FILE__ ) . 'css/filter.css',
		[],
		TDF_CF_VERSION
	);

	wp_enqueue_script(
		'tdf-category-filter',
		plugin_dir_url( __FILE__ ) . 'js/filter.js',
		[],
		TDF_CF_VERSION,
		true
	);

	/* Pass AJAX URL and nonce to the JS file so fetch() can call the handler. */
	wp_localize_script( 'tdf-category-filter', 'tdfFilter', [
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tdf_filter_nonce' ),
	] );
}
add_action( 'wp_enqueue_scripts', 'tdf_cf_enqueue' );
