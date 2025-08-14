<?php

/**
 * Plugin Name: Captcha for Elementor Pro Forms
 * Plugin URI: https://github.com/DavePodosyan/captcha-for-elementor-pro-forms
 * Description: Adds hCaptcha and Cloudflare Turnstile support to Elementor Pro forms with seamless integration.
 * Version: 1.0.8
 * Author: Dave Podosyan
 * Author URI: https://github.com/DavePodosyan
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: captcha-elementor-pro
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.8
 * Requires PHP: 7.4
 * Network: false
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!defined('CEPF_VERSION')) {
    define('CEPF_VERSION', '1.0.8');
}

if (!defined('CEPF_PLUGIN_FILE')) {
    define('CEPF_PLUGIN_FILE', __FILE__);
}

if (!defined('CEPF_PLUGIN_DIR')) {
    define('CEPF_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

if (!defined('CEPF_PLUGIN_URL')) {
    define('CEPF_PLUGIN_URL', plugin_dir_url(__FILE__));
}

class Captcha_Elementor_Pro_Forms
{
    private static $instance = null;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        add_action('plugins_loaded', [$this, 'init']);
        register_activation_hook(CEPF_PLUGIN_FILE, [$this, 'activate']);
        register_deactivation_hook(CEPF_PLUGIN_FILE, [$this, 'deactivate']);
    }

    public function init()
    {
        if (!$this->check_dependencies()) {
            return;
        }

        $this->load_textdomain();
        $this->include_files();
        $this->init_handlers();
    }

    private function check_dependencies()
    {
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor']);
            return false;
        }

        // Check for Elementor Pro

        if (!defined('ELEMENTOR_PRO_VERSION')) {
            add_action('admin_notices', [$this, 'admin_notice_missing_elementor_pro']);
            return false;
        }

        if (defined('ELEMENTOR_PRO_VERSION') && !version_compare(ELEMENTOR_PRO_VERSION, '2.0', '>=')) {
            add_action('admin_notices', [$this, 'admin_notice_minimum_elementor_pro_version']);
            return false;
        }

        return true;
    }

    private function has_elementor_pro_forms()
    {
        // Check for forms capability - works with both Elementor Pro and Pro Elements
        return class_exists('ElementorPro\\Modules\\Forms\\Module');
    }

    private function load_textdomain()
    {
        load_plugin_textdomain(
            'captcha-elementor-pro',
            false,
            dirname(plugin_basename(CEPF_PLUGIN_FILE)) . '/languages/'
        );
    }

    private function include_files()
    {
        require_once CEPF_PLUGIN_DIR . 'includes/class-base-captcha-handler.php';
        require_once CEPF_PLUGIN_DIR . 'includes/class-hcaptcha-handler.php';
        require_once CEPF_PLUGIN_DIR . 'includes/class-turnstile-handler.php';
        require_once CEPF_PLUGIN_DIR . 'includes/class-plugin-updater.php';
    }

    private function init_handlers()
    {
        add_action('elementor/init', function () {
            new CEPF_HCaptcha_Handler();
            new CEPF_Turnstile_Handler();
        });

        // Initialize plugin updater
        new CEPF_Plugin_Updater(CEPF_PLUGIN_FILE, 'DavePodosyan', 'captcha-for-elementor-pro-forms');
    }

    public function activate()
    {
        // Allow activation but show notices for missing dependencies
        // This is more user-friendly than preventing activation entirely
    }

    public function deactivate()
    {
        // Cleanup if needed
    }

    public function admin_notice_missing_elementor()
    {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'captcha-elementor-pro'),
            '<strong>' . esc_html__('Captcha for Elementor Pro Forms', 'captcha-elementor-pro') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'captcha-elementor-pro') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_missing_elementor_pro()
    {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" or "%3$s" to be installed and activated.', 'captcha-elementor-pro'),
            '<strong>' . esc_html__('Captcha for Elementor Pro Forms', 'captcha-elementor-pro') . '</strong>',
            '<strong>' . esc_html__('Elementor Pro', 'captcha-elementor-pro') . '</strong>',
            '<strong>' . esc_html__('Pro Elements', 'captcha-elementor-pro') . '</strong>'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }

    public function admin_notice_minimum_elementor_pro_version()
    {
        $message = sprintf(
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'captcha-elementor-pro'),
            '<strong>' . esc_html__('Captcha for Elementor Pro Forms', 'captcha-elementor-pro') . '</strong>',
            '<strong>' . esc_html__('Elementor Pro', 'captcha-elementor-pro') . '</strong>',
            '2.0'
        );

        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}

Captcha_Elementor_Pro_Forms::get_instance();
