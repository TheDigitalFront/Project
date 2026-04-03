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
    exit; /* prevents direct file access if someone tries to load the plugin file directly in the browser */
}

/* version constant used for cache busting when enqueuing CSS and JS assets */
define('TDF_PB_VERSION', '1.0.0');
/* option key used to store and retrieve plugin settings from the wp_options database table */
define('TDF_PB_OPTION', 'tdf_progress_bar_settings');

register_activation_hook(__FILE__, 'tdf_pb_activate'); /* fires only when the plugin is activated in WP admin */

function tdf_pb_activate()
{
    if (! get_option(TDF_PB_OPTION)) { /* only sets defaults if no settings exist yet — avoids overwriting saved settings on reactivation */
        update_option(
            TDF_PB_OPTION,
            array(
                'enabled' => 1,        /* bar is enabled by default */
                'color'   => '#e63946', /* default accent red matching the site colour */
            )
        );
    }
}

function tdf_pb_get_settings()
{
    $defaults = array(
        'enabled' => 1,
        'color'   => '#e63946',
    );

    return wp_parse_args(get_option(TDF_PB_OPTION, array()), $defaults); /* merges saved settings with defaults so missing keys always have a fallback value */
}

add_action('admin_menu', 'tdf_pb_admin_menu'); /* registers the settings page in the WP admin menu */

function tdf_pb_admin_menu()
{
    add_options_page(
        __('Reading Progress Bar', 'tdf-progress-bar'), /* page title shown in the browser tab */
        __('Reading Progress Bar', 'tdf-progress-bar'), /* menu label shown under Settings in the sidebar */
        'manage_options',                               /* only users with admin capabilities can access this page */
        'tdf-progress-bar',                             /* unique slug for this settings page URL */
        'tdf_pb_settings_page'                          /* callback function that renders the page content */
    );
}

function tdf_pb_settings_page()
{
    if (isset($_POST['tdf_pb_save']) && check_admin_referer('tdf_pb_save_settings')) { /* check_admin_referer verifies the nonce to protect against CSRF attacks */
        update_option(
            TDF_PB_OPTION,
            array(
                'enabled' => isset($_POST['enabled']) ? 1 : 0, /* checkbox returns nothing when unchecked so we default to 0 */
                'color'   => sanitize_hex_color(isset($_POST['color']) ? wp_unslash($_POST['color']) : '#e63946') ?: '#e63946', /* sanitize_hex_color ensures only valid hex values are saved */
            )
        );

        echo '<div class="notice notice-success is-dismissible"><p>Settings saved.</p></div>'; /* shows a success message at the top of the admin page after saving */
    }

    $s = tdf_pb_get_settings(); /* loads current settings to pre-fill the form fields */
?>
    <div class="wrap">
        <h1><?php esc_html_e('Reading Progress Bar Settings', 'tdf-progress-bar'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('tdf_pb_save_settings'); /* outputs a hidden nonce field to verify the form was submitted from this page */ ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable Progress Bar', 'tdf-progress-bar'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" value="1" <?php checked(1, $s['enabled']); /* checked() outputs the checked attribute if the saved value matches */ ?> />
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
                            value="<?php echo esc_attr($s['color']); /* esc_attr prevents XSS by escaping the output for use inside an HTML attribute */ ?>" />
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

add_action('wp_enqueue_scripts', 'tdf_pb_enqueue'); /* hooks into the front-end script/style loading system */

function tdf_pb_enqueue()
{
    $s = tdf_pb_get_settings();
    if (! $s['enabled']) {
        return; /* exits early if the bar is disabled in settings — no assets are loaded */
    }

    if (! is_single()) {
        return; /* exits early on any page that is not a single post or CPT — keeps assets off archives, home, and pages */
    }

    wp_enqueue_style(
        'tdf-progress-bar',
        plugin_dir_url(__FILE__) . 'css/progress.css', /* builds the full URL to the CSS file inside the plugin folder */
        array(),
        TDF_PB_VERSION /* version number forces browsers to fetch the new file after plugin updates */
    );

    wp_enqueue_script(
        'tdf-progress-bar',
        plugin_dir_url(__FILE__) . 'js/progress.js', /* builds the full URL to the JS file inside the plugin folder */
        array(),
        TDF_PB_VERSION,
        true /* true loads the script in the footer so the DOM is ready before the script runs */
    );

    $color = esc_attr($s['color']); /* sanitizes the saved colour before injecting it into CSS */
    $css   = "#tdf-reading-progress { background: {$color}; }"; /* dynamically sets the bar colour from the admin setting */
    wp_add_inline_style('tdf-progress-bar', $css); /* attaches the inline CSS directly after the enqueued stylesheet */
}

add_action('wp_footer', 'tdf_pb_markup'); /* injects the bar element just before the closing body tag */

function tdf_pb_markup()
{
    $s = tdf_pb_get_settings();
    if (! $s['enabled'] || ! is_single()) {
        return; /* double check — only outputs the bar if enabled and on a single post page */
    }

    /* outputs the bar div with ARIA attributes so screen readers can announce reading progress */
    echo '<div id="tdf-reading-progress" role="progressbar" aria-label="' . esc_attr__('Reading progress', 'tdf-progress-bar') . '" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>';
}
