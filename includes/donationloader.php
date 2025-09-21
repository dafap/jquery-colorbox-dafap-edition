<?php
/**
 * jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @link      https://github.com/dafap/jquery-colorbox-dafap-edition
 * @version   5.0
 * @since     5.0
 * @file      class-jquery-colorbox-donation-loader.php
 * @purpose   Gestion des appels Ajax vers XML-RPC pour les dons, avec mise en cache et localisation JS
 */

declare(strict_types=1);

if (!defined('JQUERYCOLORBOX_DONATIONLOADER_XMLRPC_URL')) {
  define('JQUERYCOLORBOX_DONATIONLOADER_XMLRPC_URL', 'http://www.techotronic.de/wordpress/xmlrpc.php');
}

if (!defined('JQUERYCOLORBOX_DONATIONLOADER_CACHETIME')) {
  define('JQUERYCOLORBOX_DONATIONLOADER_CACHETIME', 6000); // 100 minutes
}

class JQueryColorboxDonationLoader {

  /**
   * Agent utilisateur pour les requêtes XML-RPC
   * @var string
   */
  private string $donationLoaderUserAgent;

  /**
   * Nom du plugin utilisé dans les requêtes
   * @var string
   */
  private string $donationLoaderPluginName = 'jq_colorbox';

  /**
   * URL du plugin pour charger les scripts
   * @var string
   */
  private string $donationLoaderPluginUrl;

  /**
   * Constructeur : enregistre les hooks Ajax
   */
  public function __construct() {
    $this->donationLoaderUserAgent = defined('JQUERYCOLORBOX_USERAGENT') ? JQUERYCOLORBOX_USERAGENT : 'ColorboxDafap';
    $this->donationLoaderPluginUrl = defined('JQUERYCOLORBOX_PLUGIN_URL') ? JQUERYCOLORBOX_PLUGIN_URL : plugin_dir_url(__FILE__);

    add_action('wp_ajax_load-JQueryColorboxTopDonations', [$this, 'getJQueryColorboxTopDonations']);
    add_action('wp_ajax_load-JQueryColorboxLatestDonations', [$this, 'getJQueryColorboxLatestDonations']);
  }

  /**
   * Récupère les dons les plus importants via XML-RPC
   *
   * @return void
   */
  public function getJQueryColorboxTopDonations(): void {
    $this->getAndReturnDonations('manageDonations.getTopDonations', 'top');
  }

  /**
   * Récupère les derniers dons via XML-RPC
   *
   * @return void
   */
  public function getJQueryColorboxLatestDonations(): void {
    $this->getAndReturnDonations('manageDonations.getLatestDonations', 'latest');
  }

  /**
   * Enregistre et localise le script JavaScript pour les dons
   *
   * @return void
   */
  public function registerDonationJavaScript(): void {
    $javaScriptArray = [
      'ajaxurl' => admin_url('admin-ajax.php'),
      'pluginName' => $this->donationLoaderPluginName,
    ];

    wp_register_script('donation', $this->donationLoaderPluginUrl . '/js/donation.js', ['jquery']);
    wp_enqueue_script('donation');
    wp_localize_script('donation', 'Donation', $javaScriptArray);
  }

  /**
   * Méthode générique pour interroger XML-RPC et retourner les données via Ajax
   *
   * @param string $remoteProcedureCall Nom de la méthode XML-RPC
   * @param string $identifier Identifiant de cache
   * @return void
   */
  private function getAndReturnDonations(string $remoteProcedureCall, string $identifier): void {
    $pluginName = $_POST['pluginName'] ?? $this->donationLoaderPluginName;
    $key = $identifier . '_' . $pluginName;

    $response = get_site_transient($key);

    if ($response === false) {
      if (class_exists('IXR_Client')) {
        $ixrClient = new IXR_Client(JQUERYCOLORBOX_DONATIONLOADER_XMLRPC_URL);
        $ixrClient->useragent = $this->donationLoaderUserAgent;
        $ixrClient->query($remoteProcedureCall, $pluginName);
        $response = $ixrClient->getResponse();
      }
      set_site_transient($key, serialize($response), JQUERYCOLORBOX_DONATIONLOADER_CACHETIME);
    } else {
      $response = unserialize($response);
    }

    header("Content-Type: text/html");
    echo $response;
    exit;
  }
}
