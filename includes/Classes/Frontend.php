<?php
/**
 * Classe Frontend du plugin jQuery Colorbox Dafap Edition
 *
 * @package   jquery-colorbox-dafap-edition
 * @author    Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken
 * @copyright 2025 Alain Pomirol
 * @license   GPL-2.0-or-later
 * @file      includes/Classes/Frontend.php
 * @purpose   Gère le comportement public du plugin (injection des scripts, styles, filtres d'images et liens meta).
 */

declare(strict_types=1);

namespace Dafap\JQueryColorbox\Classes;

use Dafap\JQueryColorbox\Traits\TemplateRenderTrait;
use Dafap\JQueryColorbox\Traits\UtilsTrait;
use Dafap\JQueryColorbox\Config;

class Frontend
{
    use TemplateRenderTrait, UtilsTrait;

    private array $colorboxSettings = [];
    private string $pluginName = '';

    /**
     * Initialise les hooks et les filtres côté public (frontend).
     *
     * @param array $colorboxSettings Réglages utilisateur actuels du plugin.
     */
    public function __construct(array $colorboxSettings)
    {
        $this->colorboxSettings = $colorboxSettings;
        $this->pluginName = __('jQuery Colorbox', JQC_TEXTDOMAIN);

        if (!$this->isTrue('removeLinkFromMetaBox')) {
            add_action('wp_meta', [$this, 'renderMetaLink']);
        }

        if ($this->isTrue('autoColorbox')) {
            add_filter('the_content', [$this, 'addColorboxGroupIdToImages'], 100);
            add_filter('the_excerpt', [$this, 'addColorboxGroupIdToImages'], 100);
        }

        if ($this->isTrue('autoColorboxGalleries') || $this->isTrue('autoColorbox')) {
            add_filter('wp_get_attachment_image_attributes', [$this, 'wpPostThumbnailClassFilter']);
        }

        add_action('wp_enqueue_scripts', [$this, 'enqueueFrontendScripts']);
    }

    /**
     * Affiche le lien d'information sur le plugin dans le bloc meta du site public.
     */
    public function renderMetaLink(): void
    {
        $this->templateRender('frontend/meta-link.phtml', [
            'pluginName'    => $this->pluginName,
            'repoUrl'       => Config::REPO_URL,
            'pluginVersion' => JQC_VERSION,
            'textDomain'    => JQC_TEXTDOMAIN,
        ]);
    }

    /**
     * Ajoute automatiquement les classes de groupe Colorbox aux balises images du contenu.
     *
     * @param string $content Contenu HTML de l'article/page.
     * @return string Contenu modifié.
     */
    public function addColorboxGroupIdToImages(string $content): string
    {
        global $post;
        if (!isset($post) || !is_object($post)) {
            return $content;
        }

        $post_id = $post->ID;
        $class_to_add = ' colorbox-' . $post_id;
        $pattern = '/<img([^>]*?)class\s*=\s*([\'"])([^\'"]*?)[\'"]([^>]*?)>/i';

        $content = preg_replace_callback($pattern, function ($matches) use ($class_to_add) {
            if (str_contains($matches[3], 'colorbox-')) {
                return $matches[0];
            }
            return "<img{$matches[1]}class={$matches[2]}{$matches[3]}{$class_to_add}{$matches[2]}{$matches[4]}>";
        }, $content);

        $content = preg_replace('/<img((?:[^>]*?)(?:\s+href|\s+src)[^>]*?)>/i', '<img class="' . $class_to_add . '"$1>', $content);

        return $content;
    }

    /**
     * Ajoute la classe Colorbox aux attributs des miniatures de publications.
     *
     * @param array $attributes Attributs HTML de l'image.
     * @return array Attributs modifiés.
     */
    public function wpPostThumbnailClassFilter(array $attributes): array
    {
        global $post;
        if (!isset($post) || !is_object($post)) {
            return $attributes;
        }
        $attributes['class'] = ($attributes['class'] ?? '') . ' colorbox-' . $post->ID;
        return $attributes;
    }

    /**
     * Coordonne l'enfilage des styles et scripts nécessaires au fonctionnement de Colorbox.
     */
    public function enqueueFrontendScripts(): void
    {
        wp_enqueue_script('jquery');
        $this->addColorboxCSS();
        $this->addColorboxJS();
        $this->addColorboxWrapperJS();
        $this->addColorboxProperties();
    }

    /**
     * Enfile le fichier CSS du thème actif et éventuellement l'effet de zoom.
     */
    private function addColorboxCSS(): void
    {
        $theme = $this->colorboxSettings['colorboxTheme'] ?? 'theme1';

        wp_register_style(
            'colorbox-' . $theme,
            JQC_URL . 'assets/themes/' . $theme . '/colorbox.css',
            [],
            JQC_VERSION,
            'screen'
        );
        wp_enqueue_style('colorbox-' . $theme);

        if ($this->isTrue('addZoomOverlay')) {
            wp_register_style(
                'colorbox-zoom-css',
                JQC_URL . 'assets/css/jquery-colorbox-zoom.css',
                [],
                Config::COLORBOX_VERSION
            );
            wp_enqueue_style('colorbox-zoom-css');
        }
    }

