<?php

/**
 * Plugin Name:       TDF Reading Progress Bar
 * Description:       Displays a fixed reading progress bar at the top of single posts and articles. Shows how far the reader has scrolled through the content. Built for The Digital Front.
 * Version:           1.0.0
 * Author:            Robyn-Catherine Khan
 * Text Domain:       tdf-progress-bar
 * Admin Settings (Settings > Reading Progress Bar):
 *   - Enable / disable the bar
 *   - Accent colour (hex colour picker)
 */

if (! defined('ABSPATH')) {
    exit;
}

// CONSTANTS

define('TDF_PB_VERSION', '1.0.0');
define('TDF_PB_OPTION', 'tdf_progress_bar_settings');

// ACTIVATION - set defaults

register_activation_hook(__FILE__, 'tdf_pb_activate');

/**
 * Set default plugin settings on activation.
 *
 * @return void
 */
function tdf_pb_activate()
{
    if (! get_option(TDF_PB_OPTION)) {
        update_option(
            TDF_PB_OPTION,
            array(
                'enabled' => 1,
                'color'   => '#e63946',
            )
        );
    }
}

// =========================================================
// HELPERS
// =========================================================

/**
 * Return plugin settings with defaults.
 *
 * @return array
 */
function tdf_pb_get_settings()
{
    $defaults = array(
        'enabled' => 1,
        'color'   => '#e63946',
    );

    return wp_parse_args(get_option(TDF_PB_OPTION, array()), $defaults);
}

// =========================================================
// ADMIN SETTINGS PAGE
// =========================================================

add_action('admin_menu', 'tdf_pb_admin_menu');

/**
 * Register the settings page under Settings.
 *
 * @hooked admin_menu
 * @return void
 */
function tdf_pb_admin_menu()
{
    add_options_page(
        __('Reading Progress Bar', 'tdf-progress-bar'),
        __('Reading Progress Bar', 'tdf-progress-bar'),
        'manage_options',
        'tdf-progress-bar',
        'tdf_pb_settings_page'
    );
}

/**
 * Render settings page.
 *
 * @return void
 */
function tdf_pb_settings_page()
{
    if (isset($_POST['tdf_pb_save']) && check_admin_referer('tdf_pb_save_settings')) {
        update_option(
            TDF_PB_OPTION,
            array(
                'enabled' => isset($_POST['enabled']) ? 1 : 0,
                'color'   => sanitize_hex_color(isset($_POST['color']) ? wp_unslash($_POST['color']) : '#e63946') ?: '#e63946',
            )
        );

        echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>';
    }

    $s = tdf_pb_get_settings();
?>
    <div class="wrap">
        <h1><?php esc_html_e('Reading Progress Bar Settings', 'tdf-progress-bar'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('tdf_pb_save_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Progress Bar', 'tdf-progress-bar'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked(1, $s['enabled']); ?> />
                            <?php esc_html_e('Show reading progress bar on single post/article pages', 'tdf-progress-bar'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="tdf_pb_color"><?php esc_html_e('Bar Colour', 'tdf-progress-bar'); ?></label></th>
                    <td>
                        <input
                            type="color"
                            id="tdf_pb_color"
                            name="color"
                            value="<?php echo esc_attr($s['color']); ?>" />
                        <p class="description"><?php esc_html_e('The accent colour of the reading progress bar. Default: #e63946 (site accent red).', 'tdf-progress-bar'); ?></p>
                    </td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" name="tdf_pb_save" class="button-primary" value="<?php esc_attr_e('Save Settings', 'tdf-progress-bar'); ?>" />
            </p>
        </form>
        <hr>
        <h2><?php esc_html_e('How It Works', 'tdf-progress-bar'); ?></h2>
        <p><?php esc_html_e('The bar appears automatically at the top of every single post or article page. No shortcode is needed. The scroll progress is calculated as:', 'tdf-progress-bar'); ?></p>
        <code>progress = scrollY &divide; (documentHeight &minus; windowHeight) &times; 100</code>
        <p><?php esc_html_e('The bar width is updated on every scroll event using vanilla JavaScript - no jQuery required.', 'tdf-progress-bar'); ?></p>
    </div>
<?php
}

// =========================================================
// ENQUEUE ASSETS - only on single posts (is_single)
// =========================================================

add_action('wp_enqueue_scripts', 'tdf_pb_enqueue');

/**
 * Enqueue the progress bar CSS and JS.
 *
 * Uses is_single() to limit loading to single post/article pages only.
 * Inline CSS is generated from saved settings (accent colour).
 *
 * @hooked wp_enqueue_scripts
 * @return void
 */
function tdf_pb_enqueue()
{
    $s = tdf_pb_get_settings();
    if (! $s['enabled']) {
        return;
    }

    // Only on single posts (standard posts + CPTs).
    if (! is_single()) {
        return;
    }

    wp_enqueue_style(
        'tdf-progress-bar',
        plugin_dir_url(__FILE__) . 'css/progress.css',
        array(),
        TDF_PB_VERSION
    );

    wp_enqueue_script(
        'tdf-progress-bar',
        plugin_dir_url(__FILE__) . 'js/progress.js',
        array(),
        TDF_PB_VERSION,
        true
    );

    // Inline CSS for the bar element - driven by the saved colour setting.
    $color = esc_attr($s['color']);
    $css   = "#tdf-reading-progress { background: {$color}; }";
    wp_add_inline_style('tdf-progress-bar', $css);
}

// =========================================================
// INJECT BAR MARKUP INTO FOOTER
// =========================================================

add_action('wp_footer', 'tdf_pb_markup');

/**
 * Output the progress bar <div> element into the page footer.
 *
 * Only outputs on single post/article pages when the bar is enabled.
 *
 * @hooked wp_footer
 * @return void
 */
function tdf_pb_markup()
{
    $s = tdf_pb_get_settings();
    if (! $s['enabled'] || ! is_single()) {
        return;
    }

    echo '<div id="tdf-reading-progress" role="progressbar" aria-label="' . esc_attr__('Reading progress', 'tdf-progress-bar') . '" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>';
}
