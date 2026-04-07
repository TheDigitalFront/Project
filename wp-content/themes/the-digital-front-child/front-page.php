<?php
/* Front Page template for The Digital Front.*/

get_header();
// Loads the site header including nav and breaking news banner

// Query the 8 most recent articles (custom post type) for the hero and grid sections
$all_articles = new WP_Query([
	'post_type'      => 'article',
	'posts_per_page' => 8,  // hero consumes up to 4 (1 primary + 3 sidebar); remainder feeds the grid
	'orderby'        => 'date',
	'order'          => 'DESC',
]);

$total = $all_articles->found_posts;
// found_posts is the total matching count ignoring posts_per_page — used to branch hero layout

// Supplementary standard WP posts displayed in the "From the Blog" section
$recent_posts = new WP_Query([
	'post_type'      => 'post',
	'posts_per_page' => 4,
	'orderby'        => 'date',
	'order'          => 'DESC',
]);
?>

<main class="tdf-front">

	<!-- Hero
	     Branches on $total: empty state → 0 articles, solo → 1, full → 2+ -->
	<section class="tdf-hero">
		<div class="tdf-hero__bg"></div>
		<!-- tdf-hero__bg carries the decorative background colour / image via CSS -->
		<div class="tdf-container">

			<?php if ($total === 0) : ?>
				<!-- No articles published yet — show a branded placeholder with a CTA -->
				<div class="tdf-hero__empty">
					<p class="tdf-hero__eyebrow">The Digital Front</p>
					<h1 class="tdf-hero__empty-title">Tech. Tutorials. Insights.</h1>
					<p class="tdf-hero__empty-sub">We're building something great. Articles are on the way.</p>
					<div class="tdf-hero__actions">
						<a href="<?php echo esc_url(get_permalink(get_page_by_path('about-us'))); ?>" class="tdf-btn tdf-btn--primary">Learn About Us</a>
						<!-- get_page_by_path() resolves the page by its slug, safe across any install -->
					</div>
				</div>

			<?php else :
				$all_articles->the_post();
				// Advance the loop pointer to post #1 — this becomes the primary hero feature
			?>

				<div class="tdf-hero__layout <?php echo $total === 1 ? 'tdf-hero__layout--solo' : ''; ?>">
					<!-- --solo modifier collapses the sidebar column when only one article exists -->

					<!-- Primary feature — largest card, shows the most recent article -->
					<a href="<?php the_permalink(); ?>" class="tdf-hero__primary">
						<div class="tdf-hero__primary-img">
							<?php if (has_post_thumbnail()) : ?>
								<?php the_post_thumbnail('large'); ?>
								<!-- 'large' registered size keeps file weight reasonable at hero scale -->
							<?php else : ?>
								<div class="tdf-hero__placeholder"><span></span></div>
								<!-- CSS-styled placeholder div shown when no featured image is set -->
							<?php endif; ?>
							<div class="tdf-hero__primary-overlay">
								<!-- Gradient overlay positions the text over the image -->
								<span class="tdf-hero__badge">Featured</span>
								<h1 class="tdf-hero__primary-title"><?php the_title(); ?></h1>
								<?php if (has_excerpt() || get_the_content()) : ?>
									<p class="tdf-hero__primary-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
									<!-- Trim to 20 words to keep the overlay tidy -->
								<?php endif; ?>
								<div class="tdf-hero__primary-meta">
									<time><?php echo get_the_date(); ?></time>
									<?php $rt = get_field('reading_time'); ?>
									<!-- ACF field storing estimated reading time in minutes -->
									<?php if ($rt) : ?>
										<span>&middot; <?php echo esc_html($rt); ?> min read</span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</a>

					<!-- Side stack — up to 3 secondary articles shown beside the primary -->
					<div class="tdf-hero__sidebar">
						<?php
						$side_count = 0;
						// Counter caps the sidebar at 3 cards regardless of how many posts remain
						while ($all_articles->have_posts() && $side_count < 3) :
							$all_articles->the_post();
							$side_count++;
						?>
							<a href="<?php the_permalink(); ?>" class="tdf-hero__side-card">
								<div class="tdf-hero__side-img">
									<?php if (has_post_thumbnail()) : ?>
										<?php the_post_thumbnail('medium_large'); ?>
										<!-- medium_large (300×300 default) suits the compact sidebar cards -->
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

						<?php if ($total === 1) : ?>
							<!-- Solo fallback CTA — fills the sidebar space when only one article exists -->
							<div class="tdf-hero__side-cta">
								<p class="tdf-hero__eyebrow">The Digital Front</p>
								<p class="tdf-hero__side-cta-text">Your source for trending tech, tutorials, and digital insights.</p>
								<a href="<?php echo esc_url(get_permalink(get_page_by_path('about-us'))); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">About Us</a>
							</div>
						<?php endif; ?>
					</div>
				</div>

			<?php endif; ?>

		</div>
	</section>

	<?php wp_reset_postdata(); ?>
	<!-- Restore global $post to the main query after the hero's custom WP_Query loop -->

	<!-- All Articles
	     Filterable grid rendered by the [tdf_category_filter] shortcode.
	     per_page="6" controls how many articles appear per page of results.-->
	<section class="tdf-articles-section" id="articles">
		<div class="tdf-container">
			<h2 class="tdf-section__heading">All Articles</h2>
			<?php echo do_shortcode('[tdf_category_filter per_page="6"]'); ?>
			<!-- Shortcode registered in the theme/plugin; outputs category tabs + article grid + pagination -->
		</div>
	</section>

	<!-- Opinions
	     Shows up to 4 recent opinion CPT posts.
	     Hidden entirely when no opinions are published.-->
	<?php
	$opinions = new WP_Query([
		'post_type'      => 'opinion',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]);
	?>
	<?php if ($opinions->have_posts()) : ?>
		<section class="tdf-opinions" id="opinions">
			<div class="tdf-container">
				<div class="tdf-opinions__header">
					<h2 class="tdf-section__heading">Opinions</h2>
					<a href="<?php echo get_post_type_archive_link('opinion'); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">All Opinions &rarr;</a>
					<!-- get_post_type_archive_link() returns the registered archive URL for the opinion CPT -->
				</div>
				<div class="tdf-opinions__grid">
					<?php while ($opinions->have_posts()) : $opinions->the_post(); ?>
						<a href="<?php the_permalink(); ?>" class="tdf-opinions__card">
							<?php if (has_post_thumbnail()) : ?>
								<div class="tdf-opinions__img">
									<?php the_post_thumbnail('medium_large'); ?>
								</div>
							<?php endif; ?>
							<div class="tdf-opinions__body">
								<h3 class="tdf-opinions__title"><?php the_title(); ?></h3>
								<?php $pull_quote = get_field('pull_quote'); ?>
								<!-- ACF field: a short standout quote from the opinion piece -->
								<?php if ($pull_quote) : ?>
									<blockquote class="tdf-opinions__quote">&ldquo;<?php echo esc_html(wp_trim_words($pull_quote, 20)); ?>&rdquo;</blockquote>
									<!-- Pull quote preferred over excerpt when available; trimmed to 20 words -->
								<?php elseif (has_excerpt() || get_the_content()) : ?>
									<p class="tdf-opinions__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
									<!-- Falls back to the post excerpt when no pull quote is set -->
								<?php endif; ?>
								<div class="tdf-opinions__meta">
									<?php $bio = get_field('author_bio'); ?>
									<!-- ACF field: short bio or byline for the opinion author -->
									<?php if ($bio) : ?>
										<span class="tdf-opinions__author"><?php echo esc_html(wp_trim_words($bio, 6)); ?></span>
										<!-- Trim bio to 6 words for a compact inline byline -->
										<span>&middot;</span>
									<?php endif; ?>
									<time><?php echo get_the_date(); ?></time>
								</div>
								<?php
								$related = get_field('related_article');
								// ACF relationship field pointing to the article this opinion responds to
								if ($related) :
									$related_post = is_array($related) ? $related[0] : $related;
									// Normalise: ACF may return a single object or an array depending on field settings
								?>
									<span class="tdf-opinions__related">Re: <?php echo esc_html(get_the_title($related_post)); ?></span>
									<!-- "Re:" label links the opinion back to the article it references -->
								<?php endif; ?>
							</div>
						</a>
					<?php endwhile;
					wp_reset_postdata(); ?>
					<!-- wp_reset_postdata() restores global $post after the opinions loop -->
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- Reviews
	     Shows up to 3 recent review CPT posts with product image, stars, and excerpt.
	     Hidden entirely when no reviews are published.-->
	<?php
	$reviews = new WP_Query([
		'post_type'      => 'review',
		'posts_per_page' => 3,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]);
	?>
	<?php if ($reviews->have_posts()) : ?>
		<section class="tdf-reviews" id="reviews">
			<div class="tdf-container">
				<div class="tdf-reviews__header">
					<h2 class="tdf-section__heading">Latest Reviews</h2>
					<a href="<?php echo get_post_type_archive_link('review'); ?>" class="tdf-btn tdf-btn--outline tdf-btn--sm">All Reviews &rarr;</a>
				</div>
				<div class="tdf-reviews__grid">
					<?php while ($reviews->have_posts()) : $reviews->the_post();
						$rating        = get_field('rating');        // ACF: numeric score 1–5
						$product_name  = get_field('product_name');  // ACF: name of the reviewed product
						$product_image = get_field('product_image'); // ACF image array: url, sizes, alt, etc.
					?>
						<a href="<?php the_permalink(); ?>" class="tdf-reviews__card">
							<div class="tdf-reviews__card-img">
								<?php if ($product_image) : ?>
									<img src="<?php echo esc_url($product_image['sizes']['medium'] ?? $product_image['url']); ?>" alt="<?php echo esc_attr($product_image['alt'] ?: $product_name); ?>">
									<!-- Prefer the medium registered size; fall back to the full URL if unavailable -->
									<!-- alt falls back to product name when ACF alt text is empty -->
								<?php elseif (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('medium_large'); ?>
									<!-- Use the featured image as a secondary fallback when no product image is set -->
								<?php else : ?>
									<div class="tdf-reviews__placeholder"></div>
									<!-- CSS-styled grey box shown when neither product image nor thumbnail exists -->
								<?php endif; ?>
							</div>
							<div class="tdf-reviews__card-body">
								<?php if ($product_name) : ?>
									<span class="tdf-reviews__product-name"><?php echo esc_html($product_name); ?></span>
								<?php endif; ?>
								<h3 class="tdf-reviews__card-title"><?php the_title(); ?></h3>
								<?php if (has_excerpt() || get_the_content()) : ?>
									<p class="tdf-reviews__card-excerpt"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
									<!-- Trimmed to 15 words to fit the compact card layout -->
								<?php endif; ?>
								<div class="tdf-reviews__card-footer">
									<?php if ($rating) : ?>
										<div class="tdf-reviews__stars" aria-label="<?php echo esc_attr($rating); ?> out of 5">
											<!-- aria-label exposes the numeric score to screen readers -->
											<?php for ($i = 1; $i <= 5; $i++) : ?>
												<!-- Filled modifier applied to stars where i ≤ rating -->
												<span class="tdf-reviews__star <?php echo $i <= $rating ? 'tdf-reviews__star--filled' : ''; ?>">&#9733;</span>
												<!-- &#9733; is the filled star character ★ -->
											<?php endfor; ?>
										</div>
									<?php endif; ?>
									<time class="tdf-reviews__date"><?php echo get_the_date(); ?></time>
								</div>
							</div>
						</a>
					<?php endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!--Static topic cards -->
	<section class="tdf-topics">
		<div class="tdf-container">
			<div class="tdf-topics__header">
				<h2 class="tdf-section__heading">What We Cover</h2>
				<p class="tdf-topics__sub">Deep dives into the topics shaping the digital world.</p>
			</div>
			<div class="tdf-topics__grid">
				<!-- Each tdf-topic card is a static icon + title + description block -->
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
							<rect x="7" y="2" width="10" height="20" rx="2" />
							<path d="M11 18h2" />
						</svg>
						<!-- Mobile phone icon -->
					</div>
					<h3 class="tdf-topic__title">Mobile Devices</h3>
					<p class="tdf-topic__desc">Reviews, comparisons, and news on the latest smartphones and tablets.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
							<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z" />
							<path d="M15.5 8.5c-.83 0-1.5.67-1.5 1.5s.67 1.5 1.5 1.5S17 10.83 17 10s-.67-1.5-1.5-1.5zM8.5 8.5C7.67 8.5 7 9.17 7 10s.67 1.5 1.5 1.5S10 10.83 10 10s-.67-1.5-1.5-1.5zM12 17.5c2.33 0 4.31-1.46 5.11-3.5H6.89c.8 2.04 2.78 3.5 5.11 3.5z" />
						</svg>
						<!-- Smiley/Apple icon -->
					</div>
					<h3 class="tdf-topic__title">Apple</h3>
					<p class="tdf-topic__desc">iPhone, Mac, iPad, and the Apple ecosystem — updates and deep dives.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
							<circle cx="12" cy="12" r="10" />
							<path d="M2 12h20M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10A15.3 15.3 0 0112 2z" />
						</svg>
						<!-- Globe icon representing Google's web-wide reach -->
					</div>
					<h3 class="tdf-topic__title">Google</h3>
					<p class="tdf-topic__desc">Pixel, Android, Search, and everything across Google's platforms.</p>
				</div>
				<div class="tdf-topic">
					<div class="tdf-topic__icon">
						<svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
							<rect x="2" y="3" width="20" height="14" rx="2" />
							<path d="M8 21h8M12 17v4" />
						</svg>
						<!-- Monitor/screen icon representing Samsung's display lineup -->
					</div>
					<h3 class="tdf-topic__title">Samsung</h3>
					<p class="tdf-topic__desc">Galaxy phones, wearables, and Samsung's latest innovations.</p>
				</div>
			</div>
		</div>
	</section>

	<!-- Recent Posts (standard WP posts)
	     Numbered list of up to 4 blog posts; hidden when none are published.-->
	<?php if ($recent_posts->have_posts()) : ?>
		<section class="tdf-recent">
			<div class="tdf-container">
				<h2 class="tdf-section__heading">From the Blog</h2>
				<div class="tdf-recent__list">
					<?php $i = 1;
					while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
						<a href="<?php the_permalink(); ?>" class="tdf-recent__item">
							<span class="tdf-recent__num"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></span>
							<!-- str_pad zero-pads the counter to two digits: 01, 02, 03… -->
							<div class="tdf-recent__body">
								<h3 class="tdf-recent__title"><?php the_title(); ?></h3>
								<time class="tdf-recent__date"><?php echo get_the_date(); ?></time>
							</div>
							<span class="tdf-recent__arrow">&#8594;</span>
							<!-- &#8594; → right-arrow affordance indicating a clickable row -->
						</a>
					<?php $i++;
					endwhile;
					wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<!-- Newsletter
	     Static signup form — onsubmit="return false;" prevents real submission
	     until a back-end handler (e.g. Mailchimp action) is wired up.-->
	<section class="tdf-newsletter">
		<div class="tdf-container">
			<div class="tdf-newsletter__inner">
				<div class="tdf-newsletter__content">
					<h2 class="tdf-newsletter__title">Stay in the loop</h2>
					<p class="tdf-newsletter__desc">Get the latest articles, tutorials, and tech insights delivered straight to your inbox. No spam, ever.</p>
				</div>
				<form class="tdf-newsletter__form" action="#" method="post" onsubmit="return false;">
					<!-- action="#" and return false are placeholders; replace with real endpoint when ready -->
					<div class="tdf-newsletter__field">
						<input type="email" class="tdf-newsletter__input" placeholder="you@example.com" required>
						<!-- type="email" triggers native browser validation -->
						<button type="submit" class="tdf-btn tdf-btn--primary tdf-btn--sm">Subscribe</button>
					</div>
					<p class="tdf-newsletter__note">Join our readers. Unsubscribe anytime.</p>
				</form>
			</div>
		</div>
	</section>

	<!--Stats
	     Live counts pulled from the database at render time via wp_count_posts()
	     and get_terms(). The "Curiosity" stat is intentionally static (∞). -->
	<section class="tdf-stats">
		<div class="tdf-container">
			<div class="tdf-stats__grid">
				<?php
				$article_count  = wp_count_posts('article');
				$published      = $article_count->publish ?? 0;
				// ->publish holds the count of posts with status "publish"; nullsafe fallback to 0

				$opinion_count  = wp_count_posts('opinion');
				$opinions_pub   = $opinion_count->publish ?? 0;

				$review_count   = wp_count_posts('review');
				$reviews_pub    = $review_count->publish ?? 0;

				$categories     = get_terms(['taxonomy' => 'category', 'hide_empty' => false]);
				// hide_empty => false includes categories with no posts, giving an accurate total
				$cat_count      = is_array($categories) ? count($categories) : 0;
				// Guard against WP_Error return (e.g. invalid taxonomy) with is_array() check

				$authors        = count_users();
				// count_users() returns an array with 'total_users' and per-role breakdowns
				$author_count   = $authors['total_users'] ?? 1;
				// Minimum of 1 so the stat never reads "0 Contributors"
				?>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html($published); ?></span>
					<span class="tdf-stat__label">Articles</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html($opinions_pub); ?></span>
					<span class="tdf-stat__label">Opinions</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html($reviews_pub); ?></span>
					<span class="tdf-stat__label">Reviews</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html($cat_count); ?></span>
					<span class="tdf-stat__label">Topics</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number"><?php echo esc_html($author_count); ?></span>
					<span class="tdf-stat__label">Contributors</span>
				</div>
				<div class="tdf-stat">
					<span class="tdf-stat__number">&infin;</span>
					<!-- &infin; ∞ — static symbol, not a database value -->
					<span class="tdf-stat__label">Curiosity</span>
				</div>
			</div>
		</div>
	</section>

	<!-- Quick Links
	     Static navigation cards to key about-us sub-pages.
	     get_page_by_path() resolves each slug to its permalink safely.-->
	<section class="tdf-quicklinks">
		<div class="tdf-container">
			<h2 class="tdf-section__heading tdf-quicklinks__heading">Explore</h2>
			<div class="tdf-quicklinks__grid">
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('about-us/team'))); ?>" class="tdf-quicklink">
					<!-- Resolves the "team" child page under "about-us" -->
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9734;</span></div>
					<!-- &#9734; ☆ star icon -->
					<h3 class="tdf-quicklink__title">Meet the Team</h3>
					<p class="tdf-quicklink__desc">The people behind The Digital Front.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('about-us/mission'))); ?>" class="tdf-quicklink">
					<!-- Resolves the "mission" child page under "about-us" -->
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9998;</span></div>
					<!-- &#9998; ✎ pencil icon -->
					<h3 class="tdf-quicklink__title">Our Mission</h3>
					<p class="tdf-quicklink__desc">What drives us to create great content.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
				<a href="<?php echo esc_url(get_permalink(get_page_by_path('about-us'))); ?>" class="tdf-quicklink">
					<!-- Resolves the top-level "about-us" page -->
					<div class="tdf-quicklink__icon-wrap"><span class="tdf-quicklink__icon">&#9670;</span></div>
					<!-- &#9670; ◆ diamond icon -->
					<h3 class="tdf-quicklink__title">About Us</h3>
					<p class="tdf-quicklink__desc">Learn more about The Digital Front.</p>
					<span class="tdf-quicklink__arrow">&#8594;</span>
				</a>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
<!-- Loads the site footer and fires wp_footer() for enqueued scripts -->