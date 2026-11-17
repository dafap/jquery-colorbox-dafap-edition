<?php
/**
 * Plugin Name: jQuery Colorbox Dafap Edition
 * Description: Refonte du plugin jQuery Colorbox pour WordPress, optimisé pour PHP 8.4+, avec une architecture moderne et une compatibilité renforcée.
 * Plugin URI: https://github.com/dafap/jquery-colorbox-dafap-edition
 * Update URI: https://github.com/dafap/jquery-colorbox-dafap-edition
 * Version: 1.1.0
 * Tested up to: 7.1
 * Author: Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * Author URI: https://github.com/dafap
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: jquery-colorbox
 * Domain Path: /languages
 * Requires PHP: 8.2
 */

// Si ce fichier est accédé directement, bloquer.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Récupération dynamique de la version et des métadonnées depuis l'en-tête
if ( ! function_exists( 'get_file_data' ) ) {
    require_once ABSPATH . 'wp-includes/functions.php';
}
$plugin_data    = get_file_data( __FILE__, [ 'version' => 'Version', 'requires_php' => 'Requires PHP', 'text_domain' => 'Text Domain' ] );
$plugin_version = ! empty( $plugin_data['version'] ) ? $plugin_data['version'] : '1.1.0';
$required_php   = ! empty( $plugin_data['requires_php'] ) ? $plugin_data['requires_php'] : '8.2';
$text_domain    = ! empty( $plugin_data['text_domain'] ) ? $plugin_data['text_domain'] : 'jquery-colorbox';

// Vérification de compatibilité PHP avant tout
if ( version_compare( PHP_VERSION, $required_php, '<' ) ) {
    deactivate_plugins( plugin_basename( __FILE__ ) );
    return;
}

// Définition des constantes de base
if ( ! defined( 'JQC_VERSION' ) ) {
    define( 'JQC_VERSION', $plugin_version );
}
if ( ! defined( 'JQC_PATH' ) ) {
    define( 'JQC_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'JQC_BASENAME' ) ) {
    define( 'JQC_BASENAME', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'JQC_TEXTDOMAIN' ) ) {
    define( 'JQC_TEXTDOMAIN', $text_domain );
}
if ( ! defined( 'JQC_URL' ) ) {
    define( 'JQC_URL', plugin_dir_url( __FILE__ ) );
}

// 1. Chargement de l'Autoloader de Composer (PSR-4 strict)
require_once JQC_PATH . 'vendor/autoload.php';

// 1. bis : Initialisation de Plugin Update Checker (v6)
if ( class_exists( 'YahnisElsts\PluginUpdateChecker\v6\PucFactory' ) ) {
    $updateChecker = YahnisElsts\PluginUpdateChecker\v6\PucFactory::buildUpdateChecker(
        'https://github.com/dafap/jquery-colorbox-dafap-edition',
        __FILE__,
        'jquery-colorbox-dafap-edition'
    );
    $updateChecker->setBranch( 'master' );
} else {
    error_log( 'ERREUR : PucFactory introuvable pour jquery-colorbox-dafap-edition !' );
}

// 2. Initialisation du plugin
use Dafap\JQueryColorbox\JQueryColorbox;

if ( ! function_exists( 'jquery_colorbox_run' ) ) {
    function jquery_colorbox_run() {
        JQueryColorbox::get_instance()->run();
    }
}

add_action( 'plugins_loaded', 'jquery_colorbox_run' );