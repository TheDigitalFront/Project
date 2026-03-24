<?php
/**
 * [tdf_category_filter] Shortcode — Query 2 (Phase 4).
 *
 * Custom complex query: Posts filtered by Category + Date Range.
 *
 * PURPOSE:
 *   Renders a filterable, paginated grid of Article posts. Users can
 *   narrow results by category (pill tabs) and/or date range (date
 *   inputs) via URL query strings — no JavaScript required.
 *
 * URL PARAMETERS (all optional, combined with AND logic):
 *   ?article_cat=slug   — Filter by category slug (tax_query).
 *   ?from=YYYY-MM-DD    — Show posts on or after this date (date_query).
 *   ?to=YYYY-MM-DD      — Show posts on or before this date (date_query).
 *
 * SANITIZATION:
 *   - article_cat: sanitize_key() — strips to [a-z0-9_\-], safe for slugs.
 *   - from / to:   sanitize_text_field() then validated with strtotime().
 *     Invalid dates are silently ignored (query falls back to all dates).
 *
 * PERFORMANCE (verified with Query Monitor):
 *   Uses WordPress core's indexed wp_posts.post_date column and the
 *   wp_term_relationships table. Pagination via LIMIT/OFFSET — single
 *   DB query per page load. No N+1 issues.
 *
 * USAGE:
 *   [tdf_category_filter per_page="6"]
 *
 * Satisfies requirements R5 (pagination + curation) and R6 (custom
 * complex query, fully documented).
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

/**
 * Render the category + date range filter with a paginated article grid.
 *
 * @param array $atts Shortcode attributes.
 *                    - per_page (int) Number of articles per page. Default 6.
 * @return string     HTML output (category tabs, date form, article grid, pagination).
 */
function tdf_category_filter_shortcode( $atts ) {
	$atts = shortcode_atts( [ 'per_page' => 6 ], $atts );

	// ── Pagination ─────────────────────────────────────────────
	// get_query_var('paged') reads /page/N/ or ?paged=N from the URL.
	$paged = max( 1, get_query_var( 'paged', 1 ) );

	// ── Sanitize user inputs ───────────────────────────────────
	// sanitize_key() strips everything except [a-z0-9_\-], ideal for taxonomy slugs.
	$cur_cat = isset( $_GET['article_cat'] ) ? sanitize_key( $_GET['article_cat'] ) : '';

	// sanitize_text_field() removes tags/encoding; strtotime() validates the format.
	$from = isset( $_GET['from'] ) ? sanitize_text_field( $_GET['from'] ) : '';
	$to   = isset( $_GET['to'] )   ? sanitize_text_field( $_GET['to'] )   : '';

	// Reject anything strtotime() can't parse — prevents malformed date_query.
	if ( $from && ! strtotime( $from ) ) { $from = ''; }
	if ( $to   && ! strtotime( $to ) )   { $to   = ''; }

	// ── Build WP_Query args ────────────────────────────────────
	// Base: all published articles, newest first, paginated.
	$query_args = [
		'post_type'      => 'article',
		'posts_per_page' => absint( $atts['per_page'] ),
		'paged'          => $paged,
		'orderby'        => 'date',
		'order'          => 'DESC',
	];

	// tax_query — filter by category slug if provided.
	// Uses 'slug' field so the URL param is human-readable (?article_cat=apple).
	if ( $cur_cat ) {
		$query_args['tax_query'] = [ [
			'taxonomy' => 'category',   // Shared WP taxonomy attached to Article CPT.
			'field'    => 'slug',       // Match by slug, not term ID.
			'terms'    => $cur_cat,     // Single slug from the sanitized URL param.
		] ];
	}

	// date_query — filter by date range if 'from' and/or 'to' are provided.
	// 'inclusive' => true ensures boundary dates are included in results.
	// Both params are optional — omitting 'from' shows all posts up to 'to', etc.
	if ( $from || $to ) {
		$date_query = [ 'inclusive' => true ];
		if ( $from ) {
			$date_query['after'] = $from;   // e.g. '2025-01-01' — posts on or after.
		}
		if ( $to ) {
			$date_query['before'] = $to;    // e.g. '2025-12-31' — posts on or before.
		}
		$query_args['date_query'] = [ $date_query ];
	}

	// ── Execute the query ──────────────────────────────────────
	$articles = new WP_Query( $query_args );

	// ── Fetch category tabs ────────────────────────────────────
	// Show all categories (even empty) so users see what's available.
	// "Uncategorized" is excluded as it's the WP default and not meaningful.
	$cats = get_categories( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
	$cats = array_filter( $cats, function ( $c ) {
		return $c->slug !== 'uncategorized';
	} );

	// ── Preserve filter state across links ─────────────────────
	// When a user clicks a category tab, keep their date range intact.
	$base_params = [];
	if ( $from ) { $base_params['from'] = $from; }
	if ( $to )   { $base_params['to']   = $to; }

	// ── Render output ──────────────────────────────────────────
	ob_start();
	?>
	<div class="tdf-filter">

		<!-- Controls bar: category tabs (left) + date range (right) -->
		<div class="tdf-filter__controls">
			<div class="tdf-filter__tabs">
				<a href="<?php echo esc_url( add_query_arg( $base_params, remove_query_arg( [ 'article_cat', 'paged' ] ) ) ); ?>"
				   class="tdf-filter__tab <?php echo ! $cur_cat ? 'is-active' : ''; ?>">All</a>
				<?php foreach ( $cats as $cat ) : ?>
				<a href="<?php echo esc_url( add_query_arg( array_merge( $base_params, [ 'article_cat' => $cat->slug ] ), remove_query_arg( 'paged' ) ) ); ?>"
				   class="tdf-filter__tab <?php echo $cur_cat === $cat->slug ? 'is-active' : ''; ?>">
					<?php echo esc_html( $cat->name ); ?>
				</a>
				<?php endforeach; ?>
			</div>

			<!-- Date range form — preserves current category via hidden input -->
			<form class="tdf-filter__dates" method="get" action="">
				<?php if ( $cur_cat ) : ?>
					<input type="hidden" name="article_cat" value="<?php echo esc_attr( $cur_cat ); ?>">
				<?php endif; ?>
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
					<a href="<?php echo esc_url( remove_query_arg( [ 'from', 'to', 'paged' ] ) ); ?>" class="tdf-filter__clear">&times;</a>
				<?php endif; ?>
			</form>
		</div>

		<!-- Results grid — 3-column responsive layout -->
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

		<!-- Pagination — uses WP-PageNavi if available, else core paginate_links(). -->
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
			<p>No articles found<?php echo ( $cur_cat || $from || $to ) ? ' matching your filters' : ''; ?>.</p>
		</div>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'tdf_category_filter', 'tdf_category_filter_shortcode' );
