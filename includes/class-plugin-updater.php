<?php

if (!defined('ABSPATH')) {
    exit;
}

class CEPF_Plugin_Updater
{
    private $plugin_slug;
    private $plugin_file;
    private $plugin_version;
    private $github_user;
    private $github_repo;
    private $transient_key;

    public function __construct($plugin_file, $github_user, $github_repo)
    {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->plugin_version = CEPF_VERSION;
        $this->github_user = $github_user;
        $this->github_repo = $github_repo;
        $this->transient_key = 'cepf_update_check_' . md5($this->plugin_slug);

        add_filter('pre_set_site_transient_update_plugins', [$this, 'check_for_update']);
        add_filter('plugins_api', [$this, 'plugin_info'], 20, 3);
        add_filter('upgrader_pre_download', [$this, 'download_package'], 10, 3);
        add_action('upgrader_process_complete', [$this, 'purge_cache'], 10, 2);
    }

    public function check_for_update($transient)
    {
        if (empty($transient->checked)) {
            return $transient;
        }

        $remote_version = $this->get_remote_version();

        if ($remote_version && version_compare($this->plugin_version, $remote_version['version'], '<')) {
            $transient->response[$this->plugin_slug] = (object) [
                'slug' => dirname($this->plugin_slug),
                'plugin' => $this->plugin_slug,
                'new_version' => $remote_version['version'],
                'url' => $this->get_github_repo_url(),
                'package' => $remote_version['download_url'],
                'tested' => '6.6',
                'compatibility' => [],
            ];
        }

        return $transient;
    }

    public function plugin_info($result, $action, $args)
    {
        if ($action !== 'plugin_information') {
            return $result;
        }

        if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return $result;
        }

        $remote_version = $this->get_remote_version();

        if (!$remote_version) {
            return $result;
        }

        $plugin_data = get_plugin_data($this->plugin_file);

        return (object) [
            'name' => $plugin_data['Name'],
            'slug' => dirname($this->plugin_slug),
            'plugin' => $this->plugin_slug,
            'version' => $remote_version['version'],
            'author' => $plugin_data['Author'],
            'author_profile' => $plugin_data['AuthorURI'],
            'homepage' => $this->get_github_repo_url(),
            'short_description' => $plugin_data['Description'],
            'sections' => [
                'description' => $this->get_plugin_description($remote_version),
                'installation' => $this->get_installation_instructions(),
                'changelog' => $this->get_changelog($remote_version),
            ],
            'download_link' => $remote_version['download_url'],
            'requires' => $plugin_data['RequiresWP'] ?: '5.0',
            'tested' => '6.6',
            'requires_php' => $plugin_data['RequiresPHP'] ?: '7.4',
            'last_updated' => $remote_version['published_at'],
            'added' => '',
            'banners' => [],
            'icons' => [],
        ];
    }

    public function download_package($reply, $package, $upgrader)
    {
        if (strpos($package, 'github.com/' . $this->github_user . '/' . $this->github_repo) !== false) {
            $temp_file = download_url($package);

            if (is_wp_error($temp_file)) {
                return $temp_file;
            }

            return $temp_file;
        }

        return $reply;
    }

    public function purge_cache($upgrader, $options)
    {
        if (
            isset($options['action']) && $options['action'] === 'update' &&
            isset($options['type']) && $options['type'] === 'plugin' &&
            isset($options['plugins']) && is_array($options['plugins']) &&
            in_array($this->plugin_slug, $options['plugins'])
        ) {
            delete_transient($this->transient_key);
        }
    }

    private function get_remote_version()
    {
        $cached = get_transient($this->transient_key);
        if ($cached !== false) {
            return $cached;
        }

        $api_url = "https://api.github.com/repos/{$this->github_user}/{$this->github_repo}/releases/latest";
        
        $response = wp_remote_get($api_url, [
            'timeout' => 10,
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WordPress/' . get_bloginfo('version') . '; ' . get_bloginfo('url'),
            ],
        ]);

        if (is_wp_error($response)) {
            set_transient($this->transient_key, false, HOUR_IN_SECONDS);
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!$data || !isset($data['tag_name'])) {
            set_transient($this->transient_key, false, HOUR_IN_SECONDS);
            return false;
        }

        $download_url = null;
        if (isset($data['assets']) && is_array($data['assets'])) {
            foreach ($data['assets'] as $asset) {
                if (strpos($asset['name'], '.zip') !== false) {
                    $download_url = $asset['browser_download_url'];
                    break;
                }
            }
        }

        if (!$download_url) {
            set_transient($this->transient_key, false, HOUR_IN_SECONDS);
            return false;
        }

        $version_data = [
            'version' => ltrim($data['tag_name'], 'v'),
            'download_url' => $download_url,
            'body' => $data['body'] ?? '',
            'published_at' => $data['published_at'] ?? '',
        ];

        set_transient($this->transient_key, $version_data, 12 * HOUR_IN_SECONDS);

        return $version_data;
    }

    private function get_github_repo_url()
    {
        return "https://github.com/{$this->github_user}/{$this->github_repo}";
    }

    private function get_plugin_description($remote_version)
    {
        $description = '<h2>Captcha for Elementor Pro Forms</h2>';
        $description .= '<p>Professional CAPTCHA integration for Elementor Pro forms with hCaptcha and Cloudflare Turnstile support.</p>';
        
        $description .= '<h3>Features</h3>';
        $description .= '<ul>';
        $description .= '<li><strong>hCaptcha Integration</strong> - Privacy-focused CAPTCHA solution</li>';
        $description .= '<li><strong>Cloudflare Turnstile</strong> - Invisible CAPTCHA with zero friction</li>';
        $description .= '<li><strong>Native Integration</strong> - Works exactly like Elementor\'s built-in reCAPTCHA</li>';
        $description .= '<li><strong>Pro Elements Compatible</strong> - Works with both Elementor Pro and Pro Elements</li>';
        $description .= '</ul>';

        $description .= '<h3>Requirements</h3>';
        $description .= '<ul>';
        $description .= '<li>WordPress 5.0 or higher</li>';
        $description .= '<li>PHP 7.4 or higher</li>';
        $description .= '<li>Elementor Pro or Pro Elements 2.0 or higher</li>';
        $description .= '</ul>';

        return $description;
    }

    private function get_installation_instructions()
    {
        return '<ol>
            <li>Download and install the plugin</li>
            <li>Go to <strong>Elementor > Settings > Integrations</strong></li>
            <li>Configure your CAPTCHA provider keys (hCaptcha or Cloudflare Turnstile)</li>
            <li>Add CAPTCHA fields to your Elementor forms</li>
        </ol>';
    }

    private function get_changelog($remote_version)
    {
        $changelog = '<h4>Version ' . esc_html($remote_version['version']) . '</h4>';
        
        if (!empty($remote_version['body'])) {
            $changelog .= wpautop(esc_html($remote_version['body']));
        } else {
            $changelog .= '<p>Latest updates and improvements. See <a href="' . $this->get_github_repo_url() . '/releases" target="_blank">GitHub releases</a> for full details.</p>';
        }

        return $changelog;
    }
}