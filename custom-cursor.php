<?php
/**
 * Plugin Name: PerformX Custom Cursors
 * Plugin URI: https://performx.me/
 * Description: Adds a modern, smooth-trailing animated custom cursor with secure admin style configurations.
 * Version: 1.0.1
 * Author: PerformX Performance Marketing Exodos
 * Author URI: https://performx.me/
 * License: GPL2
 */

if (!defined('ABSPATH')) { exit; }

/**
 * 1. SECURE ADMINISTRATIVE DASHBOARD MENU SETUP
 */
add_action('admin_menu', 'pcc_create_settings_menu');
function pcc_create_settings_menu() {
    add_options_page(
        'PerformX Cursors Options',
        'PerformX Cursors',
        'manage_options', // Strictly enforces Administrator role checking
        'performx-custom-cursors',
        'pcc_render_settings_page'
    );
}

// Register settings values securely with custom sanitization callbacks
add_action('admin_init', 'pcc_register_plugin_settings');
function pcc_register_plugin_settings() {
    register_setting('pcc-settings-group', 'pcc_cursor_color', array(
        'sanitize_callback' => 'sanitize_hex_color' // Sanitizes input strictly to standard Hex values (#000000)
    ));
    register_setting('pcc-settings-group', 'pcc_cursor_shape', array(
        'sanitize_callback' => 'sanitize_key' // Strips out special characters, dangerous multi-byte tags, and spaces
    ));
}

// Inject WordPress native Color Picker assets safely into the admin layout
add_action('admin_enqueue_scripts', 'pcc_enqueue_admin_color_picker');
function pcc_enqueue_admin_color_picker($hook) {
    if ($hook !== 'settings_page_performx-custom-cursors') { return; }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('pcc-admin-js', false, array('wp-color-picker'), false, true);
    add_action('admin_footer', function() {
        echo '<script>jQuery(document).ready(function($){ $(".pcc-color-field").wpColorPicker(); });</script>';
    });
}

// Render the HTML form options view inside the WordPress Settings panel
function pcc_render_settings_page() {
    // Explicit security block against low-privilege capability traversal
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'performx-cursors'));
    }

    $current_color = get_option('pcc_cursor_color', '#ff4757');
    $current_shape = get_option('pcc_cursor_shape', 'classic-circle');
    ?>
    <div class="wrap">
        <h1>PerformX Custom Cursors Configurations</h1>
        <form method="post" action="options.php">
            <?php settings_fields('pcc-settings-group'); ?>
            <?php do_settings_sections('pcc-settings-group'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Cursor Theme Color</th>
                    <td>
                        <input type="text" name="pcc_cursor_color" value="<?php echo esc_attr($current_color); ?>" class="pcc-color-field" data-default-color="#ff4757" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Cursor Geometric Shape</th>
                    <td>
                        <select name="pcc_cursor_shape">
                            <option value="classic-circle" <?php selected($current_shape, 'classic-circle'); ?>>Classic Circle Pulse</option>
                            <option value="cyber-square" <?php selected($current_shape, 'cyber-square'); ?>>Cyber Angular Square</option>
                        </select>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * 2. COMPATIBILITY CHECK ENGINE (Evaluated at injection run-time)
 */
function pcc_should_load_cursor() {
    // Block injection inside core WordPress Admin Screens or Gutenberg Block Editor views
    if (is_admin()) { return false; }

    // Block injection inside Elementor Editor Workspace framework views
    if (class_exists('\Elementor\Plugin')) {
        if (\Elementor\Plugin::$instance->editor->is_edit_mode() || \Elementor\Plugin::$instance->preview->is_preview_mode() || isset($_GET['elementor-preview'])) {
            return false;
        }
    }
    return true;
}

/**
 * 3. FRONTEND INJECTION ENGINE (SECURELY ESCAPED & INTEGRITY ASSURED)
 */
add_action('wp_enqueue_scripts', 'pcc_enqueue_frontend_assets', 9999); // Late execution priority avoids theme override conflicts
function pcc_enqueue_frontend_assets() {
    if (!pcc_should_load_cursor()) { return; }

    $color = sanitize_hex_color(get_option('pcc_cursor_color', '#ff4757'));
    $shape = sanitize_key(get_option('pcc_cursor_shape', 'classic-circle'));

    $custom_css = "
        body, a, button, input, select, textarea { cursor: none !important; }
        .pcc-cursor-dot, .pcc-cursor-outline {
            position: fixed; transform: translate(-50%, -50%); z-index: 999999; pointer-events: none;
        }
        .pcc-cursor-dot { width: 8px; height: 8px; background-color: {$color}; border-radius: " . ($shape === 'cyber-square' ? '0%' : '50%') . "; }
        .pcc-cursor-outline {
            width: 40px; height: 40px; 
            border: 2px solid {$color}; 
            border-radius: " . ($shape === 'cyber-square' ? '0%' : '50%') . ";
            transition: transform 0.1s ease-out, width 0.2s, height 0.2s, border-radius 0.2s;
        }
        .pcc-cursor-hover { width: 55px; height: 55px; background-color: rgba(255, 71, 87, 0.15); transform: translate(-50%, -50%) rotate(45deg); }
    ";
    wp_add_inline_style('wp-block-library', $custom_css);

    // CACHE INVALIDATION: version tag handles LiteSpeed and local cache resets automatically
    wp_enqueue_script('pcc-script', plugin_dir_url(__FILE__) . 'cursor.js', array(), '1.0.1', true);
}

add_action('wp_footer', 'pcc_inject_structural_markup');
function pcc_inject_structural_markup() {
    if (!pcc_should_load_cursor()) { return; }
    echo '<div class="pcc-cursor-dot" id="pccDot"></div>';
    echo '<div class="pcc-cursor-outline" id="pccOutline"></div>';
}
