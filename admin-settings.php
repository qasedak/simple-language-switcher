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
        'translate_usernames' => 1,
        'enable_shortcodes' => 1
    ];
    private $strings_option_name = 'sls_translatable_strings';

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        
        // Add migration for new settings
        $this->maybe_migrate_settings();
    }

    private function maybe_migrate_settings() {
        $current_settings = get_option($this->option_name);
        
        if ($current_settings && !isset($current_settings['enable_shortcodes'])) {
            // Preserve existing settings and add new default
            $updated_settings = array_merge(
                $current_settings,
                ['enable_shortcodes' => $this->default_settings['enable_shortcodes']]
            );
            
            update_option($this->option_name, $updated_settings);
        }
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

        // Register strings settings separately
        register_setting(
            'sls_strings_options', // New option group
            $this->strings_option_name,
            [
                'sanitize_callback' => [$this, 'sanitize_translatable_strings'],
                'default' => []
            ]
        );

        add_settings_section(
            'sls_main_section',
            __('Display Settings', 'simple-language-switcher'),
            [$this, 'render_settings_description'],
            $this->page_slug
        );

        $this->add_settings_fields();

        // Add new section for translatable strings
        add_settings_section(
            'sls_strings_section',
            __('Translatable Strings', 'simple-language-switcher'),
            [$this, 'render_strings_description'],
            $this->page_slug . '_strings'
        );
    }

    public function render_settings_description() {
        echo '<p>' . esc_html__('Configure how the language switcher appears on your site.', 'simple-language-switcher') . '</p>';
        echo '<p>' . esc_html__('Note: You can use the shortcode [simple-language-switcher] to display the language switcher in any post or page and the Language Popup\'s title (Available Languages) is translatable in Polylang settings.', 'simple-language-switcher') . '</p>';
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
            'translate_usernames' => [
                'title' => __('Translate Author Display Names', 'simple-language-switcher'),
                'description' => __('Enable translation of author display names across languages through Polylang', 'simple-language-switcher')
            ],
            'enable_shortcodes' => [
                'title' => __('Enable Shortcodes Support', 'simple-language-switcher'),
                'description' => __('Enable support for shortcodes (disable if you only use blocks for better performance)', 'simple-language-switcher')
            ]
        ];

        foreach ($fields as $key => $field) {
            add_settings_field(
                'sls_' . $key,
                $field['title'],
                [$this, 'render_checkbox_field'],
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

    public function sanitize_settings($input) {
        return [
            'hide_untranslated' => !empty($input['hide_untranslated']) ? 1 : 0,
            'show_flags' => !empty($input['show_flags']) ? 1 : 0,
            'show_names' => !empty($input['show_names']) ? 1 : 0,
            'hide_current' => !empty($input['hide_current']) ? 1 : 0,
            'translate_usernames' => !empty($input['translate_usernames']) ? 1 : 0,
            'enable_shortcodes' => !empty($input['enable_shortcodes']) ? 1 : 0
        ];
    }

    public function render_strings_description() {
        echo '<p>' . __('Add translatable strings that you can use in your templates. you can display these strings by shortcode or using the block "Translatable String" in the Gutenberg editor.<br>The identifier is uesed as a key and it can be numbers, letters and underscores only in <strong>english</strong>.<br><strong>Note:</strong> if you use a WordPress Block theme, it\'s recommended to use only the block "Translatable String" in the Gutenberg editor and disable the shortcodes support in the Display settings Tab.', 'simple-language-switcher') . '</p>';
        echo '<p>' . __('After adding Translatable string you can translate them through Polylang <a href="' . esc_url(admin_url('admin.php?page=mlang_strings')) . '">here</a>.', 'simple-language-switcher') . '</p>';
    }

    public function sanitize_translatable_strings($input) {
        if (!is_array($input)) {
            return [];
        }
        
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = [
                'identifier' => sanitize_key($value['identifier']),
                'value' => sanitize_text_field($value['value'])
            ];
        }
        return $sanitized;
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="?page=<?php echo esc_attr($this->page_slug); ?>" class="nav-tab <?php echo empty($_GET['tab']) ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Display Settings', 'simple-language-switcher'); ?>
                </a>
                <a href="?page=<?php echo esc_attr($this->page_slug); ?>&tab=strings" class="nav-tab <?php echo isset($_GET['tab']) && $_GET['tab'] === 'strings' ? 'nav-tab-active' : ''; ?>">
                    <?php esc_html_e('Translatable Strings', 'simple-language-switcher'); ?>
                </a>
            </h2>

            <?php if (isset($_GET['tab']) && $_GET['tab'] === 'strings'): ?>
                <div class="strings-manager">
                    <form action="options.php" method="post">
                        <?php
                        settings_fields('sls_strings_options');
                        do_settings_sections($this->page_slug . '_strings');
                        $strings = get_option($this->strings_option_name, []);
                        ?>
                        <table class="widefat" id="translatable-strings-table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Identifier', 'simple-language-switcher'); ?></th>
                                    <th><?php esc_html_e('Value', 'simple-language-switcher'); ?></th>
                                    <th><?php esc_html_e('Shortcode', 'simple-language-switcher'); ?></th>
                                    <th><?php esc_html_e('Action', 'simple-language-switcher'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($strings as $index => $string): ?>
                                <tr>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr($this->strings_option_name); ?>[<?php echo esc_attr($index); ?>][identifier]" 
                                               value="<?php echo esc_attr($string['identifier']); ?>" required>
                                    </td>
                                    <td>
                                        <input type="text" name="<?php echo esc_attr($this->strings_option_name); ?>[<?php echo esc_attr($index); ?>][value]" 
                                               value="<?php echo esc_attr($string['value']); ?>" required>
                                    </td>
                                    <td>
                                        <code>[SLS-<?php echo esc_html($string['identifier']); ?>]</code>
                                    </td>
                                    <td>
                                        <button type="button" class="button remove-string"><?php esc_html_e('Remove', 'simple-language-switcher'); ?></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p>
                            <button type="button" class="button" id="add-string"><?php esc_html_e('Add String', 'simple-language-switcher'); ?></button>
                        </p>
                        <?php submit_button(); ?>
                    </form>
                </div>
                <script>
                jQuery(document).ready(function($) {
                    $('#add-string').on('click', function() {
                        const index = $('#translatable-strings-table tbody tr').length;
                        const row = `
                            <tr>
                                <td><input type="text" name="<?php echo esc_attr($this->strings_option_name); ?>[${index}][identifier]" required></td>
                                <td><input type="text" name="<?php echo esc_attr($this->strings_option_name); ?>[${index}][value]" required></td>
                                <td><code>[SLS-]</code></td>
                                <td><button type="button" class="button remove-string"><?php esc_html_e('Remove', 'simple-language-switcher'); ?></button></td>
                            </tr>
                        `;
                        $('#translatable-strings-table tbody').append(row);
                    });

                    $(document).on('click', '.remove-string', function() {
                        $(this).closest('tr').remove();
                    });
                });
                </script>
            <?php else: ?>
                <form action="options.php" method="post">
                    <?php
                    settings_fields('sls_options');
                    do_settings_sections($this->page_slug);
                    submit_button();
                    ?>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }
}

// Initialize the settings
SimpleLanguageSwitcherSettings::get_instance();