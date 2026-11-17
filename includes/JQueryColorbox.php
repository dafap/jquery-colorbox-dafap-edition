<?php
declare(strict_types=1);

namespace Dafap\JQueryColorbox;

use Dafap\JQueryColorbox\Classes\Backend;
use Dafap\JQueryColorbox\Classes\Frontend;

class JQueryColorbox
{
    private static ?self $instance = null;
    public array $colorboxSettings = [];

    public static function get_instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        // Constructeur privé pour le singleton
    }

    public function run(): void
    {
        add_action('init', [$this, 'loadTextdomain'], 5);
        
        // Lien Réglages dans la liste des plugins
        add_filter('plugin_action_links_' . JQC_BASENAME, [$this, 'addPluginActionLinks']);

        $usersettings = (array) get_option(Config::SETTINGS_NAME);
        $defaultArray = Config::getDefaultSettings();
        $validSettings = isset($usersettings['jQueryColorboxVersion']);
        
        $this->colorboxSettings = $validSettings
            ? wp_parse_args($usersettings, $defaultArray)
            : $defaultArray;

        if (!$validSettings) {
            update_option(Config::SETTINGS_NAME, $defaultArray);
        }

        if (is_admin()) {
            new Backend($this->colorboxSettings);
        } else {
            new Frontend($this->colorboxSettings);
        }
    }

    public function loadTextdomain(): void
    {
        load_plugin_textdomain(
            JQC_TEXTDOMAIN, 
            false, 
            dirname(plugin_basename(JQC_BASENAME)) . '/languages'
        );
    }

    public function addPluginActionLinks(array $links): array
    {
        $settings_link = '<a href="' . admin_url('options-general.php?page=' . Config::SETTINGS_PAGE_SLUG) . '">' .
            __('Settings', JQC_TEXTDOMAIN) .
            '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}