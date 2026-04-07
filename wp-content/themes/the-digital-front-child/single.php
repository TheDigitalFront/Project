<?php
/* Single post/article template for The Digital Front.*/

get_header();
// Load the site header (outputs <head>, site header and navigation)

while (have_posts()) : the_post();
	// Loop start: sets up global post data for template tags below

	// ACF fields and helpers used by the template
	$reading_time   = get_field('reading_time'); // minutes
	$source_url     = get_field('source_url'); // external source link
	$video_embed    = get_field('video_embed'); // URL or oEmbed provider link
	$post_type_obj  = get_post_type_object(get_post_type()); // CPT labels
	$review_rating  = get_field('rating'); // numeric 1-5
	$product_name   = get_field('product_name');
	$product_image  = get_field('product_image'); // array: url, alt, etc.
	// Collect ACF/helper values used by this template (reading time, source, embed, review data).
?>

	<article class="tdf-single">
		<!-- Outer article wrapper; BEM block "tdf-single" scopes all child styles -->

		<div class="tdf-container tdf-container--narrow">
			<!-- Narrow container centres content to a readable max-width -->

			<!-- Breadcrumbs -->
			<?php if (function_exists('yoast_breadcrumb')) : ?>
				<!-- Only render breadcrumbs when the Yoast SEO plugin is active -->
				<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
					<?php yoast_breadcrumb('<p>', '</p>'); ?>
					<!-- Yoast outputs the full breadcrumb trail wrapped in <p> tags -->
				</nav>
			<?php endif; ?>

			<!-- Post header -->
			<header class="tdf-single__header">
				<!-- Groups the post type label, date, reading time, title and excerpt -->

				<div class="tdf-single__meta-top">
					<!-- Inline meta row shown above the title -->

					<?php if ($post_type_obj && $post_type_obj->name !== 'post') : ?>
						<!-- Show custom post type label when not a standard post -->
						<span class="tdf-single__type"><?php echo esc_html($post_type_obj->labels->singular_name); ?></span>
						<!-- e.g. "Review" or "Opinion" — omitted for default posts -->
					<?php endif; ?>

					<time class="tdf-single__date" datetime="<?php echo get_the_date('c'); ?>">
						<?php echo get_the_date('F j, Y'); ?><!-- Human-readable date format -->
					</time>

					<?php if ($reading_time) : ?>
						<!-- Only displayed when the ACF "reading_time" field has a value -->
						<span class="tdf-single__reading-time"><?php echo esc_html($reading_time); ?> min read</span>
					<?php endif; ?>
				</div>

				<h1 class="tdf-single__title"><?php the_title(); ?></h1>
				<!-- Primary H1 — only one should appear per page for SEO -->

				<?php if (has_excerpt()) : ?>
					<!-- Render the manually written excerpt when one exists; falls back to nothing -->
					<p class="tdf-single__excerpt"><?php echo get_the_excerpt(); ?></p>
				<?php endif; ?>
			</header>

		</div>

		<!-- Featured image — full width -->
		<?php if (has_post_thumbnail()) : ?>
			<!-- Full-width featured image for visual emphasis -->
			<figure class="tdf-single__hero">
				<!-- <figure> is semantically appropriate for an illustrative image -->
				<div class="tdf-single__hero-wrap">
					<!-- Inner wrapper allows CSS aspect-ratio / overflow control -->
					<?php the_post_thumbnail('full'); ?>
					<!-- Outputs <img> at the "full" registered image size -->
				</div>
			</figure>
		<?php endif; ?>

		<div class="tdf-container tdf-container--narrow">
			<!-- Re-enter the narrow container for all content below the hero image -->

			<!-- Author -->
			<div class="tdf-single__author">
				<!-- Author row: avatar, name/role label, and social share buttons -->

				<?php echo get_avatar(get_the_author_meta('ID'), 40); // small author avatar 
				?>
				<!-- get_avatar() returns an <img> tag; 40 is the pixel size -->

				<div class="tdf-single__author-info">
					<span class="tdf-single__author-name"><?php the_author(); ?></span>
					<!-- Displays the display name of the post author -->
					<span class="tdf-single__author-role">Author</span>
					<!-- Static "Author" label; could be replaced with a role ACF field -->
				</div>

				<div class="tdf-single__share"> <!-- social share buttons (Twitter, LinkedIn, copy link) -->

					<!-- X (Twitter) intent share link — passes URL and title as query params -->
					<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener" class="tdf-single__share-btn" aria-label="Share on X">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
							<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
						</svg>
						<!-- X logo SVG icon -->
					</a>

					<!-- LinkedIn share link — only requires the post URL -->
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener" class="tdf-single__share-btn" aria-label="Share on LinkedIn">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">
							<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
						</svg>
						<!-- LinkedIn logo SVG icon -->
					</a>

					<!-- Copy-link button writes the current URL to the clipboard via the Clipboard API, then gives brief "Copied!" feedback before resetting -->
					<button class="tdf-single__share-btn" aria-label="Copy link" onclick="navigator.clipboard.writeText(window.location.href);this.textContent='Copied!';setTimeout(()=>{this.textContent='Link'},1500)">Link</button>
				</div>
			</div>

			<!-- Video embed -->
			<?php if ($video_embed) : ?>
				<!-- If a video URL is provided, try to render via WP embed -->
				<div class="tdf-single__video">
					<?php echo wp_oembed_get($video_embed) ?: '<a href="' . esc_url($video_embed) . '" target="_blank" rel="noopener">' . esc_html($video_embed) . '</a>'; ?>
					<!--converts a supported URL (YouTube, Vimeo, etc.) into an embed iframe. Falls back to a plain hyperlink if otherwise -->
				</div>
			<?php endif; ?>

			<!-- Content -->
			<div class="tdf-single__content"> <!-- main post content (blocks or editor output) -->
				<?php the_content(); ?>
				<!-- Outputs the full post body; handles block markup and classic editor HTML -->
			</div>

			<!-- Review Product Card (Reviews only) -->
			<?php if (get_post_type() === 'review' && ($product_name || $product_image || $review_rating)) : ?>
				<!-- Shown only on the "review" CPT and only when at least one product field is set -->
				<!-- Review card: shows product image, name and rating when present -->
				<div class="tdf-review-card">

					<?php if ($product_image) : ?>
						<!-- Product image sourced from the ACF image field (returns an array) -->
						<div class="tdf-review-card__img">
							<img src="<?php echo esc_url($product_image['url']); ?>" alt="<?php echo esc_attr($product_image['alt'] ?: $product_name); ?>">
							<!-- Falls back to product name as alt text when no alt is stored in ACF -->
						</div>
					<?php endif; ?>

					<div class="tdf-review-card__body">

						<?php if ($product_name) : ?>
							<h3 class="tdf-review-card__product"><?php echo esc_html($product_name); ?></h3>
						<?php endif; ?>

						<?php if ($review_rating) : ?>
							<div class="tdf-review-card__rating">
								<div class="tdf-review-card__stars" aria-label="<?php echo esc_attr($review_rating); ?> out of 5 stars">
									<!-- aria-label exposes the numeric score to screen readers -->
									<?php for ($i = 1; $i <= 5; $i++) : ?>
										<!-- Loop 1–5 to render each star; filled class applied when i ≤ rating -->
										<span class="tdf-review-card__star <?php echo $i <= $review_rating ? 'tdf-review-card__star--filled' : ''; ?>">&#9733;</span>
										<!-- &#9733; is the filled star character ★ -->
									<?php endfor; ?>
								</div>
								<span class="tdf-review-card__score"><?php echo esc_html($review_rating); ?>/5</span>
								<!-- Numeric score displayed alongside the star icons -->
							</div>
						<?php endif; ?>

					</div>
				</div>
			<?php endif; ?>

			<!-- Opinions on this Article (Articles only) -->
			<?php if (get_post_type() === 'article') :
				// Load all `opinion` posts that reference this article via ACF relationship
				// The stored value is a serialized ID; using LIKE with quoted ID matches safely
				$linked_opinions = new WP_Query([
					'post_type'      => 'opinion',
					'posts_per_page' => -1, // return all matching opinions
					'meta_query'     => [[
						'key'     => 'related_article',
						'value'   => '"' . get_the_ID() . '"', // match serialized array entry
						'compare' => 'LIKE',
					]],
				]);
				// WP_Query returns opinion posts whose "related_article" meta contains this post's ID
			?>
				<?php if ($linked_opinions->have_posts()) : ?>
					<!-- List opinions that reference this article -->
					<div class="tdf-single__opinions">
						<span class="tdf-single__opinions-label">Opinions on this article</span>

						<div class="tdf-single__opinions-list">
							<?php while ($linked_opinions->have_posts()) : $linked_opinions->the_post(); ?>
								<!-- Each opinion links to its own single page -->
								<a href="<?php the_permalink(); ?>" class="tdf-single__opinions-item">
									<div class="tdf-single__opinions-body">
										<h4 class="tdf-single__opinions-title"><?php the_title(); // opinion title 
																				?></h4>
										<?php $pull_quote = get_field('pull_quote'); // optional short quote 
										?>
										<?php if ($pull_quote) : ?>
											<!-- Trim the pull quote to ~15 words to keep the card compact -->
											<p class="tdf-single__opinions-quote">&ldquo;<?php echo esc_html(wp_trim_words($pull_quote, 15)); // trim to ~15 words 
																							?>&rdquo;</p>
										<?php endif; ?>
										<div class="tdf-single__opinions-meta">
											<span><?php the_author(); // opinion author 
													?></span>
											<span>&middot;</span>
											<!-- Middle dot separator between author and date -->
											<time><?php echo get_the_date(); // opinion publish date 
													?></time>
										</div>
									</div>
									<span class="tdf-single__opinions-arrow">&#8594;</span>
								</a>
								<?php endwhile;
							wp_reset_postdata(); ?>// Reset main query postdata after custom loop
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<!-- Related Article (Opinions only) -->
			<?php if (get_post_type() === 'opinion') : // Opinions may point back to a related article
				$related = get_field('related_article'); // may return a single post object or an array depending on ACF field settings
				$related_post = $related ? (is_array($related) ? $related[0] : $related) : null; // Normalise: always use the first element if an array was returned
			?>
				<?php if ($related_post) : ?>
					<div class="tdf-single__related">
						<span class="tdf-single__related-label">Related Article</span>

						<!-- Card links to the related article's single page -->
						<a href="<?php echo esc_url(get_permalink($related_post)); ?>" class="tdf-single__related-card">

							<?php if (has_post_thumbnail($related_post)) : ?>
								<!-- Thumbnail of the related article at "medium" size -->
								<div class="tdf-single__related-img">
									<?php echo get_the_post_thumbnail($related_post, 'medium'); ?>
								</div>
							<?php endif; ?>

							<div class="tdf-single__related-body">
								<h3 class="tdf-single__related-title"><?php echo esc_html(get_the_title($related_post)); ?></h3>
								<p class="tdf-single__related-excerpt"><?php echo wp_trim_words(get_the_excerpt($related_post), 15); ?></p>
								<!-- Excerpt trimmed to 15 words for the compact card layout -->
								<time class="tdf-single__related-date"><?php echo get_the_date('F j, Y', $related_post); ?></time>
							</div>
						</a>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<!-- Source link -->
			<?php if ($source_url) : ?>
				<!-- External source link (opens in new tab) -->
				<div class="tdf-single__source">
					<span class="tdf-single__source-label">Source</span>
					<a href="<?php echo esc_url($source_url); ?>" target="_blank" rel="noopener">
						<?php echo esc_html(wp_parse_url($source_url, PHP_URL_HOST)); ?> &#8599;
						<!-- wp_parse_url extracts only the hostname for a clean display label -->
					</a>
				</div>
			<?php endif; ?>

			<!-- Tags -->
			<?php $tags = get_the_tags();
			if ($tags) : ?>
				<!-- Display tags as simple hashtag links -->
				<div class="tdf-single__tags">
					<?php foreach ($tags as $tag) : ?>
						<!-- Each tag links to its archive page; prefixed with # for styling -->
						<a href="<?php echo get_tag_link($tag); ?>" class="tdf-single__tag">#<?php echo esc_html($tag->name); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php
			/* Query 3 — Related Posts by Shared Tag
			 * Finds other posts (across all CPTs) that share at least one tag
			 * with the current post. Hidden entirely if no tags or no matches */
			// Get IDs of tags assigned to the current post (used to find related content)
			$current_tags = wp_get_post_tags(get_the_ID(), ['fields' => 'ids']);
			// 'fields' => 'ids' returns a flat array of integers rather than full WP_Term objects

			if (! empty($current_tags)) :
				// Query posts across selected CPTs that share any of these tag IDs
				$related_query = new WP_Query([
					'post_type'      => ['article', 'opinion', 'review', 'post'], // allowed content types
					'tag__in'        => $current_tags, // match any of the current tags
					'post__not_in'   => [get_the_ID()], // exclude the current post
					'posts_per_page' => 3, // limit number of suggestions
					'orderby'        => 'date',
					'order'          => 'DESC',
					// Returns the 3 most recent posts that share at least one tag
				]);

				if ($related_query->have_posts()) : ?>
					<div class="tdf-single__also-like">
						<span class="tdf-single__also-like-label">You may also like</span>

						<div class="tdf-single__also-like-list">
							<?php while ($related_query->have_posts()) : $related_query->the_post(); // set up postdata for this loop 
							?>
								<!-- Each related post is a linked card with thumbnail and title -->
								<a href="<?php the_permalink(); ?>" class="tdf-single__also-like-item">
									<?php if (has_post_thumbnail()) : // show thumbnail where applicable 
									?>
										<div class="tdf-single__also-like-img">
											<?php the_post_thumbnail('medium'); ?>
										</div>
									<?php endif; ?>
									<div class="tdf-single__also-like-body">
										<h4 class="tdf-single__also-like-title"><?php the_title(); //  post title 
																				?></h4>
										<time class="tdf-single__also-like-date"><?php echo get_the_date(); //post date 
																					?></time>
									</div>
								</a>
							<?php endwhile;
							// Restore global $post to main query after custom loop
							wp_reset_postdata(); ?>
						</div>
					</div>
			<?php endif;
			endif; ?>

			<!-- Post navigation to the bottom of the article for user browsing  other articles posted before and after current content-->
			<nav class="tdf-single__nav"> <!-- previous/next post links -->
				<?php
				$prev = get_previous_post(); // returns the chronologically older adjacent post
				$next = get_next_post();     // returns the chronologically newer adjacent post
				?>
				<?php if ($prev) : ?>
					<!-- Previous post link — only rendered when an older post exists -->
					<a href="<?php echo get_permalink($prev); ?>" class="tdf-single__nav-link tdf-single__nav-link--prev">
						<span class="tdf-single__nav-label">&#8592; Previous</span>
						<!-- &#8592; ← left-arrow directional indicator -->
						<span class="tdf-single__nav-title"><?php echo esc_html($prev->post_title); ?></span>
					</a>
				<?php endif; ?>
				<?php if ($next) : ?>
					<!-- Next post link — only rendered when a newer post exists -->
					<a href="<?php echo get_permalink($next); ?>" class="tdf-single__nav-link tdf-single__nav-link--next">
						<span class="tdf-single__nav-label">Next &#8594;</span>
						<!-- &#8594; → right-arrow directional indicator -->
						<span class="tdf-single__nav-title"><?php echo esc_html($next->post_title); ?></span>
					</a>
				<?php endif; ?>
			</nav>

		</div>
	</article>

<?php
endwhile; // End of The Loop 
get_footer(); // Load the site footer (outputs footer markup and closing </html>)
?>