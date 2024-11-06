<?php

/**
 * Plugin Name: Simple Language Switcher
 * Description: A lightweight language switcher plugin that displays a clean, modal-style language selection popup. Compatible with Polylang.
 * Version: 1.5
 * Author: MACSE
 * Text Domain: simple-language-switcher
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Generate language links as a popup
function display_translated_post_links()
{
    // Check if Polylang is active first
    if (!function_exists('pll_the_languages')) {
        return '<div class="error">' . esc_html__('Polylang not activated', 'simple-language-switcher') . '</div>';
    }

    // Get current language info
    $current_lang = pll_current_language('slug');
    if (!$current_lang) {
        return ''; // Return empty if no language is set
    }

    ob_start();

    echo '<div class="popup">';
    // Use Polylang's native function to get current language name
    echo '<button class="popbtn" aria-label="' . esc_attr(__('Select Language', 'simple-language-switcher')) . '">';
    echo '<span class="language-icon">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" aria-hidden="true" role="img"><!-- Font Awesome Free 6.6.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M57.7 193l9.4 16.4c8.3 14.5 21.9 25.2 38 29.8L163 255.7c17.2 4.9 29 20.6 29 38.5l0 39.9c0 11 6.2 21 16 25.9s16 14.9 16 25.9l0 39c0 15.6 14.9 26.9 29.9 22.6c16.1-4.6 28.6-17.5 32.7-33.8l2.8-11.2c4.2-16.9 15.2-31.4 30.3-40l8.1-4.6c15-8.5 24.2-24.5 24.2-41.7l0-8.3c0-12.7-5.1-24.9-14.1-33.9l-3.9-3.9c-9-9-21.2-14.1-33.9-14.1L257 256c-11.1 0-22.1-2.9-31.8-8.4l-34.5-19.7c-4.3-2.5-7.6-6.5-9.2-11.2c-3.2-9.6 1.1-20 10.2-24.5l5.9-3c6.6-3.3 14.3-3.9 21.3-1.5l23.2 7.7c8.2 2.7 17.2-.4 21.9-7.5c4.7-7 4.2-16.3-1.2-22.8l-13.6-16.3c-10-12-9.9-29.5 .3-41.3l15.7-18.3c8.8-10.3 10.2-25 3.5-36.7l-2.4-4.2c-3.5-.2-6.9-.3-10.4-.3C163.1 48 84.4 108.9 57.7 193zM464 256c0-36.8-9.6-71.4-26.4-101.5L412 164.8c-15.7 6.3-23.8 23.8-18.5 39.8l16.9 50.7c3.5 10.4 12 18.3 22.6 20.9l29.1 7.3c1.2-9 1.8-18.2 1.8-27.5zM0 256a256 256 0 1 1 512 0A256 256 0 1 1 0 256z"/></svg>';
    echo '</span>';
    echo esc_html(pll_current_language('name')) . '</button>';
    echo '<div class="popup-content">';
    echo '<div class="popup-title">' . esc_html__('Available Languages', 'simple-language-switcher') . '</div>';
    echo '<ul>';

    $options = get_option('sls_settings');

    // Use Polylang's native arguments
    $args = array(
        'show_flags' => isset($options['show_flags']) ? $options['show_flags'] : 0,
        'show_names' => isset($options['show_names']) ? $options['show_names'] : 1,
        'hide_current' => isset($options['hide_current']) ? $options['hide_current'] : 0,
        'hide_if_no_translation' => isset($options['hide_untranslated']) ? $options['hide_untranslated'] : 1,
        'post_id' => get_the_ID(), // Let Polylang handle the post ID
        'raw' => 1
    );

    $languages = pll_the_languages($args);

    if (!empty($languages)) {
        foreach ($languages as $language) {
            echo '<li><a href="' . esc_url($language['url']) . '" lang="' . esc_attr($language['slug']) . '"';

            // Use Polylang's native current language detection
            if ($language['slug'] === $current_lang) {
                echo ' aria-current="true"';
            }

            echo '>';

            if (!empty($options['show_flags']) && !empty($language['flag'])) {
                echo wp_kses_post($language['flag']) . ' &nbsp;';
            }

            if (!empty($options['show_names'])) {
                echo esc_html($language['name']);
            }

            echo '</a></li>';
        }
    }

    echo '</ul>';
    echo '</div>'; // Close popup-content
    echo '</div>'; // Close popup

    return ob_get_clean();
}

// Register shortcode for language links
add_shortcode('translated_links', 'display_translated_post_links');

// Enqueue styles and scripts
function translated_links_enqueue_styles_and_scripts()
{
    wp_enqueue_style('simple-language-switcher-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('simple-language-switcher-script', plugin_dir_url(__FILE__) . 'script.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'translated_links_enqueue_styles_and_scripts');

// Load text domain for translations
function translated_links_load_textdomain()
{
    load_plugin_textdomain('simple-language-switcher', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'translated_links_load_textdomain');

// Ensure Polylang is active on plugin activation
function simple_language_switcher_activate()
{
    if (!function_exists('pll_languages_list')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(esc_html__('This plugin requires Polylang to be installed and activated.', 'simple-language-switcher'));
    }
}
register_activation_hook(__FILE__, 'simple_language_switcher_activate');

// Include admin settings file
if (is_admin()) {
    require_once plugin_dir_path(__FILE__) . 'admin-settings.php';
}
