=== Simple Language Switcher ===
Contributors: Mohammad Anbarestany
Tags: language, switcher, polylang, multilingual, languages, translation
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.8
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

A clean, modal-style language switcher for WordPress sites using Polylang.

== Description ==

Simple Language Switcher adds a sleek, modal-style language selector to your WordPress website. It works seamlessly with Polylang to provide a modern way for users to switch between languages and adds some advanced features to Polylang.

= Features =
* Lightweight and fast
* Modern popup interface (using shortcodes for now)
* Seamless Polylang integration
* Translatable popup title and author display name
* Customizable display options
* RTL language support
* Translatable strings across languages through Polylang using Gutenberg block or shortcodes

= Requirements =
* WordPress 5.0 or higher
* Polylang plugin must be installed and activated
* PHP 7.4 or higher

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/simple-language-switcher` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Ensure Polylang is installed and activated
4. Use the Settings->Language Switcher screen to configure display options

== Screenshots ==

1. Language switcher popup on the frontend
2. Settings page in WordPress admin
3. Example of RTL language support

== Changelog ==

= 1.9.0 RC1 =
* Editing functionality for translatable strings

= 1.9.0 RC =
* Bug fix: Fixed the issue where saving translatable strings was not working
* Added Gutenberg block "Translatable String"
* Added Disable/Enable shortcodes option in settings

= 1.8 =
* Added translatable strings feature

= 1.7 =
* Added author display name translation feature

= 1.6 =
* Bug fix: Fixed the issue where the language switcher was not working on category pages
* Popup title is now translatable through Polylang's translations settings
* Improved code quality
* Language files (POT, PO, MO) are updated and corrected

= 1.5 =
* Added display options in settings
* Improved RTL support
* Added flag display option
* Security improvements
* Bug fix: extra white space added by wpautop() is now removed using CSS.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.9.0 RC1 =
This version adds editing functionality for translatable strings. Upgrade is optional.

== Privacy Policy ==

Simple Language Switcher does not collect or store any personal data. 