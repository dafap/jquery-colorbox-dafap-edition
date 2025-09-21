<?php
/**
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * Plugin Name: jQuery Colorbox
 * Plugin URI: http://www.techotronic.de/plugins/jquery-colorbox/
 * Description: Used to overlay images on the current page. Images in one post are grouped automatically.
 * Version: 4.6.2
 * Author: Arne Franken
 * Author URI: http://www.techotronic.de/
 * License: GPL
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */
?>
<?php
//define constants
define('JQUERYCOLORBOX_VERSION', '4.6.2');
define('COLORBOXLIBRARY_VERSION', '1.4.33');

if (!defined('JQUERYCOLORBOX_PLUGIN_BASENAME')) {
  //jquery-colorbox/jquery-colorbox.php
  define('JQUERYCOLORBOX_PLUGIN_BASENAME', plugin_basename(__FILE__));
}
if (!defined('JQUERYCOLORBOX_PLUGIN_NAME')) {
  //jquery-colorbox
  define('JQUERYCOLORBOX_PLUGIN_NAME', trim(dirname(JQUERYCOLORBOX_PLUGIN_BASENAME), '/'));
}
if (!defined('JQUERYCOLORBOX_TEXTDOMAIN')) {
  define('JQUERYCOLORBOX_TEXTDOMAIN', 'jquery-colorbox');
}
if (!defined('JQUERYCOLORBOX_NAME_RAW')) {
  define('JQUERYCOLORBOX_NAME_RAW', 'jQuery Colorbox');
}
if (!defined('JQUERYCOLORBOX_PLUGIN_DIR')) {
  // /path/to/wordpress/wp-content/plugins/jquery-colorbox
  define('JQUERYCOLORBOX_PLUGIN_DIR', dirname(__FILE__));
}
if (!defined('JQUERYCOLORBOX_PLUGIN_URL')) {
  // http(s)://www.domain.com/wordpress/wp-content/plugins/jquery-colorbox
  define('JQUERYCOLORBOX_PLUGIN_URL', plugins_url('', __FILE__));
}
if (!defined('JQUERYCOLORBOX_SETTINGSNAME')) {
  define('JQUERYCOLORBOX_SETTINGSNAME', 'jquery-colorbox_settings');
}
if (!defined('JQUERYLIBRARY_VERSION')) {
  define('JQUERYLIBRARY_VERSION', '1.10.1');
}
if (!defined('JQUERYCOLORBOX_USERAGENT')) {
  define('JQUERYCOLORBOX_USERAGENT', 'jQuery Colorbox V' . JQUERYCOLORBOX_VERSION . '; (' . get_bloginfo('url') . ')');
}

/**
 * Main plugin class
 *
 * @since 1.0
 * @author Arne Franken
 */
class JQueryColorbox {
	
	public $colorboxSettings = [];
	public $colorboxThemes = [];
	public $dummyThemeNumberArray = [];
	public $colorboxUnits = [];
	public $colorboxTransitions = [];
	private $plugin_name;

  /**
   * Constructor
   * Plugin initialization
   *
   * @since 1.0
   * @access public
   * @access static
   * @author Arne Franken
   */
  function __construct() {
    if (!function_exists('plugins_url')) {
      return;
    }

    // Chargement du domaine de traduction à priorité haute
	add_action('init', [ $this, 'jquery_colorbox_load_textdomain' ], 5); // AP-ajout le 19/09/2025
	
	// Initialisation des chaînes traduites à priorité normale
    add_action('init', [ $this, 'localize_labels' ], 10);

    // Instanciation du backend à priorité tardive
    add_action('init', [ $this, 'init_backend' ], 15);

    // Chargement des réglages
    $usersettings = (array)get_option(JQUERYCOLORBOX_SETTINGSNAME);
    $defaultArray = $this->jQueryColorboxDefaultSettings();
    $validSettings = $this->validateSettingsInDatabase($usersettings);

    $this->colorboxSettings = $validSettings
            ? wp_parse_args($usersettings, $defaultArray)
            : $defaultArray;
			
    if(!$validSettings) {
      update_option(JQUERYCOLORBOX_SETTINGSNAME, $defaultArray);
    }

	// Initialisation brute des chaînes (non traduites)
	$this->colorboxThemes = array(
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
    );

    $this->dummyThemeNumberArray = array(
      'Theme #12',
      'Theme #13',
      'Theme #14',
      'Theme #15'
    );

    $this->colorboxUnits = array(
      '%' => 'percent',
      'px' => 'pixels'
    );

    $this->colorboxTransitions = array(
      'elastic' => 'elastic',
      'fade' => 'fade',
      'none' => 'none'
    );
    	
    /*if (is_admin()) {
      require_once 'includes/jquery-colorbox-backend.php';
      new JQueryColorboxBackend($this); //->colorboxSettings, $this->colorboxThemes, $this->colorboxUnits, $this->colorboxTransitions, $this->jQueryColorboxDefaultSettings());
    }
    else {
      require_once 'includes/jquery-colorbox-frontend.php';
      new JQueryColorboxFrontend($this->colorboxSettings);
    }*/

    // Enregistrement de la désinstallation
    if (function_exists('register_uninstall_hook')) {
	  register_uninstall_hook(__FILE__, [ 'JQueryColorbox', 'uninstallJqueryColorbox' ]);
    }
  }
  // JQueryColorbox()
  
