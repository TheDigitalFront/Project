<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="tdf-header" id="tdf-header">
	<div class="tdf-header__inner">
		<div class="tdf-header__brand">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="tdf-header__site-name">
					<?php bloginfo( 'name' ); ?>
				</a>
			<?php endif; ?>
		</div>

		<nav class="tdf-header__nav" id="tdf-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'the-digital-front-child' ); ?>">
			<?php
			wp_nav_menu( [
				'theme_location' => 'primary',
				'container'      => false,
				'menu_class'     => 'tdf-nav',
				'fallback_cb'    => 'tdf_fallback_menu',
				'depth'          => 2,
			] );
			?>
		</nav>

		<button class="tdf-header__toggle" id="tdf-menu-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="tdf-nav">
			<span class="tdf-header__toggle-bar"></span>
			<span class="tdf-header__toggle-bar"></span>
			<span class="tdf-header__toggle-bar"></span>
		</button>
	</div>
</header>

<?php echo do_shortcode( '[tdf_breaking_news]' ); ?>

<script>
(function () {
	var btn = document.getElementById('tdf-menu-toggle');
	var nav = document.getElementById('tdf-nav');
	var header = document.getElementById('tdf-header');
	if (!btn || !nav) return;

	btn.addEventListener('click', function () {
		var open = nav.classList.toggle('is-open');
		btn.classList.toggle('is-active', open);
		btn.setAttribute('aria-expanded', open);
		header.classList.toggle('is-nav-open', open);
	});

	// Close on click outside.
	document.addEventListener('click', function (e) {
		if (!header.contains(e.target) && nav.classList.contains('is-open')) {
			nav.classList.remove('is-open');
			btn.classList.remove('is-active');
			btn.setAttribute('aria-expanded', 'false');
			header.classList.remove('is-nav-open');
		}
	});

	// Close dropdown on Escape.
	document.addEventListener('keydown', function (e) {
		if (e.key === 'Escape' && nav.classList.contains('is-open')) {
			nav.classList.remove('is-open');
			btn.classList.remove('is-active');
			btn.setAttribute('aria-expanded', 'false');
			header.classList.remove('is-nav-open');
			btn.focus();
		}
	});
})();
</script>
