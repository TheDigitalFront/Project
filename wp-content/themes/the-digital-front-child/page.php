<?php
/**
 * Default page template for The Digital Front child theme.
 */

get_header();
?>

<main class="tdf-page">
	<div class="tdf-container tdf-container--narrow">

		<?php if ( function_exists( 'yoast_breadcrumb' ) ) : ?>
			<nav class="tdf-breadcrumbs" aria-label="Breadcrumb">
				<?php yoast_breadcrumb( '<p>', '</p>' ); ?>
			</nav>
		<?php endif; ?>

		<?php
		while ( have_posts() ) :
			the_post();
		?>
			<header class="tdf-page__header">
				<h1 class="tdf-page__title"><?php the_title(); ?></h1>
			</header>

			<div class="tdf-page__content">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>

	</div>
</main>

<?php get_footer(); ?>
