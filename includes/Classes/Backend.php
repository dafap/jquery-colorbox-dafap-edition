<?php
/**
 * Classe Backend du plugin jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @file      includes/Classes/Backend.php
 * @purpose   Gère toutes les actions, menus, enregistrements de réglages et scripts de l'administration WordPress.
 */

declare(strict_types=1);

namespace Dafap\JQueryColorbox\Classes;

use Dafap\JQueryColorbox\Traits\TemplateRenderTrait;
use Dafap\JQueryColorbox\Traits\UtilsTrait;
use Dafap\JQueryColorbox\Config;

class Backend
{
    use TemplateRenderTrait, UtilsTrait;

    private const NONCE_ACTION = 'jquery-colorbox_settings-options';
    private array $colorboxDefaultSettings = [];
    private array $colorboxSettings = [];

    /**
     * Initialise les hooks et les réglages backend.
     *
     * @param array $colorboxSettings Réglages utilisateur actuels du plugin.
     */
    public function __construct(array $colorboxSettings)
    {
        $this->colorboxSettings = $colorboxSettings;
        $this->colorboxDefaultSettings = Config::getDefaultSettings();

        add_action('admin_post_jQueryColorboxDeleteSettings', [$this, 'handleDelete']);
        add_action('admin_post_jQueryColorboxUpdateSettings', [$this, 'handleUpdate']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminScripts']);
        add_action('admin_menu', [$this, 'registerAdminMenu']);
        add_action('admin_notices', [$this, 'registerAdminWarning']);
    }

    /**
     * Enfile les scripts et styles nécessaires pour la page d'administration.
     *
     * @param string $hook Le nom de la page d'administration actuelle.
     */
    public function enqueueAdminScripts(string $hook): void
    {
        if (strpos($hook, Config::SETTINGS_PAGE_SLUG) === false) {
            return;
        }

        $path_js = $this->isTrue('debugMode') ? 'assets/js/jquery-colorbox-admin.js' : 'assets/js/jquery-colorbox-admin.min.js';

        wp_enqueue_script(
            'jquery-colorbox-admin',
            JQC_URL . $path_js,
            ['jquery'],
            '1.1.0',
            true
        );

        wp_localize_script(
            'jquery-colorbox-admin',
            'jqcAdminData',
            [
                'settingsName' => Config::SETTINGS_NAME,
                'pluginUrl'    => JQC_URL,
            ]
        );
    }

    /**
     * Enregistre la page de réglages dans le menu d'administration de WordPress.
     */
    public function registerAdminMenu(): void
    {
        add_options_page(
            __('jQuery Colorbox Settings', JQC_TEXTDOMAIN),
            __('jQuery Colorbox', JQC_TEXTDOMAIN),
            'manage_options',
            Config::SETTINGS_PAGE_SLUG,
            [$this, 'renderSettingsPage']
        );
    }

    /**
     * Affiche la vue du formulaire des réglages du plugin dans l'administration.
     */
    public function renderSettingsPage(): void
    {
        $plugin_url = plugin_dir_url(dirname(__DIR__, 2) . '/jquery-colorbox-dafap-edition.php');

        $this->templateRender('backend/settings-page.phtml', [
            'settingsName'    => Config::SETTINGS_NAME,
            'settings'        => $this->colorboxSettings,
            'themes'          => Config::getThemes(),
            'units'           => Config::getUnits(),
            'transitions'     => Config::getTransitions(),
            'defaults'        => $this->colorboxDefaultSettings,
            'pluginName'      => __('jQuery Colorbox', JQC_TEXTDOMAIN),
            'pluginUrl'       => $plugin_url,
            'textDomain'      => JQC_TEXTDOMAIN,
            'colorboxVersion' => Config::COLORBOX_VERSION,
            'nonceAction'     => self::NONCE_ACTION,
        ]);
    }

    /**
     * Traite la mise à jour des options soumises depuis le formulaire d'administration.
     */
    public function handleUpdate(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Access denied', JQC_TEXTDOMAIN));
        }
        check_admin_referer(self::NONCE_ACTION);

        $submitted_settings = $_POST[Config::SETTINGS_NAME] ?? [];
        $validated_settings = $this->sanitizeSettings($submitted_settings);

        update_option(Config::SETTINGS_NAME, $validated_settings);

        wp_redirect(admin_url('options-general.php?page=' . Config::SETTINGS_PAGE_SLUG . '&updated=true'));
        exit;
    }

    /**
     * Traite la suppression des options du plugin.
     */
    public function handleDelete(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die(__('Access denied', JQC_TEXTDOMAIN));
        }
        check_admin_referer(self::NONCE_ACTION);

        delete_option(Config::SETTINGS_NAME);

        wp_redirect(admin_url('options-general.php?page=' . Config::SETTINGS_PAGE_SLUG . '&deleted=true'));
        exit;
    }

    /**
     * Affiche un avertissement dans l'administration si la fonction autoColorbox est désactivée.
     */
    public function registerAdminWarning(): void
    {
        $settings = get_option(Config::SETTINGS_NAME, []);
        if (empty($settings['autoColorbox']) && empty($settings['colorboxWarningOff'])) {
            echo '<div class="notice notice-warning"><p>' .
                esc_html__('The plugin is enabled but the autoColorbox feature is disabled.', JQC_TEXTDOMAIN) .
                '</p></div>';
        }
    }

    /**
     * Nettoie et valide les données de réglages soumises par l'utilisateur.
     *
     * @param array $submittedData Données brutes du formulaire.
     * @return array Données validées et sécurisées.
     */
    private function sanitizeSettings(array $submittedData): array
    {
        $checkboxKeys = [
            'autoColorbox', 'autoColorboxGalleries', 'autoColorboxJavaScript',
            'autoHideFlash', 'addZoomOverlay', 'useGoogleJQuery',
            'javascriptInFooter', 'removeLinkFromMetaBox', 'colorboxWarningOff',
            'slideshow', 'slideshowAuto', 'scalePhotos', 'preloading', 'overlayClose'
        ];

        $numericKeys = [
            'slideshowSpeed', 'speed', 'maxWidthValue', 'maxHeightValue',
            'widthValue', 'heightValue', 'linkWidthValue', 'linkHeightValue'
        ];

        $defaultSettings = Config::getDefaultSettings();
        $newSettings = $defaultSettings;

        foreach ($defaultSettings as $key => $defaultValue) {
            if (in_array($key, $checkboxKeys, true)) {
                $value = isset($submittedData[$key]) ? 'true' : 'false';
            } else {
                $value = $submittedData[$key] ?? $defaultValue;
            }

            if (in_array($key, $numericKeys, true)) {
                if ($key === 'opacity') {
                    $value = max(0.0, min(1.0, floatval($value)));
                } else {
                    $value = absint($value);
                }
            } elseif (is_string($value)) {
                $value = sanitize_text_field($value);
            }

            $newSettings[$key] = $value;
        }

        return $newSettings;
    }
}