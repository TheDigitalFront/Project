<?php
/**
 * Plugin Name: TDF Breaking News Banner
 * Plugin URI:  https://thedigitalfront.com
 * Description: A horizontally scrolling breaking-news ticker that displays the most recent posts across all public CPTs. Configurable via Settings > Breaking News or the [tdf_breaking_news] shortcode.
 * Version:     1.0.0
 * Author:      Terrence Murray
 * Author URI:  https://thedigitalfront.com
 * Text Domain: tdf-breaking-news
 *
 * === Shortcode Usage ===
 * [tdf_breaking_news]
 *   Renders the banner wherever placed. Accepts no attributes — all
 *   configuration is managed from Settings > Breaking News.
 *
 * === Admin Settings (Settings > Breaking News) ===
 *   - Enable Banner:     Show/hide the banner site-wide (default: on).
 *   - Scroll Speed:      Milliseconds between each scroll step (default: 3000).
 *   - Max Headlines:     Number of headlines to display, 1-20 (default: 5).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------ */
/*  1. Activation hook                                                 */
/* ------------------------------------------------------------------ */

function tdf_bn_activate() {
	add_option( 'tdf_bn_enabled', '1' );
	add_option( 'tdf_bn_speed', '3000' );
	add_option( 'tdf_bn_max', '5' );
}
register_activation_hook( __FILE__, 'tdf_bn_activate' );

/* ------------------------------------------------------------------ */
/*  2. Admin settings page                                             */
/* ------------------------------------------------------------------ */

function tdf_bn_admin_menu() {
	add_options_page(
		'Breaking News',
		'Breaking News',
		'manage_options',
		'tdf-breaking-news',
		'tdf_bn_settings_page'
	);
}
add_action( 'admin_menu', 'tdf_bn_admin_menu' );

function tdf_bn_register_settings() {
	register_setting( 'tdf_bn_settings', 'tdf_bn_enabled', [
		'type'              => 'string',
		'sanitize_callback' => function ( $v ) { return $v ? '1' : '0'; },
		'default'           => '1',
	] );
	register_setting( 'tdf_bn_settings', 'tdf_bn_speed', [
		'type'              => 'integer',
		'sanitize_callback' => 'absint',
		'default'           => 3000,
	] );
	register_setting( 'tdf_bn_settings', 'tdf_bn_max', [
		'type'              => 'integer',
		'sanitize_callback' => function ( $v ) {
			return max( 1, min( 20, absint( $v ) ) );
		},
		'default'           => 5,
	] );
}
add_action( 'admin_init', 'tdf_bn_register_settings' );

function tdf_bn_settings_page() {
	?>
	<div class="wrap">
		<h1>Breaking News Banner</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'tdf_bn_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row">Enable Banner</th>
					<td>
						<label>
							<input type="checkbox" name="tdf_bn_enabled" value="1"
								<?php checked( get_option( 'tdf_bn_enabled', '1' ), '1' ); ?>>
							Show the breaking-news banner on the front end
						</label>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tdf_bn_speed">Scroll Speed (ms)</label></th>
					<td>
						<input type="number" id="tdf_bn_speed" name="tdf_bn_speed"
							value="<?php echo esc_attr( get_option( 'tdf_bn_speed', 3000 ) ); ?>"
							min="500" max="10000" step="100" class="small-text">
						<p class="description">Milliseconds between each headline transition. Lower = faster.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="tdf_bn_max">Max Headlines</label></th>
					<td>
						<input type="number" id="tdf_bn_max" name="tdf_bn_max"
							value="<?php echo esc_attr( get_option( 'tdf_bn_max', 5 ) ); ?>"
							min="1" max="20" class="small-text">
						<p class="description">Number of recent headlines to display (1-20).</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/* ------------------------------------------------------------------ */
/*  3. Frontend: enqueue assets                                        */
/* ------------------------------------------------------------------ */

function tdf_bn_enqueue_assets() {
	if ( is_admin() ) {
		return;
	}

	wp_enqueue_style(
		'tdf-breaking-news',
		plugin_dir_url( __FILE__ ) . 'css/banner.css',
		[],
		'1.0.0'
	);

	wp_enqueue_script(
		'tdf-breaking-news',
		plugin_dir_url( __FILE__ ) . 'js/banner.js',
		[],
		'1.0.0',
		true
	);

	wp_localize_script( 'tdf-breaking-news', 'tdfBN', [
		'speed' => absint( get_option( 'tdf_bn_speed', 3000 ) ),
	] );
}
add_action( 'wp_enqueue_scripts', 'tdf_bn_enqueue_assets' );

/* ------------------------------------------------------------------ */
/*  4. WP_Query + markup                                               */
/* ------------------------------------------------------------------ */

function tdf_bn_render() {
	if ( get_option( 'tdf_bn_enabled', '1' ) !== '1' ) {
		return '';
	}

	$max   = absint( get_option( 'tdf_bn_max', 5 ) );
	$types = get_post_types( [ 'public' => true ], 'names' );
	unset( $types['attachment'] );

	$query = new WP_Query( [
		'post_type'      => array_values( $types ),
		'posts_per_page' => $max,
		'orderby'        => 'date',
		'order'          => 'DESC',
		'post_status'    => 'publish',
	] );

	if ( ! $query->have_posts() ) {
		return '';
	}

	$index = 0;
	ob_start();
	?>
	<div class="tdf-banner" aria-label="Trending Headlines">

		<div class="tdf-banner__inner">
			<div class="tdf-banner__label">
				<span class="tdf-banner__pulse"></span>
				<span class="tdf-banner__label-text">Trending</span>
			</div>

			<div class="tdf-banner__viewport">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
				<a href="<?php the_permalink(); ?>"
				   class="tdf-banner__slide <?php echo $index === 0 ? 'is-active' : ''; ?>"
				   data-index="<?php echo $index; ?>">
					<span class="tdf-banner__num"><?php echo str_pad( $index + 1, 2, '0', STR_PAD_LEFT ); ?></span>
					<span class="tdf-banner__title"><?php the_title(); ?></span>
					<span class="tdf-banner__cpt"><?php echo esc_html( get_post_type_object( get_post_type() )->labels->singular_name ); ?></span>
				</a>
				<?php $index++; endwhile; ?>
			</div>

			<div class="tdf-banner__progress">
				<div class="tdf-banner__progress-bar"></div>
			</div>

			<div class="tdf-banner__nav">
				<button class="tdf-banner__arrow" data-dir="-1" aria-label="Previous">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M9 2L4 7l5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
				<span class="tdf-banner__counter">
					<span class="tdf-banner__current">01</span>/<span class="tdf-banner__total"><?php echo str_pad( $index, 2, '0', STR_PAD_LEFT ); ?></span>
				</span>
				<button class="tdf-banner__arrow" data-dir="1" aria-label="Next">
					<svg width="14" height="14" viewBox="0 0 14 14" fill="none"><path d="M5 2l5 5-5 5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
				</button>
			</div>
		</div>

	</div>
	<?php
	wp_reset_postdata();
	return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/*  5. Shortcode                                                       */
/* ------------------------------------------------------------------ */

add_shortcode( 'tdf_breaking_news', 'tdf_bn_render' );

/* ------------------------------------------------------------------ */
/*  6. Placement                                                       */
/*  The banner is placed via the theme's header.php using               */
/*  do_shortcode('[tdf_breaking_news]'). It can also be placed          */
/*  manually in any post/page with the [tdf_breaking_news] shortcode.   */
/* ------------------------------------------------------------------ */
