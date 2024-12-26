# Simple Language Switcher

## Description

Simple Language Switcher is a lightweight WordPress plugin that provides a clean, modal-style language selection popup. It is designed to work seamlessly with the Polylang plugin, allowing users to switch between different languages on your website with ease and it adds some advanced features so using Polylang would be easier.
![Screenshot](https://github.com/user-attachments/assets/7db8b8cf-abab-4ed3-94e7-dbd990baab80)

## Features

- Lightweight and fast
- Modern popup interface (using shortcodes for now)
- Seamless Polylang integration
- Translatable strings across languages through Polylang using Gutenberg block or shortcodes
- Translatable popup title and author display name
- Customizable display options
- RTL language support

## Installation

1. Download the latest release from the [GitHub Releases page](https://github.com/qasedak/simple-language-switcher/releases) or [Notwp.org Repo](https://notwp.org/plugins/qasedak/simple-language-switcher/)
2. Upload the ZIP file through WordPress plugin installer, or extract to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Ensure Polylang is installed and activated

## Usage

1. **Add the Shortcode**: Use the `[simple-language-switcher]` shortcode in your posts, pages, or widgets where you want the language switcher to appear.
2. **Add Translatable Strings**: 
    - Use the Gutenberg block "Translatable String" to add translatable strings. (Recommended)
    - Use the shortcode format [SLS-{identifier}] to display the translated string. (For old themes)

3. **Configure Settings**: 
   - Go to Settings > Language Switcher > Display settings to customize:
     - Show/hide country flags
     - Show/hide language names
     - Hide current language from the list
     - Hide languages without translations
     - Disable/Enable shortcodes
   - Go to Languages > Translations to translate the popup title, author display name and translatable strings.
   - Go to Settings > Translatable Strings to add translatable strings.

4. **Customize Appearance**: You can customize the appearance of the language switcher through:
   - The plugin settings page for display options
   - Your theme's CSS for advanced styling (UI Customizer will be available soon)

5. **RTL Support**: The plugin automatically supports right-to-left (RTL) languages and adjusts its layout accordingly.

Note: Make sure Polylang is properly configured with your desired languages before using this plugin.

## Requirements

- WordPress 5.0 or higher
- Polylang plugin

## License

This plugin is licensed under the GPLv3.

## Support

For support, please contact me [m_anbarestany@hotmail.com](mailto:m_anbarestany@hotmail.com).
