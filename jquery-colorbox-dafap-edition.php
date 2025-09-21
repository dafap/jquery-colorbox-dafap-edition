<?php
/**
 * Plugin Name: jQuery Colorbox Dafap Edition
 * Plugin URI: https://github.com/dafap/jquery-colorbox-dafap-edition
 * Description: Refonte moderne du plugin Colorbox pour WordPress, avec compatibilité PHP 8.4 et architecture modulaire.
 * Version: 5.0
 * Author: Arne Franken modifié par Alain Pomirol (Dafap - 2025)
 * Author URI: https://github.com/dafap
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Domain Path: /languages
 */

declare(strict_types=1);

/**
 * jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @link      https://github.com/dafap/jquery-colorbox-dafap-edition
 * @version   5.0
 * @file      jquery-colorbox-dafap-edition.php
 * @purpose   Fichier principal du plugin, déclaration et initialisation
 */

// 🔹 Définition des constantes
define('JQUERYCOLORBOX_VERSION', '4.6.2');
define('COLORBOXLIBRARY_VERSION', '1.4.33');
define('JQUERYCOLORBOX_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('JQUERYCOLORBOX_PLUGIN_NAME', trim(dirname(JQUERYCOLORBOX_PLUGIN_BASENAME), '/'));
define('JQUERYCOLORBOX_TEXTDOMAIN', 'jquery-colorbox');
define('JQUERYCOLORBOX_NAME_RAW', 'jQuery Colorbox');
define('JQUERYCOLORBOX_PLUGIN_DIR', dirname(__FILE__));
define('JQUERYCOLORBOX_PLUGIN_URL', plugins_url('', __FILE__));
define('JQUERYCOLORBOX_SETTINGSNAME', 'jquery-colorbox_settings');
define('JQUERYLIBRARY_VERSION', '1.10.1');
define('JQUERYCOLORBOX_USERAGENT', 'jQuery Colorbox V' . JQUERYCOLORBOX_VERSION . '; (' . get_bloginfo('url') . ')');

// 🔹 Classe principale du plugin
/**
 * Classe principale du plugin jQuery Colorbox Dafap Edition
 *
 * Initialise les réglages, charge les composants backend/frontend,
 * et gère les traductions et constantes du plugin.
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @link      https://github.com/dafap/jquery-colorbox-dafap-edition
 * @version   5.0
 * @since     5.0
 */
class JQueryColorbox {

  /**
   * Réglages utilisateur du plugin
   * @var array<string, mixed>
   */
  public array $colorboxSettings = [];

  /**
   * Liste des thèmes disponibles
   * @var array<string, string>
   */
  public array $colorboxThemes = [];

  /**
   * Thèmes fictifs pour compatibilité ou extension
   * @var array<int, string>
   */
  public array $dummyThemeNumberArray = [];

  /**
   * Unités de mesure disponibles
   * @var array<string, string>
   */
  public array $colorboxUnits = [];

  /**
   * Transitions disponibles pour Colorbox
   * @var array<string, string>
   */
  public array $colorboxTransitions = [];

  /**
   * Nom localisé du plugin
   * @var string
   */
  private string $plugin_name = '';

  /**
   * Constructeur : initialise les hooks et les réglages
   */
  public function __construct() {
    if (!function_exists('plugins_url')) {
      return;
    }

    add_action('init', [ $this, 'jquery_colorbox_load_textdomain' ], 5);
    add_action('init', [ $this, 'localize_labels' ], 10);
    add_action('init', [ $this, 'init_backend' ], 15);

    $usersettings = (array) get_option(JQUERYCOLORBOX_SETTINGSNAME);
    $defaultArray = $this->jQueryColorboxDefaultSettings();
    $validSettings = $this->validateSettingsInDatabase($usersettings);

    $this->colorboxSettings = $validSettings
      ? wp_parse_args($usersettings, $defaultArray)
      : $defaultArray;

    if (!$validSettings) {
      update_option(JQUERYCOLORBOX_SETTINGSNAME, $defaultArray);
    }

    $this->colorboxThemes = [
      'theme1' => 'Theme #1',
      'theme2' => 'Theme #2',
      'theme3' => 'Theme #3',
      'theme4' => 'Theme #4',
      'theme5' => 'Theme #5',
      'theme6' => 'Theme #6',
      'theme7' => 'Theme #7',
      'theme8' => 'Theme #8',
      'theme9' => 'Theme #9',
      'theme10' => 'Theme #10',
      'theme11' => 'Theme #11'
    ];

    $this->dummyThemeNumberArray = [ 'Theme #12', 'Theme #13', 'Theme #14', 'Theme #15' ];
    $this->colorboxUnits = [ '%' => 'percent', 'px' => 'pixels' ];
    $this->colorboxTransitions = [ 'elastic' => 'elastic', 'fade' => 'fade', 'none' => 'none' ];

    if (function_exists('register_uninstall_hook')) {
      register_uninstall_hook(__FILE__, [ self::class, 'uninstallJqueryColorbox' ]);
    }
  }

  /**
   * Charge le fichier de traduction du plugin
   *
   * @return void
   */
  public function jquery_colorbox_load_textdomain(): void {
    load_plugin_textdomain(JQUERYCOLORBOX_TEXTDOMAIN, false, '/jquery-colorbox/languages/');
    if (!defined('JQUERYCOLORBOX_NAME')) {
      define('JQUERYCOLORBOX_NAME', __(JQUERYCOLORBOX_NAME_RAW, JQUERYCOLORBOX_TEXTDOMAIN));
    }
  }

  /**
   * Localise les libellés des thèmes, unités et transitions
   *
   * @return void
   */
  public function localize_labels(): void {
    $this->plugin_name = __(JQUERYCOLORBOX_NAME_RAW, JQUERYCOLORBOX_TEXTDOMAIN);

    foreach ($this->colorboxThemes as $key => $label) {
      $this->colorboxThemes[$key] = __($label, JQUERYCOLORBOX_TEXTDOMAIN);
    }

    $this->colorboxUnits = [
      '%' => __('percent', JQUERYCOLORBOX_TEXTDOMAIN),
      'px' => __('pixels', JQUERYCOLORBOX_TEXTDOMAIN)
    ];

    $this->colorboxTransitions = [
      'elastic' => __('elastic', JQUERYCOLORBOX_TEXTDOMAIN),
      'fade' => __('fade', JQUERYCOLORBOX_TEXTDOMAIN),
      'none' => __('none', JQUERYCOLORBOX_TEXTDOMAIN)
    ];
  }

  /**
   * Initialise le backend ou le frontend selon le contexte
   *
   * @return void
   */
  public function init_backend(): void {
    if (is_admin()) {
      require_once JQUERYCOLORBOX_PLUGIN_DIR . '/includes/jquery-colorbox-backend.php';
      new JQueryColorboxBackend($this);
    } else {
      require_once JQUERYCOLORBOX_PLUGIN_DIR . '/includes/jquery-colorbox-frontend.php';
      new JQueryColorboxFrontend($this->colorboxSettings, $this->getPluginName());
    }
  }

  /**
   * Retourne le nom localisé du plugin
   *
   * @return string
   */
  public function getPluginName(): string {
    return $this->plugin_name ?: JQUERYCOLORBOX_NAME_RAW;
  }

  /**
   * Vérifie si les réglages en base sont valides
   *
   * @param array<string, mixed> $colorboxSettings
   * @return bool
   */
  public function validateSettingsInDatabase(array $colorboxSettings): bool {
    return isset($colorboxSettings['jQueryColorboxVersion']);
  }

  /**
   * Retourne les réglages par défaut du plugin
   *
   * @return array<string, mixed>
   */
  public function jQueryColorboxDefaultSettings(): array {
    return [
      'jQueryColorboxVersion' => JQUERYCOLORBOX_VERSION,
      'colorboxTheme' => 'theme1',
      'maxWidth' => 'false',
      'maxWidthValue' => '',
      'maxWidthUnit' => '%',
      'maxHeight' => 'false',
      'maxHeightValue' => '',
      'maxHeightUnit' => '%',
      'height' => 'false',
      'heightValue' => '',
      'heightUnit' => '%',
      'width' => 'false',
      'widthValue' => '',
      'widthUnit' => '%',
      'linkHeight' => 'false',
      'linkHeightValue' => '',
      'linkHeightUnit' => '%',
      'linkWidth' => 'false',
      'linkWidthValue' => '',
      'linkWidthUnit' => '%',
      'initialWidth' => '300',
      'initialHeight' => '100',
      'autoColorbox' => false,
      'autoColorboxGalleries' => false,
      'slideshow' => false,
      'slideshowAuto' => false,
      'scalePhotos' => false,
      'displayScrollbar' => false,
      'draggable' => false,
      'slideshowSpeed' => '2500',
      'opacity' => '0.85',
      'preloading' => false,
      'transition' => 'elastic',
      'speed' => '350',
      'overlayClose' => false,
      'disableLoop' => false,
      'disableKeys' => false,
      'autoHideFlash' => false,
      'colorboxWarningOff' => false,
      'colorboxMetaLinkOff' => false,
      'javascriptInFooter' => false,
      'debugMode' => false,
      'autoColorboxJavaScript' => false,
      'colorboxAddClassToLinks' => false,
      'addZoomOverlay' => false,
      'useGoogleJQuery' => false,
      'removeLinkFromMetaBox' => true
    ];
  }

  /**
   * Supprime les réglages du plugin lors de la désinstallation
   *
   * @return void
   */
  public static function uninstallJqueryColorbox(): void {
    delete_option(JQUERYCOLORBOX_SETTINGSNAME);
  }
}

// 🔹 Initialisation du plugin
function initJQueryColorbox(): void {
  new JQueryColorbox();
}

add_action('init', 'initJQueryColorbox', 3);
