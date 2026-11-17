<?php
/**
 * Trait de rendu des fichiers de template pour jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap)
 * @license   GPL-2.0-or-later
 * @file      includes/Traits/TemplateRenderTrait.php
 */

declare(strict_types=1);

namespace Dafap\JQueryColorbox\Traits;

trait TemplateRenderTrait
{
    /**
     * Charge et affiche un fichier de template avec des variables passées en contexte.
     *
     * @param string $templatePath Chemin relatif du template par rapport au dossier views/ du plugin.
     * @param array  $data         Tableau associatif des variables à rendre disponibles dans le template.
     * @return void
     */
    public function templateRender(string $templatePath, array $data = []): void
    {
        // Utilisation de JQC_PATH défini à la racine du plugin
        $file = rtrim(JQC_PATH, '/\\') . '/views/' . ltrim($templatePath, '/\\');

        if (!file_exists($file)) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                echo '<div class="notice notice-error"><p>' . 
                    sprintf(esc_html__('Template introuvable : %s', JQC_TEXTDOMAIN), esc_html($file)) . 
                    '</p></div>';
            }
            return;
        }

        // Extrait les données pour les rendre accessibles sous forme de variables dans la vue
        if (!empty($data)) {
            extract($data, EXTR_OVERWRITE);
        }

        include $file;
    }
}