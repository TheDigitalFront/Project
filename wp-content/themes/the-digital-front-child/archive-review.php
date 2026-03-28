<?php
/**
 * Archive template for Review CPT.
 * Displays a searchable, filterable, paginated grid of all reviews.
 */

get_header();

$paged = max( 1, get_query_var( 'paged', 1 ) );

// ── Sanitize search & filter inputs ──────────────────────────────
$search_query  = isset( $_GET['review_s'] ) ? sanitize_text_field( $_GET['review_s'] ) : '';
$rating_filter = isset( $_GET['rating'] )   ? absint( $_GET['rating'] ) : 0;
if ( $rating_filter < 1 || $rating_filter > 5 ) {
	$rating_filter = 0;
}

// ── Build query ──────────────────────────────────────────────────
$query_args = [
	'post_type'      => 'review',
	'posts_per_page' => 9,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( $search_query ) {
	$query_args['s'] = $search_query;
}

if ( $rating_filter ) {
	$query_args['meta_query'] = [ [
		'key'     => 'rating',
		'value'   => $rating_filter,
		'compare' => '=',
		'type'    => 'NUMERIC',
	] ];
}

$reviews = new WP_Query( $query_args );

// ── Preserve filter state across pagination/links ────────────────
$filter_params = [];
if ( $search_query )  { $filter_params['review_s'] = $search_query; }
if ( $rating_filter ) { $filter_params['rating']    = $rating_filter; }
?>

<main class="tdf-archive tdf-archive--reviews">
	<div class="tdf-container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<header class="tdf-archive__header">
			<h1 class="tdf-archive__title">Reviews</h1>
			<p class="tdf-archive__desc">In-depth reviews of the latest tech products, gadgets, and software.</p>
		</header>

		<!-- Search & Filter Controls -->
		<div class="tdf-review-filter">
			<form class="tdf-review-filter__form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'review' ) ); ?>">
				<div class="tdf-review-filter__search">
					<input type="text" name="review_s" value="<?php echo esc_attr( $search_query ); ?>"
						placeholder="Search reviews..." class="tdf-review-filter__input" aria-label="Search reviews">
					<button type="submit" class="tdf-btn tdf-btn--primary tdf-btn--sm">Search</button>
				</div>
				<div class="tdf-review-filter__rating-bar">
					<span class="tdf-review-filter__label">Rating:</span>
					<a href="<?php echo esc_url( add_query_arg( $search_query ? [ 'review_s' => $search_query ] : [], get_post_type_archive_link( 'review' ) ) ); ?>"
					   class="tdf-review-filter__pill <?php echo ! $rating_filter ? 'is-active' : ''; ?>">All</a>
					<?php for ( $r = 5; $r >= 1; $r-- ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array_merge( $search_query ? [ 'review_s' => $search_query ] : [], [ 'rating' => $r ] ), get_post_type_archive_link( 'review' ) ) ); ?>"
					   class="tdf-review-filter__pill <?php echo $rating_filter === $r ? 'is-active' : ''; ?>">
						<?php echo esc_html( $r ); ?> &#9733;
					</a>
					<?php endfor; ?>
				</div>
			</form>

			<?php if ( $search_query || $rating_filter ) : ?>
				<div class="tdf-review-filter__active">
					<span class="tdf-review-filter__results">
						<?php echo esc_html( $reviews->found_posts ); ?> result<?php echo $reviews->found_posts !== 1 ? 's' : ''; ?>
						<?php if ( $search_query ) : ?>
							for &ldquo;<?php echo esc_html( $search_query ); ?>&rdquo;
						<?php endif; ?>
						<?php if ( $rating_filter ) : ?>
							rated <?php echo esc_html( $rating_filter ); ?>/5
						<?php endif; ?>
					</span>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'review' ) ); ?>" class="tdf-review-filter__clear">Clear all &times;</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $reviews->have_posts() ) : ?>
		<div class="tdf-reviews__grid">
			<?php while ( $reviews->have_posts() ) : $reviews->the_post();
				$rating        = get_field( 'rating' );
				$product_name  = get_field( 'product_name' );
				$product_image = get_field( 'product_image' );
			?>
			<a href="<?php the_permalink(); ?>" class="tdf-reviews__card">
				<div class="tdf-reviews__card-img">
					<?php if ( $product_image ) : ?>
						<img src="<?php echo esc_url( $product_image['sizes']['medium'] ?? $product_image['url'] ); ?>" alt="<?php echo esc_attr( $product_image['alt'] ?: $product_name ); ?>">
					<?php elseif ( has_post_thumbnail() ) : ?>
						<?php the_post_thumbnail( 'medium_large' ); ?>
					<?php else : ?>
						<div class="tdf-reviews__placeholder"></div>
					<?php endif; ?>
				</div>
				<div class="tdf-reviews__card-body">
					<?php if ( $product_name ) : ?>
						<span class="tdf-reviews__product-name"><?php echo esc_html( $product_name ); ?></span>
					<?php endif; ?>
					<h3 class="tdf-reviews__card-title"><?php the_title(); ?></h3>
					<?php if ( has_excerpt() || get_the_content() ) : ?>
						<p class="tdf-reviews__card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
					<?php endif; ?>
					<div class="tdf-reviews__card-footer">
						<?php if ( $rating ) : ?>
						<div class="tdf-reviews__stars" aria-label="<?php echo esc_attr( $rating ); ?> out of 5">
							<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
								<span class="tdf-reviews__star <?php echo $i <= $rating ? 'tdf-reviews__star--filled' : ''; ?>">&#9733;</span>
							<?php endfor; ?>
						</div>
						<?php endif; ?>
						<time class="tdf-reviews__date"><?php echo get_the_date(); ?></time>
					</div>
				</div>
			</a>
			<?php endwhile; ?>
		</div>

		<div class="tdf-pagination">
			<?php
			if ( function_exists( 'wp_pagenavi' ) ) {
				wp_pagenavi( [ 'query' => $reviews ] );
			} else {
				// Preserve filters across pagination links.
				$paginate_args = [
					'total'   => $reviews->max_num_pages,
					'current' => $paged,
					'format'  => 'page/%#%/',
					'base'    => trailingslashit( get_post_type_archive_link( 'review' ) ) . 'page/%#%/',
				];
				if ( $filter_params ) {
					$paginate_args['add_args'] = $filter_params;
				}
				echo paginate_links( $paginate_args );
			}
			?>
		</div>
		<?php else : ?>
			<p class="tdf-archive__empty">No reviews found<?php echo ( $search_query || $rating_filter ) ? ' matching your filters' : ''; ?>. Check back soon.</p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div>
</main>

<?php get_footer(); ?>
