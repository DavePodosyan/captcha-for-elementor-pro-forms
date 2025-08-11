=== Captcha for Elementor Pro Forms ===
Contributors: davepodosyan
Tags: elementor, captcha, hcaptcha, turnstile, cloudflare, security, forms, spam protection
Requires at least: 5.0
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds hCaptcha and Cloudflare Turnstile support to Elementor Pro forms with seamless integration.

== Description ==

Captcha for Elementor Pro Forms extends your Elementor Pro forms with advanced CAPTCHA solutions including hCaptcha and Cloudflare Turnstile. Protect your website from bots and spam while ensuring a smooth user experience.

**Features:**

* **hCaptcha Integration**: Privacy-focused CAPTCHA solution that protects user privacy
* **Cloudflare Turnstile**: Invisible CAPTCHA alternative with no user interaction required
* **Seamless Integration**: Works exactly like Elementor's built-in reCAPTCHA field
* **Easy Setup**: Simple configuration through Elementor settings
* **Modern Architecture**: Clean, object-oriented code with proper WordPress standards
* **Performance Optimized**: External JavaScript files for better caching and performance

**Supported CAPTCHA Providers:**

1. **hCaptcha** - Privacy-focused CAPTCHA solution
2. **Cloudflare Turnstile** - Invisible CAPTCHA with no user interaction

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/captcha-for-elementor-pro-forms` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. **Important**: This plugin requires Elementor Pro or Pro Elements as it needs the Form widget functionality.
4. Go to **Elementor > Settings > Integrations** to configure your CAPTCHA providers.
5. Enter your API keys for the CAPTCHA services you want to use.
6. Add CAPTCHA fields to your Elementor forms just like you would add reCAPTCHA fields.

== Configuration ==

**hCaptcha Setup:**
1. Create an account at [hCaptcha.com](https://www.hcaptcha.com/)
2. Create a new site and get your Site Key and Secret Key
3. Go to **WordPress Dashboard > Elementor > Settings > Integrations > hCaptcha**
4. Enter your Site Key and Secret Key
5. Add an "hCaptcha" field to your Elementor forms

**Cloudflare Turnstile Setup:**
1. Create a Cloudflare account and access the Turnstile section
2. Create a new site and get your Site Key and Secret Key  
3. Go to **WordPress Dashboard > Elementor > Settings > Integrations > Cloudflare Turnstile**
4. Enter your Site Key and Secret Key
5. Add a "Cloudflare Turnstile" field to your Elementor forms

== Frequently Asked Questions ==

= Does this plugin require Elementor Pro? =

Yes, this plugin requires Elementor Pro to be installed and activated as it extends the Elementor Pro forms functionality.

= Can I use both hCaptcha and Cloudflare Turnstile at the same time? =

Yes, you can configure both CAPTCHA providers and use them in different forms as needed.

= Is this plugin compatible with popup and modal forms? =

Yes, the plugin uses explicit rendering mode for better compatibility with popups and modal forms.

= What happens if I deactivate the plugin? =

The CAPTCHA fields will not render, but your forms will continue to work without CAPTCHA protection.

= Does this plugin store any data? =

The plugin only stores your CAPTCHA API keys in the WordPress options table. No user data is stored.

== Screenshots ==

1. hCaptcha field in Elementor form builder
2. Cloudflare Turnstile field in Elementor form builder  
3. hCaptcha settings in Elementor integrations
4. Cloudflare Turnstile settings in Elementor integrations
5. Live form with hCaptcha widget
6. Live form with Cloudflare Turnstile widget

== Changelog ==

= 1.0.4 =
* Added automatic plugin update system from GitHub releases
* Improved Pro Elements compatibility and dependency checking
* Enhanced layout stability with CAPTCHA field styling
* Updated documentation and GitHub README with better presentation

= 1.0.3 =
* Added GitHub-based plugin updater for seamless WordPress dashboard updates
* Fixed dependency checking for Pro Elements compatibility
* Improved user experience with flexible installation requirements

= 1.0.2 =
* Enhanced Pro Elements support and compatibility
* Improved admin notices and dependency checking
* Better user guidance for plugin requirements

= 1.0.1 =
* Added Pro Elements compatibility alongside Elementor Pro
* Improved dependency checking and user notifications
* Enhanced plugin activation flow

= 1.0.0 =
* Initial release
* hCaptcha integration with Elementor Pro forms
* Cloudflare Turnstile integration with Elementor Pro forms
* Seamless integration following Elementor's patterns
* External JavaScript files for better performance
* Proper error handling and validation
* WordPress coding standards compliance
* Uninstall cleanup functionality

== Upgrade Notice ==

= 1.0.0 =
Initial release of Captcha for Elementor Pro Forms.

== Development ==

This plugin is developed with modern WordPress standards:

* Object-oriented architecture
* Abstract base classes for code reuse
* Proper sanitization and validation
* WordPress Coding Standards compliance
* Comprehensive error handling
* Performance optimization with external assets

For support and contributions, visit the [GitHub repository](https://github.com/DavePodosyan/captcha-for-elementor-pro-forms).

== Privacy ==

This plugin integrates with third-party CAPTCHA services:

* **hCaptcha**: Please review [hCaptcha's Privacy Policy](https://www.hcaptcha.com/privacy)
* **Cloudflare Turnstile**: Please review [Cloudflare's Privacy Policy](https://www.cloudflare.com/privacypolicy/)

The plugin itself does not collect or store any personal user data beyond the CAPTCHA API keys in your WordPress database.