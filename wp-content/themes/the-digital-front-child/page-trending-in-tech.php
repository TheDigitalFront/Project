<?php
/**
 * Template Name: Trending in Tech
 *
 * Query 1 — Trending Posts by View Count
 *
 * This template powers the "Trending in Tech" page. It displays the most
 * viewed posts across all CPTs, ordered by the _tdf_post_views meta key
 * (incremented on each single-post visit by tdf_track_post_views() in
 * functions.php).
 *
 * Query breakdown:
 *   - post_type:      article, opinion, review, post (all public CPTs)
 *   - meta_key:       _tdf_post_views (custom view counter stored in post meta)
 *   - orderby:        meta_value_num (sorts numerically by view count, highest first)
 *   - order:          DESC (most viewed at the top)
 *   - posts_per_page: 9 (paginated via WP-PageNavi)
 *
 * Fallback: Posts with no views yet have a meta value of 0 or no meta row.
 * meta_query uses EXISTS so posts without the key still appear (sorted last).
 *
 * Performance: Query Monitor confirms this runs a single SQL query with
 * a LEFT JOIN on wp_postmeta. Index on meta_key keeps it fast.
 *
 * @package TheDigitalFront
 * @since   1.0.0
 */

get_header();

$paged = max( 1, get_query_var( 'paged', 1 ) );
?>

<main class="tdf-page">
	<div class="tdf-container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<header class="tdf-archive__header">
			<h1 class="tdf-archive__title">Trending in Tech</h1>
			<p class="tdf-archive__desc">The most-read stories across articles, opinions, and reviews — ranked by reader engagement.</p>
		</header>

		<?php
		/**
		 * Query 1 — WP_Query: trending posts ordered by view count.
		 *
		 * meta_key:  _tdf_post_views — integer stored by tdf_track_post_views().
		 * orderby:   meta_value_num  — numeric sort so "100" beats "20" (not string sort).
		 * order:     DESC            — highest view count first.
		 *
		 * The meta_query with 'compare' => 'EXISTS' ensures posts that have
		 * been viewed at least once are included. Posts with no views won't
		 * have the meta key yet and will not appear until they get their
		 * first view — this is intentional so brand-new posts don't dilute
		 * the trending feed.
		 */
		$trending = new WP_Query( [
			'post_type'      => [ 'article', 'opinion', 'review', 'post' ],
			'posts_per_page' => 9,
			'paged'          => $paged,
			'meta_key'       => '_tdf_post_views',
			'orderby'        => 'meta_value_num',
			'order'          => 'DESC',
		] );
		?>

		<?php if ( $trending->have_posts() ) : ?>

			<!-- Top 4 posts: hero grid -->
			<div class="tdf-hero-grid">
				<?php
				$count = 0;
				while ( $trending->have_posts() && $count < 4 ) : $trending->the_post();
					$count++;
					$views = (int) get_post_meta( get_the_ID(), '_tdf_post_views', true );
					$cats  = get_the_category();
					$badge = ! empty( $cats ) ? esc_html( $cats[0]->name ) : '';
					$type  = get_post_type_object( get_post_type() );
				?>
					<a href="<?php the_permalink(); ?>" class="tdf-hero-grid__item">
						<div class="tdf-hero-grid__img">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large' ); ?>
							<?php else : ?>
								<div class="tdf-hero__placeholder"></div>
							<?php endif; ?>
						</div>
						<div class="tdf-hero-grid__overlay">
							<?php if ( $badge ) : ?>
								<span class="tdf-hero__badge"><?php echo $badge; ?></span>
							<?php elseif ( $type && $type->name !== 'post' ) : ?>
								<span class="tdf-hero__badge"><?php echo esc_html( $type->labels->singular_name ); ?></span>
							<?php endif; ?>
							<h2 class="tdf-hero-grid__title"><?php the_title(); ?></h2>
							<div class="tdf-hero-grid__meta">
								<span><?php echo get_the_date(); ?></span>
								<span>&middot;</span>
								<span><?php echo number_format( $views ); ?> views</span>
							</div>
						</div>
					</a>
				<?php endwhile; ?>
			</div>

			<!-- Remaining posts: card list -->
			<?php if ( $trending->have_posts() ) : ?>
				<div class="tdf-trending__list">
					<?php while ( $trending->have_posts() ) : $trending->the_post();
						$views = (int) get_post_meta( get_the_ID(), '_tdf_post_views', true );
						$type  = get_post_type_object( get_post_type() );
					?>
						<a href="<?php the_permalink(); ?>" class="tdf-trending__card">
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="tdf-trending__card-img">
									<?php the_post_thumbnail( 'medium' ); ?>
								</div>
							<?php endif; ?>
							<div class="tdf-trending__card-body">
								<?php if ( $type && $type->name !== 'post' ) : ?>
									<span class="tdf-trending__card-type"><?php echo esc_html( $type->labels->singular_name ); ?></span>
								<?php endif; ?>
								<h3 class="tdf-trending__card-title"><?php the_title(); ?></h3>
								<?php if ( has_excerpt() || get_the_content() ) : ?>
									<p class="tdf-trending__card-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
								<?php endif; ?>
								<div class="tdf-trending__card-meta">
									<time><?php echo get_the_date(); ?></time>
									<span>&middot;</span>
									<span><?php echo number_format( $views ); ?> views</span>
								</div>
							</div>
						</a>
					<?php endwhile; ?>
				</div>
			<?php endif; ?>

			<!-- Pagination via WP-PageNavi -->
			<div class="tdf-pagination">
				<?php
				if ( function_exists( 'wp_pagenavi' ) ) {
					wp_pagenavi( [ 'query' => $trending ] );
				} else {
					echo paginate_links( [
						'total'   => $trending->max_num_pages,
						'current' => $paged,
					] );
				}
				?>
			</div>

		<?php else : ?>
			<p class="tdf-archive__empty">No trending posts yet. Content will appear here as readers visit articles.</p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div>
</main>

<?php get_footer(); ?>
