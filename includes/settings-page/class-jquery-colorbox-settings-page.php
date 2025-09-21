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
 * @file      class-jquery-colorbox-settings-page.php
 * @purpose   Génère dynamiquement la page de réglages du plugin Colorbox dans l’administration WordPress
 */

class JQueryColorboxSettingsPage {

  /**
   * Tableau des champs à afficher dans la page de réglages
   * @var array
   */
  private array $colorboxFields = [
    [
      'id' => 'enable_colorbox',
      'label' => 'Activer Colorbox',
      'type' => 'checkbox',
      'description' => 'Active le lightbox Colorbox sur les images.',
    ],
    [
      'id' => 'colorbox_theme',
      'label' => 'Thème Colorbox',
      'type' => 'select',
      'options' => [
        'default' => 'Défaut',
        'minimal' => 'Minimal',
        'dark' => 'Sombre',
        'light' => 'Clair',
      ],
      'description' => 'Choisissez le thème visuel du lightbox.',
    ],
    [
      'id' => 'animation_speed',
      'label' => 'Vitesse d’animation',
      'type' => 'text',
      'description' => 'Durée de transition en millisecondes (ex : 350).',
    ],
  ];

  /**
   * Affiche le formulaire HTML de la page de réglages
   */
  public function render(): void {
    ?>
    <div id="jquery-colorbox-plugin-settings" class="postbox">
      <div class="inside">
        <h3 id="colorbox-settings"><?php echo esc_html__('Réglages Colorbox', JQUERYCOLORBOX_TEXTDOMAIN); ?></h3>
        <table class="form-table">
          <?php foreach ($this->colorboxFields as $field): ?>
            <tr valign="top">
              <th scope="row">
                <label for="<?php echo esc_attr($field['id']); ?>">
                  <?php echo esc_html__($field['label'], JQUERYCOLORBOX_TEXTDOMAIN); ?>
                </label>
              </th>
              <td>
                <?php echo $this->renderField($field); ?>
                <?php if (!empty($field['description'])): ?>
                  <br/><span class="description"><?php echo esc_html__($field['description'], JQUERYCOLORBOX_TEXTDOMAIN); ?></span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

        <p class="submit">
          <input type="hidden" name="action" value="jQueryColorboxUpdateSettings"/>
          <input type="submit" name="jQueryColorboxUpdateSettings" class="button-primary" value="<?php echo esc_attr__('Enregistrer les modifications', JQUERYCOLORBOX_TEXTDOMAIN); ?>"/>
        </p>
      </div>
    </div>
    <?php
  }

  /**
   * Génère le champ HTML selon son type
   *
   * @param array $field Définition du champ
   * @return string HTML généré
   */
  private function renderField(array $field): string {
    $value = get_option($field['id'], '');
    $html = '';

    switch ($field['type']) {
      case 'checkbox':
        $html .= sprintf(
          '<input type="checkbox" id="%1$s" name="%1$s" value="1" %2$s />',
          esc_attr($field['id']),
          checked($value, '1', false)
        );
        break;

      case 'select':
        $html .= sprintf('<select id="%1$s" name="%1$s">', esc_attr($field['id']));
        foreach ($field['options'] as $key => $label) {
          $html .= sprintf(
            '<option value="%1$s" %2$s>%3$s</option>',
            esc_attr($key),
            selected($value, $key, false),
            esc_html__($label, JQUERYCOLORBOX_TEXTDOMAIN)
          );
        }
        $html .= '</select>';
        break;

      case 'text':
      default:
        $html .= sprintf(
          '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
          esc_attr($field['id']),
          esc_attr($value)
        );
        break;
    }

    return $html;
  }
}
