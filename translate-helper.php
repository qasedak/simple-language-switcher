<?php

// Register display names of all users with posting capabilities for translation
function register_users_display_names_for_translation() {
    // Get plugin settings
    $options = get_option('sls_settings');
    
    // Check if username translation is enabled
    if (empty($options['translate_usernames'])) {
        return;
    }

    // Get all users with necessary fields
    $users = get_users([
        'fields' => ['display_name', 'ID', 'user_login'],
        'capability__in' => ['edit_posts', 'publish_posts']
    ]);

    // Register each user's display name for translation
    foreach ($users as $user) {
        pll_register_string(
            'User Display Name - ' . $user->user_login,
            $user->display_name,
            'simple-language-switcher'
        );
    }
}
add_action('admin_init', 'register_users_display_names_for_translation');

// Filter to replace author's name with translated display name
function replace_author_with_display_name($display_name) {
    // Get plugin settings
    $options = get_option('sls_settings');
    
    // Check if username translation is enabled
    if (empty($options['translate_usernames'])) {
        return $display_name;
    }

    global $post;
    
    // First check if we have a valid post and post_author
    if (!$post || !$post->post_author) {
        return $display_name;
    }
    
    // Get the post author's data
    $author = get_userdata($post->post_author);
    
    // Get the translated display name if available, otherwise fallback to original display name
    $translated_name = pll__($display_name, 'simple-language-switcher');
    return !empty($translated_name) ? $translated_name : $author->display_name;
}
add_filter('the_author', 'replace_author_with_display_name');
add_filter('get_the_author_display_name', 'replace_author_with_display_name');

// Function to register the translatable strings
function register_translatable_strings() {
    $strings = get_option('sls_translatable_strings', []);
    foreach ($strings as $string) {
        if (!empty($string['identifier']) && !empty($string['value'])) {
            pll_register_string($string['identifier'], $string['value'], 'simple-language-switcher');
        }
    }
}
add_action('admin_init', 'register_translatable_strings');

// Function to handle the translatable string shortcode
function handle_translatable_string_shortcode($atts, $content, $tag) {
    $identifier = substr($tag, 4);
    
    $strings = get_option('sls_translatable_strings', []);
    foreach ($strings as $string) {
        if ($string['identifier'] === $identifier) {
            return pll__($string['value'], 'simple-language-switcher');
        }
    }
    return '';
}

// Function to register the translatable string block
function register_translatable_string_block() {
    register_block_type( __DIR__ . '/blocks/translatable-string', [
        'render_callback' => 'render_translatable_string_block',
        'editor_script' => 'sls-translatable-string-editor',
        'editor_style' => 'sls-translatable-string-editor',
    ]);

    wp_register_script(
        'sls-translatable-string-editor',
        plugins_url('blocks/translatable-string/build/index.js', __FILE__),
        ['wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'wp-components', 'wp-api-fetch'],
        filemtime(plugin_dir_path(__FILE__) . 'blocks/translatable-string/build/index.js')
    );

    wp_register_style(
        'sls-translatable-string-editor',
        plugins_url('blocks/translatable-string/editor.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'blocks/translatable-string/editor.css')
    );
}
add_action('init', 'register_translatable_string_block');

// Function to render the translatable string block
function render_translatable_string_block( $attributes ) {
    if ( empty( $attributes['identifier'] ) ) {
        return '';
    }

    $strings = get_option( 'sls_translatable_strings', [] );
    foreach ( $strings as $string ) {
        if ( $string['identifier'] === $attributes['identifier'] ) {
            return pll__( $string['value'], 'simple-language-switcher' );
        }
    }
    return '';
}

// Shortcodes function for translatable strings (used for old wordpress themes that use shortcodes)
function register_translatable_string_shortcodes() {
    $strings = get_option('sls_translatable_strings', []);
    foreach ($strings as $string) {
        if (!empty($string['identifier'])) {
            add_shortcode('SLS-' . $string['identifier'], 'handle_translatable_string_shortcode');
        }
    }
}
add_action('init', 'register_translatable_string_shortcodes');

// Register REST route for fetching translatable strings
function register_translatable_strings_rest_route() {
    register_rest_route('simple-language-switcher/v1', '/strings', [
        'methods' => 'GET',
        'callback' => function() {
            return get_option('sls_translatable_strings', []);
        },
        'permission_callback' => '__return_true',
        'args' => [],
    ]);
}
add_action('rest_api_init', 'register_translatable_strings_rest_route');

