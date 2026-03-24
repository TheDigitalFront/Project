<?php
/**
 * Single post/article template for The Digital Front.
 */

get_header();

while ( have_posts() ) : the_post();

$reading_time = get_field( 'reading_time' );
$source_url   = get_field( 'source_url' );
$video_embed  = get_field( 'video_embed' );
$post_type_obj = get_post_type_object( get_post_type() );
?>

<article class="tdf-single">

	<div class="tdf-container tdf-container--narrow">

		<!-- Breadcrumbs -->
		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<!-- Post header -->
		<header class="tdf-single__header">
			<div class="tdf-single__meta-top">
				<?php if ( $post_type_obj && $post_type_obj->name !== 'post' ) : ?>
					<span class="tdf-single__type"><?php echo esc_html( $post_type_obj->labels->singular_name ); ?></span>
				<?php endif; ?>
				<time class="tdf-single__date" datetime="<?php echo get_the_date( 'c' ); ?>">
					<?php echo get_the_date( 'F j, Y' ); ?>
				</time>
				<?php if ( $reading_time ) : ?>
					<span class="tdf-single__reading-time"><?php echo esc_html( $reading_time ); ?> min read</span>
				<?php endif; ?>
			</div>

			<h1 class="tdf-single__title"><?php the_title(); ?></h1>

			<?php if ( has_excerpt() ) : ?>
				<p class="tdf-single__excerpt"><?php echo get_the_excerpt(); ?></p>
			<?php endif; ?>
		</header>

	</div>

	<!-- Featured image — full width -->
	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="tdf-single__hero">
			<div class="tdf-single__hero-wrap">
				<?php the_post_thumbnail( 'full' ); ?>
			</div>
		</figure>
	<?php endif; ?>

	<div class="tdf-container tdf-container--narrow">

		<!-- Author -->
		<div class="tdf-single__author">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
			<div class="tdf-single__author-info">
				<span class="tdf-single__author-name"><?php the_author(); ?></span>
				<span class="tdf-single__author-role">Author</span>
			</div>
			<div class="tdf-single__share">
				<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode( get_permalink() ); ?>&text=<?php echo urlencode( get_the_title() ); ?>" target="_blank" rel="noopener" class="tdf-single__share-btn" aria-label="Share on X">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
				</a>
				<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode( get_permalink() ); ?>" target="_blank" rel="noopener" class="tdf-single__share-btn" aria-label="Share on LinkedIn">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
				</a>
				<button class="tdf-single__share-btn" aria-label="Copy link" onclick="navigator.clipboard.writeText(window.location.href);this.textContent='Copied!';setTimeout(()=>{this.textContent='Link'},1500)">Link</button>
			</div>
		</div>

		<!-- Video embed -->
		<?php if ( $video_embed ) : ?>
			<div class="tdf-single__video">
				<?php echo wp_oembed_get( $video_embed ) ?: '<a href="' . esc_url( $video_embed ) . '" target="_blank" rel="noopener">' . esc_html( $video_embed ) . '</a>'; ?>
			</div>
		<?php endif; ?>

		<!-- Content -->
		<div class="tdf-single__content">
			<?php the_content(); ?>
		</div>

		<!-- Source link -->
		<?php if ( $source_url ) : ?>
			<div class="tdf-single__source">
				<span class="tdf-single__source-label">Source</span>
				<a href="<?php echo esc_url( $source_url ); ?>" target="_blank" rel="noopener">
					<?php echo esc_html( wp_parse_url( $source_url, PHP_URL_HOST ) ); ?> &#8599;
				</a>
			</div>
		<?php endif; ?>

		<!-- Tags -->
		<?php $tags = get_the_tags(); if ( $tags ) : ?>
			<div class="tdf-single__tags">
				<?php foreach ( $tags as $tag ) : ?>
					<a href="<?php echo get_tag_link( $tag ); ?>" class="tdf-single__tag">#<?php echo esc_html( $tag->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<!-- Post navigation -->
		<nav class="tdf-single__nav">
			<?php
			$prev = get_previous_post();
			$next = get_next_post();
			?>
			<?php if ( $prev ) : ?>
			<a href="<?php echo get_permalink( $prev ); ?>" class="tdf-single__nav-link tdf-single__nav-link--prev">
				<span class="tdf-single__nav-label">&#8592; Previous</span>
				<span class="tdf-single__nav-title"><?php echo esc_html( $prev->post_title ); ?></span>
			</a>
			<?php endif; ?>
			<?php if ( $next ) : ?>
			<a href="<?php echo get_permalink( $next ); ?>" class="tdf-single__nav-link tdf-single__nav-link--next">
				<span class="tdf-single__nav-label">Next &#8594;</span>
				<span class="tdf-single__nav-title"><?php echo esc_html( $next->post_title ); ?></span>
			</a>
			<?php endif; ?>
		</nav>

	</div>
</article>

<?php
endwhile;
get_footer();
?>