  /**
   * AP - Ajout le 19/09/2025
   */
   public function jquery_colorbox_load_textdomain() {
	   load_plugin_textdomain(JQUERYCOLORBOX_TEXTDOMAIN, false, '/jquery-colorbox/localization/');
	   //load_plugin_textdomain(JQUERYCOLORBOX_TEXTDOMAIN, false, dirname(plugin_basename(__FILE__)) . '/localization');
	   
       // Définition différée de la constante traduite
       if (!defined('JQUERYCOLORBOX_NAME')) {
           define('JQUERYCOLORBOX_NAME', __(JQUERYCOLORBOX_NAME_RAW, JQUERYCOLORBOX_TEXTDOMAIN));
       }
	}
	
	public function localize_labels() {
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
	
    public function init_backend() {
        if (is_admin()) {
            require_once 'includes/jquery-colorbox-backend.php';
            new JQueryColorboxBackend($this);
        } else {
            require_once 'includes/jquery-colorbox-frontend.php';
			new JQueryColorboxFrontend($this->colorboxSettings, $this->getPluginName());
        }
    }
	
	public function getPluginName() {
        return isset($this->plugin_name) ? $this->plugin_name : JQUERYCOLORBOX_NAME_RAW;
    }

    /* FIN DE L'AJOUT */

  /**
   * Checks wheter the settings stored in the database are compatible with current version.
   *
   * @since 2.0
   * @access public
   * @author Arne Franken
   * @param $colorboxSettings array current colorboxSettings.
   *
   * @return bool true if settings work with this plugin version
   */
  //public function validateSettingsInDatabase() {
  function validateSettingsInDatabase($colorboxSettings) {
    if ($colorboxSettings) {
      //if jQueryColorboxVersion does not exist, the plugin is a version prior to 2.0
      //settings are incompatible with 2.0, restore default settings.
      if (!array_key_exists('jQueryColorboxVersion', $colorboxSettings)) {
        //in case future versions require resetting the settings
        //if($jquery_colorbox_settings['jQueryColorboxVersion'] < JQUERYCOLORBOX_VERSION)
        return false;
      }
    }
    return true;
  }

  // validateSettingsInDatabase()

  //=====================================================================================================

  /**
   * This is what an example jQuery Colorbox configuration looks like in the wp_options-table of the database:
   *
   * Database-entry name: "jquery-colorbox_settings"
   *
   * a:29:{
   * s:12:"autoColorbox";s:4:"true";
   * s:22:"autoColorboxJavaScript";s:4:"true";
   * s:13:"autoHideFlash";s:4:"true";
   * s:18:"colorboxWarningOff";s:4:"true";
   * s:13:"colorboxTheme";s:7:"theme11";
   * s:14:"slideshowSpeed";s:4:"2500";
   * s:8:"maxWidth";s:5:"false";s
   * :13:"maxWidthValue";s:0:"";
   * s:12:"maxWidthUnit";s:1:"%";
   * s:9:"maxHeight";s:5:"false";
   * s:14:"maxHeightValue";s:0:"";
   * s:13:"maxHeightUnit";s:1:"%";
   * s:5:"width";s:5:"false";
   * s:10:"widthValue";s:0:"";
   * s:9:"widthUnit";s:1:"%";
   * s:6:"height";s:5:"false";
   * s:11:"heightValue";s:0:"";
   * s:10:"heightUnit";s:1:"%";
   * s:9:"linkWidth";s:6:"custom";
   * s:14:"linkWidthValue";s:2:"80";
   * s:13:"linkWidthUnit";s:1:"%";
   * s:10:"linkHeight";s:6:"custom";
   * s:15:"linkHeightValue";s:2:"80";
   * s:14:"linkHeightUnit";s:1:"%";
   * s:12:"overlayClose";s:4:"true";
   * s:10:"transition";s:7:"elastic";
   * s:5:"speed";s:3:"350";
   * s:7:"opacity";s:4:"0.85";
   * s:21:"jQueryColorboxVersion";s:5:"4.1";
   * }
   */

  /**
   * Default array of plugin settings
   *
   * @since 2.0
   * @access private
   * @author Arne Franken
   *
   * @return array of default settings
   */
  //private function jQueryColorboxDefaultSettings() {
  function jQueryColorboxDefaultSettings() {

    // Create and return array of default settings
    return array(
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
    );
  }

  // jQueryColorboxDefaultSettings()

  /**
   * Delete plugin settings
   *
   * handles deletion from WordPress database
   *
   * @since 4.1
   * @access private
   * @author Arne Franken
   */
  //private function uninstallJqueryColorbox() {
  function uninstallJqueryColorbox() {
    delete_option(JQUERYCOLORBOX_SETTINGSNAME);
  }

  /**
   * currently unused.
   * it was requested a few times that people want to add their own version of a Colorbox skin and the plugin
   * should dynamically load theme directories.
   */
//    function getThemeDirs() {
//        $themesDirPath = JQUERYCOLORBOX_PLUGIN_DIR.'/themes/';
//        if ($themesDir = opendir($themesDirPath)) {
//            while (false !== ($dir = readdir($themesDir))) {
//                if (substr("$dir", 0, 1) != "."){
//                    $themeDirs[$dir] = $dir;
//                }
//            }
//            closedir($themesDir);
//        }
//        asort($themeDirs);
//        return $themeDirs;
//    }

}

// class JQueryColorbox()
?><?php
/**
 * Workaround for PHP4
 * initialize plugin, call constructor
 *
 * @since 1.0
 * @access public
 * @author Arne Franken
 */
function initJQueryColorbox() {
  new JQueryColorbox();
}

// initJQueryColorbox()

// add jQueryColorbox to WordPress initialization
add_action('init', 'initJQueryColorbox', 3); // priorité avant les hooks internes

//static call to constructor is only possible if constructor is 'public static', therefore not PHP4 compatible:
//add_action('init', array('JQueryColorbox','JQueryColorbox'), 7);
?>