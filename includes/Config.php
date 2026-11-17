<?php
declare(strict_types=1);

namespace Dafap\JQueryColorbox;

/**
 * Classe de configuration du plugin jQuery Colorbox Dafap Edition
 */
class Config
{
    public const DEBUG_MODE = 'false';
    public const REPO_URL = 'https://github.com/dafap/jquery-colorbox-dafap-edition';
    public const NAME_RAW = 'jQuery Colorbox';
    public const SETTINGS_NAME = 'jquery-colorbox_settings';
    public const SETTINGS_PAGE_SLUG = 'jquery-colorbox-settings';
    public const COLORBOX_VERSION = '1.6.4';

    public static function getDefaultSettings(): array
    {
        return [
            'jQueryColorboxVersion' => JQC_VERSION,
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
            'autoColorbox' => true,
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
            'debugMode' => self::DEBUG_MODE,
            'autoColorboxJavaScript' => false,
            'colorboxAddClassToLinks' => false,
            'addZoomOverlay' => false,
            'removeLinkFromMetaBox' => true
        ];
    }

    public static function getThemes(): array
    {
        return [
            'theme1' => __('Theme #1', JQC_TEXTDOMAIN),
            'theme2' => __('Theme #2', JQC_TEXTDOMAIN),
            'theme3' => __('Theme #3', JQC_TEXTDOMAIN),
            'theme4' => __('Theme #4', JQC_TEXTDOMAIN),
            'theme5' => __('Theme #5', JQC_TEXTDOMAIN),
            'theme6' => __('Theme #6', JQC_TEXTDOMAIN),
            'theme7' => __('Theme #7', JQC_TEXTDOMAIN),
            'theme8' => __('Theme #8', JQC_TEXTDOMAIN),
            'theme9' => __('Theme #9', JQC_TEXTDOMAIN),
            'theme10' => __('Theme #10', JQC_TEXTDOMAIN),
            'theme11' => __('Theme #11', JQC_TEXTDOMAIN),
        ];
    }

    public static function getUnits(): array
    {
        return [
            'px'  => __('Pixels', JQC_TEXTDOMAIN),
            'em'  => __('Ems', JQC_TEXTDOMAIN),
            'rem' => __('Rems', JQC_TEXTDOMAIN),
            '%'   => __('Pourcentage', JQC_TEXTDOMAIN),
        ];
    }

    public static function getTransitions(): array
    {
        return [
            'elastic' => __('Élasticité', JQC_TEXTDOMAIN),
            'fade'    => __('Fondu', JQC_TEXTDOMAIN),
            'none'    => __('Aucun', JQC_TEXTDOMAIN),
        ];
    }
}