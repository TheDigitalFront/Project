<?php
/*
Template Name: Trending in Tech
*/
get_header();
?>

<main class="tdf-page">

	<section class="tdf-hero">
	<div class="tdf-hero__bg">
		
	</div>

	<div class="tdf-container">
		
			<div class="tdf-topics__header">
				<h2 class="tdf-section__heading">Explore Trending Technologies:</h2>
				<p class="tdf-topics__sub">Tech moves fast—blink, and you’ll miss the next big thing. We're cutting through the noise to bring you the freshest news, gadget reviews, and digital trends. Whether it's AI agents or futuristic smart home tech, get your weekly dose of what's buzzing.</p>
			</div>

		<?php
		$query = new WP_Query(array(
			'post_type'      => 'article',
			'posts_per_page' => 8,
			'orderby'        => 'date',
			'order'          => 'DESC',
		));

		if ($query->have_posts()) :
		?>

	<div class="tdf-hero-grid">

	<?php while ($query->have_posts()) : $query->the_post(); ?>

	<a href="<?php the_permalink(); ?>" class="tdf-hero-grid__item">

		<div class="tdf-hero-grid__img">
			<?php if (has_post_thumbnail()) : ?>
				<?php the_post_thumbnail('large'); ?>
			<?php else : ?>
				<div class="tdf-hero__placeholder"></div>
			<?php endif; ?>
		</div>

		<div class="tdf-hero-grid__overlay">
			<span class="tdf-hero__badge">
				<?php echo get_the_category()[0]->name; ?>
			</span>

			<h2 class="tdf-hero-grid__title">
				<?php the_title(); ?>
			</h2>

			<div class="tdf-hero-grid__meta">
				<?php echo get_the_date(); ?>
			</div>
		</div>

	</a>

	<?php endwhile; ?>

	</div>

	<?php endif; wp_reset_postdata(); ?>

	</div>
	</section>
</main>

<?php get_footer(); ?>