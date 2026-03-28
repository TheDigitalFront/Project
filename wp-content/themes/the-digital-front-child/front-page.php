<?php
/**
 * Front Page template for The Digital Front.
 */

get_header();

$all_articles = new WP_Query( [
	'post_type'      => 'article',
	'posts_per_page' => 8,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );

$total = $all_articles->found_posts;

// Also grab recent posts (standard WP posts) as supplementary content.
$recent_posts = new WP_Query( [
	'post_type'      => 'post',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
] );
?>

<main class="tdf-front">

	<!-- Hero -->
	<section class="tdf-hero">
		<div class="tdf-hero__bg"></div>
		<div class="tdf-container">

		<?php if ( $total === 0 ) : ?>

			<div class="tdf-hero__empty">
				<p class="tdf-hero__eyebrow">The Digital Front</p>
				<h1 class="tdf-hero__empty-title">Tech. Tutorials. Insights.</h1>
				<p class="tdf-hero__empty-sub">We're building something great. Articles are on the way.</p>
				<div class="tdf-hero__actions">
					<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-us' ) ) ); ?>" class="tdf-btn tdf-btn--primary">Learn About Us</a>
				</div>
			</div>

		<?php else :
			$all_articles->the_post();
		?>

			<div class="tdf-hero__layout <?php echo $total === 1 ? 'tdf-hero__layout--solo' : ''; ?>">
				<!-- Primary feature -->
				<a href="<?php the_permalink(); ?>" class="tdf-hero__primary">
					<div class="tdf-hero__primary-img">
						<?php if ( has_post_thumbnail() ) : ?>
							<?php the_post_thumbnail( 'large' ); ?>
						<?php else : ?>
							<div class="tdf-hero__placeholder"><span></span></div>
						<?php endif; ?>
						<div class="tdf-hero__primary-overlay">
							<span class="tdf-hero__badge">Featured</span>
							<h1 class="tdf-hero__primary-title"><?php the_title(); ?></h1>
							<?php if ( has_excerpt() || get_the_content() ) : ?>
								<p class="tdf-hero__primary-excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
							<?php endif; ?>
							<div class="tdf-hero__primary-meta">
								<time><?php echo get_the_date(); ?></time>
								<?php $rt = get_field( 'reading_time' ); ?>
								<?php if ( $rt ) : ?>
									<span>&middot; <?php echo esc_html( $rt ); ?> min read</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</a>

				<!-- Side stack -->
				<div class="tdf-hero__sidebar">
					<?php
					$side_count = 0;
					while ( $all_articles->have_posts() && $side_count < 3 ) :
						$all_articles->the_post();
						$side_count++;
					?>
					<a href="<?php the_permalink(); ?>" class="tdf-hero__side-card">
						<div class="tdf-hero__side-img">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'medium_large' ); ?>
							<?php else : ?>
								<div class="tdf-hero__placeholder tdf-hero__placeholder--sm"><span></span></div>
							<?php endif; ?>
						</div>
						<div class="tdf-hero__side-body">
							<h3 class="tdf-hero__side-title"><?php the_title(); ?></h3>
							<time class="tdf-hero__side-date"><?php echo get_the_date(); ?></time>
						</div>
					</a>
					<?php endwhile; ?>

					<?php if ( $total === 1 ) : ?>
					<!-- CTA when only 1 article -->
					<div class="tdf-hero__side-cta">
						<p class="tdf-hero__eyebrow">The Digital Front</p>
						<p class="tdf-hero__side-cta-text">Your source for trending tech, tutorials, and digital insights.</p>
						<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-us' ) ) ); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">About Us</a>
					</div>
					<?php endif; ?>
				</div>
			</div>

		<?php endif; ?>

		</div>
	</section>

	<?php wp_reset_postdata(); ?>

	<!-- All Articles: filterable grid with pagination (Step 5) -->
	<section class="tdf-articles-section" id="articles">
		<div class="tdf-container">
			<h2 class="tdf-section__heading">All Articles</h2>
			<?php echo do_shortcode( '[tdf_category_filter per_page="6"]' ); ?>
		</div>
	</section>

	<!-- Opinions -->
	<?php
	$opinions = new WP_Query( [
		'post_type'      => 'opinion',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );
	?>
	<?php if ( $opinions->have_posts() ) : ?>
	<section class="tdf-opinions" id="opinions">
		<div class="tdf-container">
			<div class="tdf-opinions__header">
				<h2 class="tdf-section__heading">Opinions</h2>
				<a href="<?php echo get_post_type_archive_link( 'opinion' ); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">All Opinions &rarr;</a>
			</div>
			<div class="tdf-opinions__grid">
				<?php while ( $opinions->have_posts() ) : $opinions->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="tdf-opinions__card">
					<?php if ( has_post_thumbnail() ) : ?>
						<div class="tdf-opinions__img">
							<?php the_post_thumbnail( 'medium_large' ); ?>
						</div>
					<?php endif; ?>
					<div class="tdf-opinions__body">
						<h3 class="tdf-opinions__title"><?php the_title(); ?></h3>
						<?php $pull_quote = get_field( 'pull_quote' ); ?>
						<?php if ( $pull_quote ) : ?>
							<blockquote class="tdf-opinions__quote">&ldquo;<?php echo esc_html( wp_trim_words( $pull_quote, 20 ) ); ?>&rdquo;</blockquote>
						<?php elseif ( has_excerpt() || get_the_content() ) : ?>
							<p class="tdf-opinions__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
						<?php endif; ?>
						<div class="tdf-opinions__meta">
							<?php $bio = get_field( 'author_bio' ); ?>
							<?php if ( $bio ) : ?>
								<span class="tdf-opinions__author"><?php echo esc_html( wp_trim_words( $bio, 6 ) ); ?></span>
								<span>&middot;</span>
							<?php endif; ?>
							<time><?php echo get_the_date(); ?></time>
						</div>
						<?php
						$related = get_field( 'related_article' );
						if ( $related ) :
							$related_post = is_array( $related ) ? $related[0] : $related;
						?>
							<span class="tdf-opinions__related">Re: <?php echo esc_html( get_the_title( $related_post ) ); ?></span>
						<?php endif; ?>
					</div>
				</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- Reviews -->
	<?php
	$reviews = new WP_Query( [
		'post_type'      => 'review',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	] );
	?>
	<?php if ( $reviews->have_posts() ) : ?>
	<section class="tdf-reviews" id="reviews">
		<div class="tdf-container">
			<div class="tdf-reviews__header">
				<h2 class="tdf-section__heading">Latest Reviews</h2>
				<a href="<?php echo get_post_type_archive_link( 'review' ); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">All Reviews &rarr;</a>
			</div>
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
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- What We Cover -->
	<section class="tdf-topics">
		<div class="tdf-container">
			<div class="tdf-topics__header">
				<h2 class="tdf-section__heading">What We Cover</h2>
				<p class="tdf-topics__sub">Deep dives into the topics shaping the digital world.</p>
			</div>
			<div class="tdf-topics__grid">
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg>
					</div>
					<h3 class="tdf-topic__title">Mobile Devices</h3>
					<p class="tdf-topic__desc">Reviews, comparisons, and news on the latest smartphones and tablets.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/><path d="M15.5 8.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5S17 10.83 17 10s-.67-1.5-1.5-1.5zM8.5 8.5C7.67 8.5 7 9.17 7 10s.67 1.5 1.5 1.5S10 10.83 10 10s-.67-1.5-1.5-1.5zM12 17.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z"/></svg>
					</div>
					<h3 class="tdf-topic__title">Apple</h3>
					<p class="tdf-topic__desc">iPhone, Mac, iPad, and the Apple ecosystem — updates and deep dives.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10A15.3 15.3 0 0112 2z"/></svg>
					</div>
					<h3 class="tdf-topic__title">Google</h3>
					<p class="tdf-topic__desc">Pixel, Android, Search, and everything across Google's platforms.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
					</div>
					<h3 class="tdf-topic__title">Samsung</h3>
					<p class="tdf-topic__desc">Galaxy phones, wearables, and Samsung's latest innovations.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Recent Posts (standard WP posts) -->
	<?php if ( $recent_posts->have_posts() ) : ?>
	<section class="tdf-recent">
		<div class="tdf-container">
			<h2 class="tdf-section__heading">From the Blog</h2>
			<div class="tdf-recent__list">
				<?php $i = 1; while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="tdf-recent__item">
					<span class="tdf-recent__num"><?php echo str_pad( $i, 2, '0', STR_PAD_LEFT ); ?></span>
					<div class="tdf-recent__body">
						<h3 class="tdf-recent__title"><?php the_title(); ?></h3>
						<time class="tdf-recent__date"><?php echo get_the_date(); ?></time>
					</div>
					<span class="tdf-recent__arrow">&#8594;</span>
				</a>
				<?php $i++; endwhile; wp_reset_postdata(); ?>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<!-- Newsletter -->
	<section class="tdf-newsletter">
		<div class="tdf-container">
			<div class="tdf-newsletter__inner">
				<div class="tdf-newsletter__content">
					<h2 class="tdf-newsletter__title">Stay in the loop</h2>
					<p class="tdf-newsletter__desc">Get the latest articles, tutorials, and tech insights delivered straight to your inbox. No spam, ever.</p>
				</div>
				<form class="tdf-newsletter__form" action="#" method="post" onsubmit="return false;">
					<div class="tdf-newsletter__field">
						<input type="email" class="tdf-newsletter__input" placeholder="you@example.com" required>
						<button type="submit" class="tdf-btn tdf-btn--primary tdf-btn--sm">Subscribe</button>
					</div>
					<p class="tdf-newsletter__note">Join our readers. Unsubscribe anytime.</p>
				</form>
			</div>
		</div>
	</section>

	<!-- Stats -->
	<section class="tdf-stats">
		<div class="tdf-container">
			<div class="tdf-stats__grid">
				<?php
				$article_count  = wp_count_posts( 'article' );
				$published      = $article_count->publish ?? 0;
				$opinion_count  = wp_count_posts( 'opinion' );
				$opinions_pub   = $opinion_count->publish ?? 0;
				$review_count   = wp_count_posts( 'review' );
				$reviews_pub    = $review_count->publish ?? 0;
				$categories     = get_terms( [ 'taxonomy' => 'category', 'hide_empty' => false ] );
				$cat_count      = is_array( $categories ) ? count( $categories ) : 0;
				$authors        = count_users();
				$author_count   = $authors['total_users'] ?? 1;
				?>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html( $published ); ?></span>
					<span class="tdf-stat__label">Articles</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html( $opinions_pub ); ?></span>
					<span class="tdf-stat__label">Opinions</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html( $reviews_pub ); ?></span>
					<span class="tdf-stat__label">Reviews</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html( $cat_count ); ?></span>
					<span class="tdf-stat__label">Topics</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html( $author_count ); ?></span>
					<span class="tdf-stat__label">Contributors</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number">&infin;</span>
					<span class="tdf-stat__label">Curiosity</span>
				</div>
			</div>
		</div>
	</section>

	<!-- Quick Links -->
	<section class="tdf-quicklinks">
		<div class="tdf-container">
			<h2 class="tdf-section__heading tdf-quicklinks__heading">Explore</h2>
			<div class="tdf-quicklinks__grid">
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-us/team' ) ) ); ?>" class="tdf-quicklink">
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9734;</span></div>
					<h3 class="tdf-quicklink__title">Meet the Team</h3>
					<p class="tdf-quicklink__desc">The people behind The Digital Front.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-us/mission' ) ) ); ?>" class="tdf-quicklink">
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9998;</span></div>
					<h3 class="tdf-quicklink__title">Our Mission</h3>
					<p class="tdf-quicklink__desc">What drives us to create great content.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
				<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'about-us' ) ) ); ?>" class="tdf-quicklink">
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9670;</span></div>
					<h3 class="tdf-quicklink__title">About Us</h3>
					<p class="tdf-quicklink__desc">Learn more about The Digital Front.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
