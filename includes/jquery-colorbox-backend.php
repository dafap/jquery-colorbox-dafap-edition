<?php
/**
 * jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain (Dafap)
 * @license   GPL-2.0-or-later
 * @link      https://github.com/dafap/jquery-colorbox-dafap-edition
 *
 * @version   5.0
 * @since     5.0
 *
 * @file      jquery-colorbox-backend.php
 * @purpose   Handles all actions in the WordPress backend for the jQuery Colorbox plugin.
 */

class JQueryColorboxBackend {

    /**
     * Instance of the main plugin class.
     *
     * @var JQueryColorbox
     */
    private $main;

    /**
     * User-defined plugin settings.
     *
     * @var array
     */
    public $colorboxSettings;

    /**
     * Available themes for the plugin.
     *
     * @var array
     */
    public $colorboxThemes;

    /**
     * Available units for dimension settings.
     *
     * @var array
     */
    public $colorboxUnits;

    /**
     * Available transition effects.
     *
     * @var array
     */
    public $colorboxTransitions;

    /**
     * Default plugin settings.
     *
     * @var array
     */
    public $colorboxDefaultSettings;

    /**
     * Constructor: initializes backend logic and hooks.
     *
     * @param JQueryColorbox $main Instance of the main plugin class.
     */
    public function __construct(JQueryColorbox $main) {
        $this->main = $main;
        $this->colorboxSettings = $main->colorboxSettings;
        $this->colorboxThemes = $main->colorboxThemes;
        $this->colorboxUnits = $main->colorboxUnits;
        $this->colorboxTransitions = $main->colorboxTransitions;
        $this->colorboxDefaultSettings = $main->jQueryColorboxDefaultSettings();

        // Register admin actions
        add_action('admin_post_jQueryColorboxDeleteSettings', [ $this, 'jQueryColorboxDeleteSettings' ], 20);
        add_action('admin_post_jQueryColorboxUpdateSettings', [ $this, 'jQueryColorboxUpdateSettings' ], 20);
        add_action('admin_menu', [ $this, 'registerAdminMenu' ], 20);
        add_action('admin_notices', [ $this, 'registerAdminWarning' ], 20);

        // Register TinyMCE filters
        add_filter('mce_buttons_2', [ $this, 'addStyleSelectorBox' ], 100);
        add_filter('mce_css', [ $this, 'addColorboxLinkClass' ], 100);

        // Load donation JavaScript if on plugin settings page
        if (isset($_GET['page']) && $_GET['page'] === JQUERYCOLORBOX_PLUGIN_BASENAME) {
            require_once 'donationloader.php';
            $donationLoader = new JQueryColorboxDonationLoader();
            add_action('admin_print_scripts', [ $donationLoader, 'registerDonationJavaScript' ]);
        }
    }

    /**
     * Renders the plugin settings page.
     *
     * @return void
     */
    public function renderSettingsPage() {
        require_once 'settings-page.php';
    }

    /**
     * Registers the plugin settings page and admin notices.
     *
     * @return void
     */
    public function registerAdminMenu() {
        $return_message = '';

        if (function_exists('add_management_page') && current_user_can('manage_options')) {
            $request_uri = sanitize_text_field($_SERVER['REQUEST_URI']);
            if (strpos($request_uri, 'jquery-colorbox.php') && isset($_GET['jQueryColorboxUpdateSettings'])) {
                $return_message = sprintf(__('Successfully updated %1$s settings.', JQUERYCOLORBOX_TEXTDOMAIN), $this->main->getPluginName());
            } elseif (strpos($request_uri, 'jquery-colorbox.php') && isset($_GET['jQueryColorboxDeleteSettings'])) {
                $return_message = sprintf(__('%1$s settings were successfully deleted.', JQUERYCOLORBOX_TEXTDOMAIN), $this->main->getPluginName());
            }
        }

        $this->registerAdminNotice($return_message);
        $this->registerSettingsPage();
    }

    /**
     * Displays a warning notice if plugin is not fully activated.
     *
     * @return void
     */
    public function registerAdminWarning() {
        if ($this->main->colorboxSettings['colorboxWarningOff'] || $this->main->colorboxSettings['autoColorbox']) {
            return;
        }
        ?>
        <div class="updated" style="background-color:#f66;">
            <p>
                <a href="options-general.php?page=<?php echo esc_attr(JQUERYCOLORBOX_PLUGIN_BASENAME); ?>">
                    <?php echo esc_html($this->main->getPluginName()); ?>
                </a>
                <?php _e('needs attention: the plugin is not activated to work for all images.', JQUERYCOLORBOX_TEXTDOMAIN); ?>
            </p>
        </div>
        <?php
    }

