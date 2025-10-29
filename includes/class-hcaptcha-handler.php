<?php

if (!defined('ABSPATH')) {
    exit;
}

use Elementor\Settings;
use ElementorPro\Plugin;
Use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class CEPF_HCaptcha_Handler extends CEPF_Base_Captcha_Handler
{
    const OPTION_NAME_SITE_KEY = 'elementor_pro_hcaptcha_site_key';
    const OPTION_NAME_SECRET_KEY = 'elementor_pro_hcaptcha_secret_key';

    protected static function get_captcha_name()
    {
        return 'hcaptcha';
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
        return 'https://hcaptcha.com/1/api.js';
    }

    protected static function get_verify_api_url()
    {
        return 'https://hcaptcha.com/siteverify';
    }

    protected static function get_response_field_name()
    {
        return 'hcaptcha-response';
    }

    protected static function get_handler_js_file()
    {
        return 'hcaptcha-handler.js';
    }

    public static function get_setup_message()
    {
        return esc_html__('To use hCaptcha, you need to add the API Key and complete the setup process in Dashboard > Elementor > Settings > Integrations > hCaptcha.', 'captcha-elementor-pro');
    }

    public function register_admin_fields(Settings $settings)
    {
        $settings->add_section(Settings::TAB_INTEGRATIONS, static::get_captcha_name(), [
            'label' => esc_html__('hCaptcha', 'captcha-elementor-pro'),
            'callback' => function () {
                echo sprintf(
                    /* translators: 1: Link opening tag, 2: Link closing tag. */
                    esc_html__('%1$shCaptcha%2$s is a CAPTCHA solution that protects your website from bots while ensuring a seamless user experience.', 'captcha-elementor-pro'),
                    '<a href="https://www.hcaptcha.com/" target="_blank">',
                    '</a>'
                );
            },
            'fields' => [
                'pro_hcaptcha_site_key' => [
                    'label' => esc_html__('Site Key', 'captcha-elementor-pro'),
                    'field_args' => [
                        'type' => 'text',
                    ],
                ],
                'pro_hcaptcha_secret_key' => [
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
        return esc_html__('Invalid form, hCaptcha validation failed.', 'captcha-elementor-pro');
    }

    public function render_field($item, $item_index, $widget)
    {
        $captcha_html = '<div class="elementor-field" id="form-field-' . esc_attr($item['custom_id']) . '">';

        $captcha_name = static::get_captcha_name();

        if (static::is_enabled()) {
            $this->enqueue_scripts();
            $this->add_render_attributes($item, $item_index, $widget);
            $captcha_html .= '<div ' . $widget->get_render_attribute_string($captcha_name . $item_index) . '></div>';
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
                'class' => 'elementor-hcaptcha',
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
            'data-theme' => $item[$this->get_captcha_name() . '_style'] ?? 'light',
            'data-size' => 'normal'
        ]);
    }

    public function add_field_type($field_types)
    {
        $field_types['hcaptcha'] = esc_html__('hCaptcha', 'captcha-elementor-pro');

        return $field_types;
    }

    public function update_controls(Widget_Base $widget)
    {
        $elementor = Plugin::elementor();

        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');

        if (is_wp_error($control_data)) {
            return;
        }

        foreach ($control_data['fields'] as $index => $field) {
            if ('required' === $field['name'] || 'width' === $field['name']) {
                $control_data['fields'][$index]['conditions']['terms'][] = [
                    'name' => 'field_type',
                    'operator' => '!in',
                    'value' => [
                        $this->get_captcha_name(),
                    ],
                ];
            }
        }

        $field_controls = [
            $this->get_captcha_name() . '_style' => [
                'name' => $this->get_captcha_name() . '_style',
                'label' => esc_html__('Style', 'captcha-elementor-pro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'light' => esc_html__('Light', 'captcha-elementor-pro'),
                    'dark' => esc_html__('Dark', 'captcha-elementor-pro'),
                ],
                'default' => 'light',
                'condition' => [
                    'field_type' => $this->get_captcha_name(),
                ],
                'tab' => 'content',
                'inner_tab' => 'form_fields_content_tab',
                'tabs_wrapper' => 'form_fields_tabs',
            ]
        ];

        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }
}
