<?php

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Settings;
use ElementorPro\Core\Utils;
use ElementorPro\Plugin;

abstract class CEPF_Base_Captcha_Handler
{
    abstract protected static function get_captcha_name();
    abstract protected static function get_option_name_site_key();
    abstract protected static function get_option_name_secret_key();
    abstract protected static function get_captcha_api_url();
    abstract protected static function get_verify_api_url();
    abstract protected static function get_response_field_name();
    abstract protected static function get_handler_js_file();

    public static function get_site_key()
    {
        return get_option(static::get_option_name_site_key());
    }

    public static function get_secret_key()
    {
        return get_option(static::get_option_name_secret_key());
    }

    public static function get_captcha_type()
    {
        return 'managed';
    }

    public static function is_enabled()
    {
        return static::get_site_key() && static::get_secret_key();
    }

    abstract public static function get_setup_message();

    abstract public function register_admin_fields(Settings $settings);

    public function localize_settings($settings)
    {
        $settings = array_replace_recursive($settings, [
            'forms' => [
                static::get_captcha_name() => [
                    'enabled' => static::is_enabled(),
                    'type' => static::get_captcha_type(),
                    'site_key' => static::get_site_key(),
                    'setup_message' => static::get_setup_message(),
                ],
            ],
        ]);

        return $settings;
    }

    protected static function get_script_name()
    {
        return 'elementor-' . static::get_captcha_name() . '-api';
    }

    protected static function get_handler_script_name()
    {
        return 'elementor-' . static::get_captcha_name() . '-handler';
    }

    public function register_scripts()
    {
        wp_register_script(
            static::get_script_name(),
            static::get_captcha_api_url(),
            [],
            false,
            true
        );
        
        wp_register_script(
            static::get_handler_script_name(),
            CEPF_PLUGIN_URL . 'assets/js/' . static::get_handler_js_file(),
            ['elementor-frontend'],
            CEPF_VERSION,
            true
        );
    }

    public function enqueue_scripts()
    {
        if (Plugin::elementor()->preview->is_preview_mode()) {
            return;
        }

        wp_enqueue_script(static::get_script_name());
        wp_enqueue_script(static::get_handler_script_name());
    }

    public function enqueue_admin_scripts()
    {
        static $admin_script_enqueued = false;
        
        if ($admin_script_enqueued) {
            return;
        }
        
        wp_enqueue_script(
            'cepf-admin-editor',
            CEPF_PLUGIN_URL . 'assets/js/admin-editor.js',
            ['jquery', 'elementor-editor'],
            CEPF_VERSION,
            true
        );
        
        $admin_script_enqueued = true;
    }

    public function validation($record, $ajax_handler)
    {
        $fields = $record->get_field([
            'type' => static::get_captcha_name(),
        ]);

        if (empty($fields)) {
            return;
        }

        $field = current($fields);

        $captcha_response = Utils::_unstable_get_super_global_value($_POST, static::get_response_field_name());

        if (empty($captcha_response)) {
            $ajax_handler->add_error($field['id'], esc_html__('The Captcha field cannot be blank. Please enter a value.', 'captcha-elementor-pro'));
            return;
        }

        $captcha_errors = $this->get_error_messages();
        $captcha_secret = static::get_secret_key();
        $client_ip = Utils::get_client_ip();

        $request = [
            'body' => [
                'secret' => $captcha_secret,
                'response' => $captcha_response,
                'remoteip' => $client_ip,
            ],
            'timeout' => 30,
            'headers' => [
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
        ];

        $response = wp_remote_post(static::get_verify_api_url(), $request);

        if (is_wp_error($response)) {
            $ajax_handler->add_error($field['id'], sprintf(
                esc_html__('Connection error: %s', 'captcha-elementor-pro'),
                $response->get_error_message()
            ));
            return;
        }

        $response_code = wp_remote_retrieve_response_code($response);

        if (200 !== (int) $response_code) {
            $ajax_handler->add_error($field['id'], sprintf(
                esc_html__('Can not connect to the captcha server (%d).', 'captcha-elementor-pro'),
                $response_code
            ));
            return;
        }

        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        if (!$this->validate_result($result, $field)) {
            $message = $this->get_validation_error_message();

            if (isset($result['error-codes']) && is_array($result['error-codes'])) {
                $result_errors = array_flip($result['error-codes']);

                foreach ($captcha_errors as $error_key => $error_desc) {
                    if (isset($result_errors[$error_key])) {
                        $message = $error_desc;
                        break;
                    }
                }
            }

            $this->add_error($ajax_handler, $field, $message);
            return;
        }

        $record->remove_field($field['id']);
    }

    abstract protected function get_error_messages();
    abstract protected function get_validation_error_message();

    protected function add_error($ajax_handler, $field, $message)
    {
        $ajax_handler->add_error($field['id'], $message);
    }

    protected function validate_result($result, $field)
    {
        if (!isset($result['success']) || !$result['success']) {
            return false;
        }

        return true;
    }

    abstract public function render_field($item, $item_index, $widget);

    abstract protected function add_render_attributes($item, $item_index, $widget);

    abstract public function add_field_type($field_types);

    public function filter_field_item($item)
    {
        if (static::get_captcha_name() === $item['field_type']) {
            $item['field_label'] = false;
            $item['required'] = false;
        }

        return $item;
    }

    public function __construct()
    {
        $this->register_scripts();

        add_filter('elementor_pro/forms/field_types', [$this, 'add_field_type']);
        add_action('elementor_pro/forms/render_field/' . static::get_captcha_name(), [$this, 'render_field'], 10, 3);
        add_filter('elementor_pro/forms/render/item', [$this, 'filter_field_item']);
        add_filter('elementor_pro/editor/localize_settings', [$this, 'localize_settings']);

        if (static::is_enabled()) {
            add_action('elementor_pro/forms/validation', [$this, 'validation'], 10, 2);
            add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
        }

        if (is_admin()) {
            add_action('elementor/admin/after_create_settings/' . Settings::PAGE_ID, [$this, 'register_admin_fields']);
            add_action('elementor/editor/before_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
        }
    }
}