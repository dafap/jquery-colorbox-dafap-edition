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
 * @file      settings-page.php
 * @purpose   Génère l'interface HTML de la page de réglages du plugin dans l'administration WordPress
 */

require_once 'settings-page/sp-javascript-header.php';
?>

<div class="wrap">
  <?php if (!is_plugin_active('sumome/sumome.php')): ?>
    <?php add_thickbox(); ?>
    <style type="text/css">
      #aio_global_notification a.button:active { vertical-align: baseline; }
    </style>
    <div id="aio_global_notification" style="border:3px solid #31964D;position:relative;background:#6AB07B;color:#ffffff;height:70px;margin:5px 0 15px;padding:1px 12px;">
      <p style="font-size:16px;line-height:40px;">
        <?php _e('Tools to grow your Email List, Social Sharing and Analytics.', 'jquery-colorbox-dafap-edition'); ?>
        &nbsp;
        <a style="background-color: #6267BE; border-color: #3C3F76;"
           href="<?php echo esc_url(admin_url('plugin-install.php?tab=plugin-information&plugin=sumome&TB_iframe=true&width=743&height=500')); ?>"
           class="thickbox button button-primary">
          <?php _e('Get SumoMe WordPress Plugin', 'jquery-colorbox-dafap-edition'); ?>
        </a>
      </p>
    </div>
  <?php endif; ?>

  <div>
    <h1><?php printf(esc_html__('%1$s Settings', 'jquery-colorbox-dafap-edition'), esc_html(JQUERYCOLORBOX_NAME)); ?></h1>
    <br class="clear"/>
    <?php settings_fields(JQUERYCOLORBOX_SETTINGSNAME); ?>
    <?php require_once 'settings-page/sp-left-column.php'; ?>
    <?php // require_once 'settings-page/sp-right-column.php'; ?>
  </div>

  <?php require_once 'settings-page/sp-footer.php'; ?>
</div>
