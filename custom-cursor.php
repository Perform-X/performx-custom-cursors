<?php
/**
 * Plugin Name: PerformX Custom Cursors
 * Plugin URI: https://performx.me/
 * Description: Adds a modern, smooth-trailing animated custom cursor to your WordPress site.
 * Version: 1.0.0
 * Author: PerformX Performance Marketing Exodos
 * Author URI: https://performx.me/
 * License: GPL2
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function pcc_enqueue_cursor_assets() {
    // Inject the CSS directly into the site header for lightning-fast loading
    $custom_css = "
        body, a, button, input, select, textarea {
            cursor: none !important;
        }
        .pcc-cursor-dot {
            width: 8px;
            height: 8px;
            background-color: #ff4757;
            position: fixed;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            z-index: 999999;
            pointer-events: none;
        }
        .pcc-cursor-outline {
            width: 40px;
            height: 40px;
            border: 2px solid #ff4757;
            position: fixed;
            transform: translate(-50%, -50%);
            border-radius: 50%;
            z-index: 999998;
            pointer-events: none;
            transition: transform 0.1s ease-out, width 0.2s, height 0.2s;
        }
        .pcc-cursor-hover {
            width: 55px;
            height: 55px;
            background-color: rgba(213, 71, 87, 0.1);
        }
    ";
    wp_add_inline_style('wp-block-library', $custom_css);

    wp_enqueue_script(
        'pcc-cursor-script',
        plugin_dir_url(__FILE__) . 'cursor.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'pcc_enqueue_cursor_assets');

function pcc_inject_cursor_html() {
    echo '<div class="pcc-cursor-dot" id="pccDot"></div>';
    echo '<div class="pcc-cursor-outline" id="pccOutline"></div>';
}
add_action('wp_footer', 'pcc_inject_cursor_html');
