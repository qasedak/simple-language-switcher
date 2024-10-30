<?php

/**
 * Plugin Name: Simple Language Switcher
 * Description: A lightweight language switcher plugin that displays a clean, modal-style language selection popup. Compatible with Polylang.
 * Version: 1.3
 * Author: MACSE
 * Text Domain: simple-language-switcher
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Generate language links as a popup
function display_translated_post_links() {
    ob_start(); // Start output buffering

    echo '<div class="popup">';
    echo '<button class="popbtn">' . __('Languages', 'simple-language-switcher') . '</button>';
    echo '<div class="popup-content">';
    echo '<div class="popup-title">' . __('Available Languages', 'simple-language-switcher') . '</div>';
    echo '<ul>';

    if (function_exists('pll_the_languages')) {
        $languages = pll_the_languages(array(
            'show_flags' => 0,
            'show_names' => 1,
            'hide_current' => 0,
            'raw' => 1
        ));

        if (!empty($languages)) {
            foreach ($languages as $language) {
                echo '<li><a href="' . esc_url($language['url']) . '" lang="' . esc_attr($language['slug']) . '">' 
                    . esc_html($language['name']) . '</a></li>';
            }
        }
    } else {
        echo '<li>' . __('Polylang not activated', 'simple-language-switcher') . '</li>';
    }

    echo '</ul>';
    echo '</div>'; // Close popup-content
    echo '</div>'; // Close popup

    return ob_get_clean(); // Return buffered content
}

// Register shortcode for language links
add_shortcode('translated_links', 'display_translated_post_links');

// Enqueue styles and scripts
function translated_links_enqueue_styles_and_scripts() {
    wp_enqueue_style('simple-language-switcher-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('simple-language-switcher-script', plugin_dir_url(__FILE__) . 'script.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'translated_links_enqueue_styles_and_scripts');

// Load text domain for translations
function translated_links_load_textdomain() {
    load_plugin_textdomain('simple-language-switcher', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'translated_links_load_textdomain');

// Ensure Polylang is active on plugin activation
function simple_language_switcher_activate() {
    if (!function_exists('pll_languages_list')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('This plugin requires Polylang to be installed and activated.', 'simple-language-switcher'));
    }
}
register_activation_hook(__FILE__, 'simple_language_switcher_activate');

