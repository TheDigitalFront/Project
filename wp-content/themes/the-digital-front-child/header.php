<!-- Site header: outputs meta tags, site brand, primary nav, auth links and menu toggle -->
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1"> <!-- responsive viewport -->
	<?php wp_head(); // hook for enqueue scripts/styles and head output 
	?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); // hook for plugins to inject after <body> 
	?>

	<header class="tdf-header" id="tdf-header">
		<div class="tdf-header__inner">
			<div class="tdf-header__brand">
				<?php if (has_custom_logo()) : ?>
					<?php the_custom_logo(); ?>
				<?php else : ?>
					<a href="<?php echo esc_url(home_url('/')); ?>" class="tdf-header__site-name">
						<?php bloginfo('name'); ?>
					</a>
				<?php endif; ?>
			</div>
			<!-- Primary navigation: uses `primary` menu location or fallback -->

			<nav class="tdf-header__nav" id="tdf-nav" aria-label="<?php esc_attr_e('Primary Navigation', 'the-digital-front-child'); ?>">
				<?php
				// Primary navigation: render `primary` menu or fallback callback
				wp_nav_menu([
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'tdf-nav',
					'fallback_cb'    => 'tdf_fallback_menu',
					'depth'          => 2,
				]);
				?>
			</nav>
			<!-- Authentication: shows login/register or current user and logout -->
			<div class="tdf-header__auth">
				<?php if (is_user_logged_in()) : ?>
					<span class="tdf-header__user">Welcome, <?php echo esc_html(wp_get_current_user()->display_name); ?></span>
					<a href="<?php echo esc_url(wp_logout_url(home_url('/'))); ?>" class="tdf-btn tdf-btn--outline">Log out</a>
				<?php else : ?>
					<a href="<?php echo esc_url(wp_login_url()); ?>" class="tdf-btn tdf-btn--outline">Log in</a>
					<a href="<?php echo esc_url(home_url('/register/')); ?>" class="tdf-btn tdf-btn--primary">Register</a>
				<?php endif; ?>
			</div>

			<button class="tdf-header__toggle" id="tdf-menu-toggle" aria-label="Toggle menu" aria-expanded="false" aria-controls="tdf-nav">
				<span class="tdf-header__toggle-bar"></span>
				<span class="tdf-header__toggle-bar"></span>
				<span class="tdf-header__toggle-bar"></span>
			</button>
		</div>
	</header>

	<?php echo do_shortcode('[tdf_breaking_news]'); ?>

	<script>
		(function() {
			// Grab DOM elements used to control the mobile nav
			var btn = document.getElementById('tdf-menu-toggle'); // hamburger button
			var nav = document.getElementById('tdf-nav'); // nav container
			var header = document.getElementById('tdf-header'); // root header element
			// Abort if required elements are not present (prevents JS errors)
			if (!btn || !nav) return;

			// Toggle menu: open/close nav, update classes and ARIA state
			btn.addEventListener('click', function() {
				var open = nav.classList.toggle('is-open');
				btn.classList.toggle('is-active', open); // animate button
				btn.setAttribute('aria-expanded', open); // accessibility state
				header.classList.toggle('is-nav-open', open); // header style when open
			});

			// Close on click outside the header when nav is open
			document.addEventListener('click', function(e) {
				if (!header.contains(e.target) && nav.classList.contains('is-open')) {
					nav.classList.remove('is-open');
					btn.classList.remove('is-active');
					btn.setAttribute('aria-expanded', 'false');
					header.classList.remove('is-nav-open');
				}
			});

			// Close nav with Escape key and restore focus to the toggle
			document.addEventListener('keydown', function(e) {
				if (e.key === 'Escape' && nav.classList.contains('is-open')) {
					nav.classList.remove('is-open');
					btn.classList.remove('is-active');
					btn.setAttribute('aria-expanded', 'false');
					header.classList.remove('is-nav-open');
					btn.focus(); // return focus for keyboard users
				}
			});
		})();
	</script>