<?php

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Settings;

class CEPF_Turnstile_Handler extends CEPF_Base_Captcha_Handler
{
    const OPTION_NAME_SITE_KEY = 'elementor_pro_cf_turnstile_site_key';
    const OPTION_NAME_SECRET_KEY = 'elementor_pro_cf_turnstile_secret_key';

    protected static function get_captcha_name()
    {
        return 'cf_turnstile';
    }

    protected static function get_option_name_site_key()
    {
        return self::OPTION_NAME_SITE_KEY;
    }

    protected static function get_option_name_secret_key()
    {
        return self::OPTION_NAME_SECRET_KEY;
    }

    protected static function get_captcha_api_url()
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/api.js';
    }

    protected static function get_verify_api_url()
    {
        return 'https://challenges.cloudflare.com/turnstile/v0/siteverify';
    }

    protected static function get_response_field_name()
    {
        return 'cf-turnstile-response';
    }

    protected static function get_handler_js_file()
    {
        return 'turnstile-handler.js';
    }

    public static function get_setup_message()
    {
        return esc_html__('To use Cloudflare Turnstile, you need to add the API Key and complete the setup process in Dashboard > Elementor > Settings > Integrations > Cloudflare Turnstile.', 'captcha-elementor-pro');
    }

    public function register_admin_fields(Settings $settings)
    {
        $settings->add_section(Settings::TAB_INTEGRATIONS, static::get_captcha_name(), [
            'label' => esc_html__('Cloudflare Turnstile', 'captcha-elementor-pro'),
            'callback' => function () {
                echo sprintf(
                    /* translators: 1: Link opening tag, 2: Link closing tag. */
                    esc_html__('%1$sCloudflare Turnstile%2$s is Cloudflare\'s CAPTCHA alternative solution where your users don\'t ever have to solve another puzzle to get to your website, no more stop lights and fire hydrants.', 'captcha-elementor-pro'),
                    '<a href="https://www.cloudflare.com/application-services/products/turnstile/" target="_blank">',
                    '</a>'
                );
            },
            'fields' => [
                'pro_cf_turnstile_site_key' => [
                    'label' => esc_html__('Site Key', 'captcha-elementor-pro'),
                    'field_args' => [
                        'type' => 'text',
                    ],
                ],
                'pro_cf_turnstile_secret_key' => [
                    'label' => esc_html__('Secret Key', 'captcha-elementor-pro'),
                    'field_args' => [
                        'type' => 'text',
                    ],
                ],
            ],
        ]);
    }

    protected function get_error_messages()
    {
        return [
            'missing-input-secret' => esc_html__('The secret parameter is missing.', 'captcha-elementor-pro'),
            'invalid-input-secret' => esc_html__('The secret parameter is invalid or malformed.', 'captcha-elementor-pro'),
            'missing-input-response' => esc_html__('The response parameter is missing.', 'captcha-elementor-pro'),
            'invalid-input-response' => esc_html__('The response parameter is invalid or malformed.', 'captcha-elementor-pro'),
        ];
    }

    protected function get_validation_error_message()
    {
        return esc_html__('Invalid form, Cloudflare Turnstile validation failed.', 'captcha-elementor-pro');
    }

    public function render_field($item, $item_index, $widget)
    {
        $captcha_html = '<div class="elementor-field" id="form-field-' . esc_attr($item['custom_id']) . '">';

        $captcha_name = static::get_captcha_name();

        if (static::is_enabled()) {
            $this->enqueue_scripts();
            $this->add_render_attributes($item, $item_index, $widget);
            $captcha_html .= '<div ' . $widget->get_render_attribute_string($captcha_name . $item_index) . ' style="min-height:65px"></div>';
        } elseif (current_user_can('manage_options')) {
            $captcha_html .= '<div class="elementor-alert elementor-alert-info">';
            $captcha_html .= static::get_setup_message();
            $captcha_html .= '</div>';
        }

        $captcha_html .= '</div>';

        echo wp_kses_post($captcha_html);
    }

    protected function add_render_attributes($item, $item_index, $widget)
    {
        $captcha_name = static::get_captcha_name();

        $widget->add_render_attribute([
            $captcha_name . $item_index => [
                'class' => 'elementor-cf-turnstile',
                'data-sitekey' => static::get_site_key(),
                'data-type' => static::get_captcha_type(),
            ],
        ]);

        $this->add_version_specific_render_attributes($item, $item_index, $widget);
    }

    protected function add_version_specific_render_attributes($item, $item_index, $widget)
    {
        $captcha_name = static::get_captcha_name();
        $widget->add_render_attribute($captcha_name . $item_index, [
            'data-theme' => 'light',
            'data-size' => 'flexible',
        ]);
    }

    public function add_field_type($field_types)
    {
        $field_types['cf_turnstile'] = esc_html__('Cloudflare Turnstile', 'captcha-elementor-pro');

        return $field_types;
    }
}