<?php
/**
 * Archive template for Article CPT.
 * Displays a paginated grid of all articles with WP-PageNavi.
 */

get_header();
?>

<main class="tdf-archive">
	<div class="tdf-container">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<header class="tdf-archive__header">
			<h1 class="tdf-archive__title">Articles</h1>
			<p class="tdf-archive__desc">All published articles on The Digital Front.</p>
		</header>

		<?php echo do_shortcode( '[tdf_category_filter per_page="9"]' ); ?>

	</div>
</main>

<?php get_footer(); ?>
