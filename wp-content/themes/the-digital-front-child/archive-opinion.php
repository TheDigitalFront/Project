<?php
/**
 * Archive template for Opinion CPT.
 * Displays a searchable, filterable, paginated grid of all opinions.
 */

get_header();

$paged = max( 1, get_query_var( 'paged', 1 ) );

// ── Sanitize search & filter inputs ──────────────────────────────
$search_query = isset( $_GET['opinion_s'] ) ? sanitize_text_field( $_GET['opinion_s'] ) : '';
$author_filter = isset( $_GET['author'] ) ? absint( $_GET['author'] ) : 0;

// ── Build query ──────────────────────────────────────────────────
$query_args = [
	'post_type'      => 'opinion',
	'posts_per_page' => 9,
	'paged'          => $paged,
	'orderby'        => 'date',
	'order'          => 'DESC',
];

if ( $search_query ) {
	$query_args['s'] = $search_query;
}

if ( $author_filter ) {
	$query_args['author'] = $author_filter;
}

$opinions = new WP_Query( $query_args );

// ── Get authors who have published opinions ──────────────────────
$opinion_authors = get_users( [
	'has_published_posts' => [ 'opinion' ],
	'orderby'             => 'display_name',
	'order'               => 'ASC',
] );

// ── Preserve filter state across pagination/links ────────────────
$filter_params = [];
if ( $search_query )  { $filter_params['opinion_s'] = $search_query; }
if ( $author_filter ) { $filter_params['author']     = $author_filter; }
?>

<main class="tdf-archive tdf-archive--opinions">
	<div class="tdf-container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<header class="tdf-archive__header">
			<h1 class="tdf-archive__title">Opinions</h1>
			<p class="tdf-archive__desc">Perspectives, analysis, and commentary from our contributors.</p>
		</header>

		<!-- Search & Filter Controls -->
		<div class="tdf-opinion-filter">
			<form class="tdf-opinion-filter__form" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'opinion' ) ); ?>">
				<div class="tdf-opinion-filter__search">
					<input type="text" name="opinion_s" value="<?php echo esc_attr( $search_query ); ?>"
						placeholder="Search opinions..." class="tdf-opinion-filter__input" aria-label="Search opinions">
					<button type="submit" class="tdf-btn tdf-btn--primary tdf-btn--sm">Search</button>
				</div>
				<?php if ( count( $opinion_authors ) > 1 ) : ?>
				<div class="tdf-opinion-filter__author-bar">
					<span class="tdf-opinion-filter__label">Author:</span>
					<a href="<?php echo esc_url( add_query_arg( $search_query ? [ 'opinion_s' => $search_query ] : [], get_post_type_archive_link( 'opinion' ) ) ); ?>"
					   class="tdf-opinion-filter__pill <?php echo ! $author_filter ? 'is-active' : ''; ?>">All</a>
					<?php foreach ( $opinion_authors as $author ) : ?>
					<a href="<?php echo esc_url( add_query_arg( array_merge( $search_query ? [ 'opinion_s' => $search_query ] : [], [ 'author' => $author->ID ] ), get_post_type_archive_link( 'opinion' ) ) ); ?>"
					   class="tdf-opinion-filter__pill <?php echo $author_filter === (int) $author->ID ? 'is-active' : ''; ?>">
						<?php echo esc_html( $author->display_name ); ?>
					</a>
					<?php endforeach; ?>
				</div>
				<?php endif; ?>
			</form>

			<?php if ( $search_query || $author_filter ) : ?>
				<div class="tdf-opinion-filter__active">
					<span class="tdf-opinion-filter__results">
						<?php echo esc_html( $opinions->found_posts ); ?> result<?php echo $opinions->found_posts !== 1 ? 's' : ''; ?>
						<?php if ( $search_query ) : ?>
							for &ldquo;<?php echo esc_html( $search_query ); ?>&rdquo;
						<?php endif; ?>
						<?php if ( $author_filter ) :
							$filtered_author = get_userdata( $author_filter );
						?>
							by <?php echo esc_html( $filtered_author ? $filtered_author->display_name : '' ); ?>
						<?php endif; ?>
					</span>
					<a href="<?php echo esc_url( get_post_type_archive_link( 'opinion' ) ); ?>" class="tdf-opinion-filter__clear">Clear all &times;</a>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $opinions->have_posts() ) : ?>
		<div class="tdf-opinions-archive__grid">
			<?php while ( $opinions->have_posts() ) : $opinions->the_post();
				$pull_quote      = get_field( 'pull_quote' );
				$author_bio      = get_field( 'author_bio' );
				$related         = get_field( 'related_article' );
				$related_post    = $related ? ( is_array( $related ) ? $related[0] : $related ) : null;
			?>
			<a href="<?php the_permalink(); ?>" class="tdf-opinions__card">
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="tdf-opinions__img">
						<?php the_post_thumbnail( 'medium_large' ); ?>
					</div>
				<?php endif; ?>
				<div class="tdf-opinions__body">
					<h3 class="tdf-opinions__title"><?php the_title(); ?></h3>
					<?php if ( $pull_quote ) : ?>
						<blockquote class="tdf-opinions__quote">&ldquo;<?php echo esc_html( wp_trim_words( $pull_quote, 20 ) ); ?>&rdquo;</blockquote>
					<?php elseif ( has_excerpt() || get_the_content() ) : ?>
						<p class="tdf-opinions__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 20 ); ?></p>
					<?php endif; ?>
					<div class="tdf-opinions__meta">
						<?php if ( $author_bio ) : ?>
							<span class="tdf-opinions__author"><?php echo esc_html( wp_trim_words( $author_bio, 6 ) ); ?></span>
							<span>&middot;</span>
						<?php endif; ?>
						<time><?php echo get_the_date(); ?></time>
					</div>
					<?php if ( $related_post ) : ?>
						<span class="tdf-opinions__related">Re: <?php echo esc_html( get_the_title( $related_post ) ); ?></span>
					<?php endif; ?>
				</div>
			</a>
			<?php endwhile; ?>
		</div>

		<div class="tdf-pagination">
			<?php
			if ( function_exists( 'wp_pagenavi' ) ) {
				wp_pagenavi( [ 'query' => $opinions ] );
			} else {
				$paginate_args = [
					'total'   => $opinions->max_num_pages,
					'current' => $paged,
					'format'  => 'page/%#%/',
					'base'    => trailingslashit( get_post_type_archive_link( 'opinion' ) ) . 'page/%#%/',
				];
				if ( $filter_params ) {
					$paginate_args['add_args'] = $filter_params;
				}
				echo paginate_links( $paginate_args );
			}
			?>
		</div>
		<?php else : ?>
			<p class="tdf-archive__empty">No opinions found<?php echo ( $search_query || $author_filter ) ? ' matching your filters' : ''; ?>. Check back soon.</p>
		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div>
</main>

<?php get_footer(); ?>
