<?php
if (!defined('ABSPATH')) {
    exit;
}

class SimpleLanguageSwitcherSettings {
    private static $instance = null;
    private $option_name = 'sls_settings';
    private $page_slug = 'simple-language-switcher';
    private $default_settings = [
        'hide_untranslated' => 1,
        'show_flags' => 0,
        'show_names' => 1,
        'hide_current' => 0,
        'languages_title' => 'Available Languages'
    ];

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('Simple Language Switcher Settings', 'simple-language-switcher'),
            __('Language Switcher', 'simple-language-switcher'),
            'manage_options',
            $this->page_slug,
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting(
            'sls_options',
            $this->option_name,
            [
                'sanitize_callback' => [$this, 'sanitize_settings'],
                'default' => $this->default_settings
            ]
        );

        add_settings_section(
            'sls_main_section',
            __('Display Settings', 'simple-language-switcher'),
            [$this, 'render_settings_description'],
            $this->page_slug
        );

        $this->add_settings_fields();
    }

    public function render_settings_description() {
        echo '<p>' . esc_html__('Configure how the language switcher appears on your site.', 'simple-language-switcher') . '</p>';
    }

    private function add_settings_fields() {
        $fields = [
            'show_flags' => [
                'title' => __('Show Flags', 'simple-language-switcher'),
                'description' => __('Display country flags using Polylang\'s built-in flag system', 'simple-language-switcher')
            ],
            'show_names' => [
                'title' => __('Show Language Names', 'simple-language-switcher'),
                'description' => __('Display the full language names', 'simple-language-switcher')
            ],
            'hide_current' => [
                'title' => __('Hide Current Language', 'simple-language-switcher'),
                'description' => __('Hide the current language from the language list', 'simple-language-switcher')
            ],
            'hide_untranslated' => [
                'title' => __('Hide Untranslated Languages', 'simple-language-switcher'),
                'description' => __('Hide languages that don\'t have translations for the current content', 'simple-language-switcher')
            ],
            'languages_title' => [
                'title' => __('Languages Title', 'simple-language-switcher'),
                'description' => __('Custom title for the languages popup. The title will be available for translation in Polylang\'s String Translations.', 'simple-language-switcher'),
                'type' => 'text'
            ]
        ];

        foreach ($fields as $key => $field) {
            add_settings_field(
                'sls_' . $key,
                $field['title'],
                [$this, isset($field['type']) && $field['type'] === 'text' ? 'render_text_field' : 'render_checkbox_field'],
                $this->page_slug,
                'sls_main_section',
                [
                    'key' => $key,
                    'description' => $field['description']
                ]
            );
        }
    }

    public function render_checkbox_field($args) {
        $options = get_option($this->option_name, $this->default_settings);
        $value = isset($options[$args['key']]) ? $options[$args['key']] : $this->default_settings[$args['key']];
        ?>
        <input type="checkbox" 
               id="sls_<?php echo esc_attr($args['key']); ?>" 
               name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['key']); ?>]" 
               value="1" 
               <?php checked(1, $value); ?>>
        <label for="sls_<?php echo esc_attr($args['key']); ?>">
            <?php echo esc_html($args['description']); ?>
        </label>
        <?php
    }

    public function render_text_field($args) {
        $options = get_option($this->option_name, $this->default_settings);
        $value = isset($options[$args['key']]) ? $options[$args['key']] : $this->default_settings[$args['key']];
        ?>
        <input type="text" 
               id="sls_<?php echo esc_attr($args['key']); ?>" 
               name="<?php echo esc_attr($this->option_name); ?>[<?php echo esc_attr($args['key']); ?>]" 
               value="<?php echo esc_attr($value); ?>"
               class="regular-text">
        <p class="description"><?php echo esc_html($args['description']); ?></p>
        <?php
    }

    public function sanitize_settings($input) {
        $sanitized = [
            'hide_untranslated' => !empty($input['hide_untranslated']) ? 1 : 0,
            'show_flags' => !empty($input['show_flags']) ? 1 : 0,
            'show_names' => !empty($input['show_names']) ? 1 : 0,
            'hide_current' => !empty($input['hide_current']) ? 1 : 0,
            'languages_title' => sanitize_text_field($input['languages_title'])
        ];

        // Update the registered string in Polylang
        if (function_exists('pll_register_string')) {
            pll_register_string(
                'languages_title',
                $sanitized['languages_title'],
                'Simple Language Switcher',
                false
            );
        }

        return $sanitized;
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('sls_options');
                do_settings_sections($this->page_slug);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the settings
SimpleLanguageSwitcherSettings::get_instance();