    /**
     * Enfile le script d'initialisation wrapper de Colorbox.
     */
    private function addColorboxWrapperJS(): void
    {

        $path = $this->isTrue('debugMode') ? 'assets/js/jquery-colorbox-wrapper.js' : 'assets/js/jquery-colorbox-wrapper.min.js';

        wp_enqueue_script(
            'colorbox-wrapper',
            JQC_URL . $path,
            ['colorbox'],
            JQC_VERSION,
            (bool)$this->colorboxSettings['javascriptInFooter']
        );
    }

    /**
     * Formate une dimension de réglage (largeur/hauteur).
     *
     * @param string $settingName Nom du réglage (ex: 'maxWidth').
     * @return string Valeur formatée ou 'false'.
     */
    private function formatColorboxDimension(string $settingName): string
    {
        if (($this->colorboxSettings[$settingName] ?? 'false') === 'false') {
            return 'false';
        }
        return ($this->colorboxSettings[$settingName . 'Value'] ?? '') . ($this->colorboxSettings[$settingName . 'Unit'] ?? '%');
    }

    /**
     * Transmet les propriétés de configuration du plugin au script JavaScript via wp_localize_script.
     */
    private function addColorboxProperties(): void
    {
        $props = [
            'jQueryColorboxVersion'  => JQC_VERSION,
            'colorboxClose'          => __('close', JQC_TEXTDOMAIN),
            'colorboxNext'           => __('next', JQC_TEXTDOMAIN),
            'colorboxPrevious'       => __('previous', JQC_TEXTDOMAIN),
            'colorboxSlideshowStart' => __('start slideshow', JQC_TEXTDOMAIN),
            'colorboxSlideshowStop'  => __('stop slideshow', JQC_TEXTDOMAIN),
            'colorboxCurrent'        => __('{current} of {total} images', JQC_TEXTDOMAIN),
            'colorboxXhrError'       => __('This content failed to load.', JQC_TEXTDOMAIN),
            'colorboxImgError'       => __('This image failed to load.', JQC_TEXTDOMAIN),
            'colorboxSlideshow'      => !empty($this->colorboxSettings['slideshow']) ? 'true' : 'false',
            'colorboxSlideshowAuto'  => !empty($this->colorboxSettings['slideshowAuto']) ? 'true' : 'false',
            'colorboxScalePhotos'    => !empty($this->colorboxSettings['scalePhotos']) ? 'true' : 'false',
            'colorboxPreloading'     => !empty($this->colorboxSettings['preloading']) ? 'true' : 'false',
            'colorboxOverlayClose'   => !empty($this->colorboxSettings['overlayClose']) ? 'true' : 'false',
            'colorboxLoop'           => empty($this->colorboxSettings['disableLoop']) ? 'true' : 'false',
            'colorboxEscKey'         => empty($this->colorboxSettings['disableKeys']) ? 'true' : 'false',
            'colorboxArrowKey'       => empty($this->colorboxSettings['disableKeys']) ? 'true' : 'false',
            'colorboxScrolling'      => empty($this->colorboxSettings['displayScrollbar']) ? 'true' : 'false',
            'colorboxOpacity'        => $this->colorboxSettings['opacity'] ?? '0.85',
            'colorboxTransition'     => $this->colorboxSettings['transition'] ?? 'elastic',
            'colorboxSpeed'          => $this->colorboxSettings['speed'] ?? '350',
            'colorboxSlideshowSpeed' => $this->colorboxSettings['slideshowSpeed'] ?? '2500',
            'colorboxImageMaxWidth'  => $this->formatColorboxDimension('maxWidth'),
            'colorboxImageMaxHeight' => $this->formatColorboxDimension('maxHeight'),
            'colorboxImageHeight'    => $this->formatColorboxDimension('height'),
            'colorboxImageWidth'     => $this->formatColorboxDimension('width'),
            'colorboxLinkHeight'     => $this->formatColorboxDimension('linkHeight'),
            'colorboxLinkWidth'      => $this->formatColorboxDimension('linkWidth'),
            'colorboxInitialHeight'  => $this->colorboxSettings['initialHeight'] ?? 100,
            'colorboxInitialWidth'   => $this->colorboxSettings['initialWidth'] ?? 300,
            'autoColorboxJavaScript' => $this->colorboxSettings['autoColorboxJavaScript'] ?? 'false',
            'autoHideFlash'          => $this->colorboxSettings['autoHideFlash'] ?? 'false',
            'autoColorbox'           => $this->colorboxSettings['autoColorbox'] ?? 'false',
            'autoColorboxGalleries'  => $this->colorboxSettings['autoColorboxGalleries'] ?? 'false',
            'addZoomOverlay'         => $this->colorboxSettings['addZoomOverlay'] ?? 'false',
            'colorboxAddClassToLinks'=> $this->colorboxSettings['colorboxAddClassToLinks'] ?? 'false'
        ];

        wp_localize_script('colorbox', 'jQueryColorboxSettingsArray', $props);
    }

    /**
     * Enfile le fichier JavaScript principal de la bibliothèque Colorbox.
     */
    private function addColorboxJS(): void
    {
        $path = $this->isTrue('debugMode') ? 'assets/js/jquery.colorbox.js' : 'assets/js/jquery.colorbox-min.js';

        wp_enqueue_script(
            'colorbox',
            JQC_URL . $path,
            ['jquery'],
            Config::COLORBOX_VERSION,
            (bool)$this->colorboxSettings['javascriptInFooter']
        );
    }
}