    /**
     * Adds a link to the plugin settings page in the plugin list.
     *
     * @param array $action_links Existing action links.
     * @return array Modified action links.
     */
    public function addPluginActionLinks($action_links) {
        $settings_link = '<a href="options-general.php?page=' . esc_attr(JQUERYCOLORBOX_PLUGIN_BASENAME) . '">' .
            esc_html(__('Settings', JQUERYCOLORBOX_TEXTDOMAIN)) . '</a>';
        array_unshift($action_links, $settings_link);
        return $action_links;
    }

    /**
     * Handles plugin settings update via admin form.
     *
     * @return void
     */
    public function jQueryColorboxUpdateSettings() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Did not update settings, you do not have the necessary rights.', JQUERYCOLORBOX_TEXTDOMAIN));
        }

        check_admin_referer('jquery-colorbox-settings-form');

        if (isset($_POST[JQUERYCOLORBOX_SETTINGSNAME])) {
            $this->main->colorboxSettings = $_POST[JQUERYCOLORBOX_SETTINGSNAME];
            $this->main->colorboxSettings['jQueryColorboxVersion'] = JQUERYCOLORBOX_VERSION;
            $this->updateSettingsInDatabase();
        }

        $referrer = str_replace(['&jQueryColorboxUpdateSettings', '&jQueryColorboxDeleteSettings'], '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&jQueryColorboxUpdateSettings');
        exit;
    }

    /**
     * Handles plugin settings deletion via admin form.
     *
     * @return void
     */
    public function jQueryColorboxDeleteSettings() {
        if (current_user_can('manage_options') && isset($_POST['delete_settings-true'])) {
            check_admin_referer('jquery-delete_settings-form');
            $this->deleteSettingsFromDatabase();
        } else {
            wp_die(sprintf(__('Did not delete %1$s settings. Either you don’t have the necessary rights or you didn’t check the checkbox.', JQUERYCOLORBOX_TEXTDOMAIN), $this->main->getPluginName()));
        }

        $referrer = str_replace(['&jQueryColorboxUpdateSettings', '&jQueryColorboxDeleteSettings'], '', $_POST['_wp_http_referer']);
        wp_redirect($referrer . '&jQueryColorboxDeleteSettings');
        exit;
    }

    /**
     * Adds the plugin CSS to TinyMCE editor.
     *
     * @param string $initialCssFiles Existing CSS files.
     * @return string Modified CSS file list.
     */
    public function addColorboxLinkClass($initialCssFiles) {
        return $initialCssFiles . ',' . JQUERYCOLORBOX_PLUGIN_URL . '/css/jquery-colorbox.css';
    }

    /**
     * Adds the style selector to TinyMCE editor.
     *
     * @param array $array Existing TinyMCE buttons.
     * @return array Modified button array.
     */
    public function addStyleSelectorBox($array) {
        if (!in_array('styleselect', $array)) {
            $array[] = 'styleselect';
        }
        return $array;
    }

    /**
     * Registers an admin notice.
     *
     * @param string $notice Message to display.
     * @return void
     */
    public function registerAdminNotice($notice) {
        if ($notice !== '') {
            $message = '<div class="updated fade"><p>' . esc_html($notice) . '</p></div>';
            add_action('admin_notices', function() use ($message) {
                echo $message;
            });
        }
    }

    /**
     * Registers the plugin settings page.
     *
     * @return void
     */
    public function registerSettingsPage() {
        if (current_user_can('manage_options')) {
            add_filter('plugin_action_links_' . JQUERYCOLORBOX_PLUGIN_BASENAME, [ $this, 'addPluginActionLinks' ]);
            add_options_page(
                $this->main->getPluginName(),
                $this->main->getPluginName(),
                'manage_options',
                JQUERYCOLORBOX_PLUGIN_BASENAME,
                [ $this, 'renderSettingsPage' ]
            );
        }
    }

    /**
     * Updates plugin settings in the database.
     *
     * @return void
     */
    public function updateSettingsInDatabase() {
        update_option(JQUERYCOLORBOX_SETTINGSNAME, $this->main->colorboxSettings);
    }

        /**
     * Deletes plugin settings from the WordPress database.
     *
     * @return void
     */
    public function deleteSettingsFromDatabase() {
        delete_option(JQUERYCOLORBOX_SETTINGSNAME);
    }

    /**
     * Outputs the current return location (URL) of the admin page.
     *
     * @return void
     */
    public function getReturnLocation() {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $protocol .= 's';
        }

        $host = $_SERVER['SERVER_NAME'];
        $port = $_SERVER['SERVER_PORT'];
        $uri = $_SERVER['REQUEST_URI'];

        // Assemble full URL
        $url = $protocol . '://' . $host;
        if (($protocol === 'http' && $port !== '80') || ($protocol === 'https' && $port !== '443')) {
            $url .= ':' . $port;
        }
        $url .= $uri;

        echo esc_url($url);
    }
}
