<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

$options_to_delete = [
    'elementor_pro_hcaptcha_site_key',
    'elementor_pro_hcaptcha_secret_key',
    'elementor_pro_cf_turnstile_site_key',
    'elementor_pro_cf_turnstile_secret_key',
];

foreach ($options_to_delete as $option) {
    delete_option($option);
    delete_site_option($option);
}

delete_transient('captcha_elementor_pro_activation_check');