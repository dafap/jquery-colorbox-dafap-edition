<?php
/**
 * @package Techotronic
 * @subpackage jQuery Colorbox
 *
 * @since 4.1
 * @author Arne Franken
 *
 * Handles all frontend actions for the jQuery Colorbox plugin.
 */
class JQueryColorboxFrontend {

    /**
     * Plugin settings passed from the main class.
     *
     * @var array
     */
    public $colorboxSettings;

    /**
     * Translated plugin name.
     *
     * @var string
     */
    private $pluginName;

    /**
     * Constructor.
     *
     * @param array $colorboxSettings Plugin settings.
     * @param string $pluginName Translated plugin name.
     */
    public function __construct($colorboxSettings, $pluginName) {
        $this->colorboxSettings = $colorboxSettings;
        $this->pluginName = $pluginName;

        if ($this->isFalse('removeLinkFromMetaBox')) {
            add_action('wp_meta', [ $this, 'renderMetaLink' ]);
        }

        if ($this->isTrue('autoColorbox')) {
            add_filter('the_content', [ $this, 'addColorboxGroupIdToImages' ], 100);
            add_filter('the_excerpt', [ $this, 'addColorboxGroupIdToImages' ], 100);
        }

        if ($this->isTrue('autoColorboxGalleries') || $this->isTrue('autoColorbox')) {
            add_filter('wp_get_attachment_image_attributes', [ $this, 'wpPostThumbnailClassFilter' ]);
        }

        add_action('init', [ $this, 'addJQueryJS' ], 100);

        $this->addColorboxCSS();
        $this->addColorboxJS();
        $this->addColorboxWrapperJS();
        $this->addColorboxProperties();
    }

    /**
     * Renders the plugin meta link.
     */
    public function renderMetaLink() {
        ?>
        <li id="colorboxLink">
            <?php _e('Using', JQUERYCOLORBOX_TEXTDOMAIN); ?>
            <a href="https://www.techotronic.de/plugins/jquery-colorbox/" target="_blank" title="<?php echo esc_attr($this->pluginName); ?>">
                <?php echo esc_html($this->pluginName); ?>
            </a>
        </li>
        <?php
    }

    /**
     * Adds Colorbox group ID to image tags in post content.
     *
     * @param string $content Post content.
     * @return string Modified content.
     */
    public function addColorboxGroupIdToImages($content) {
        global $post;
        if (!isset($post) || !is_object($post)) {
            return $content;
        }

        $imgPattern = "/<img([^\>]*?)>/i";
        if (preg_match_all($imgPattern, $content, $imgTags)) {
            foreach ($imgTags[0] as $imgTag) {
                if (!preg_match('/colorbox-/i', $imgTag)) {
                    if (!preg_match('/class=/i', $imgTag)) {
                        $pattern = $imgPattern;
                        $replacement = '<img class="colorbox-' . $post->ID . '" $1>';
                    } else {
                        $pattern = "/<img(.*?)class=('|\")([A-Za-z0-9 \/_\.\~\:-]*?)('|\")([^\>]*?)>/i";
                        $replacement = '<img$1class=$2$3 colorbox-' . $post->ID . '$4$5>';
                    }
                    $replacedImgTag = preg_replace($pattern, $replacement, $imgTag);
                    $content = str_replace($imgTag, $replacedImgTag, $content);
                }
            }
        }
        return $content;
    }

    /**
     * Adds Colorbox class to post thumbnail attributes.
     *
     * @param array $attribute Image attributes.
     * @return array Modified attributes.
     */
    public function wpPostThumbnailClassFilter($attribute) {
        global $post;
        if (!isset($post) || !is_object($post)) {
            return $attribute;
        }
        $attribute['class'] .= ' colorbox-' . $post->ID;
        return $attribute;
    }

    /**
     * Localizes plugin settings for JavaScript.
     */
    public function addColorboxProperties() {
        $s = $this->colorboxSettings;
        $props = [
            'jQueryColorboxVersion' => JQUERYCOLORBOX_VERSION,
            'colorboxClose' => __('close', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxNext' => __('next', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxPrevious' => __('previous', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxSlideshowStart' => __('start slideshow', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxSlideshowStop' => __('stop slideshow', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxCurrent' => __('{current} of {total} images', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxXhrError'=> __('This content failed to load.', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxImgError'=> __('This image failed to load.', JQUERYCOLORBOX_TEXTDOMAIN),
            'colorboxSlideshow' => $s['slideshow'] ? 'true' : 'false',
            'colorboxSlideshowAuto' => $s['slideshowAuto'] ? 'true' : 'false',
            'colorboxScalePhotos' => $s['scalePhotos'] ? 'true' : 'false',
            'colorboxPreloading' => $s['preloading'] ? 'true' : 'false',
            'colorboxOverlayClose' => $s['overlayClose'] ? 'true' : 'false',
            'colorboxLoop' => !$s['disableLoop'] ? 'true' : 'false',
            'colorboxEscKey' => !$s['disableKeys'] ? 'true' : 'false',
            'colorboxArrowKey' => !$s['disableKeys'] ? 'true' : 'false',
            'colorboxScrolling' => !$s['displayScrollbar'] ? 'true' : 'false',
            'colorboxOpacity' => $s['opacity'],
            'colorboxTransition' => $s['transition'],
            'colorboxSpeed' => $s['speed'],
            'colorboxSlideshowSpeed' => $s['slideshowSpeed'],
            'colorboxImageMaxWidth' => $s['maxWidth'] === "false" ? 'false' : $s['maxWidthValue'] . $s['maxWidthUnit'],
            'colorboxImageMaxHeight' => $s['maxHeight'] === "false" ? 'false' : $s['maxHeightValue'] . $s['maxHeightUnit'],
            'colorboxImageHeight' => $s['height'] === "false" ? 'false' : $s['heightValue'] . $s['heightUnit'],
            'colorboxImageWidth' => $s['width'] === "false" ? 'false' : $s['widthValue'] . $s['widthUnit'],
            'colorboxLinkHeight' => $s['linkHeight'] === "false" ? 'false' : $s['linkHeightValue'] . $s['linkHeightUnit'],
            'colorboxLinkWidth' => $s['linkWidth'] === "false" ? 'false' : $s['linkWidthValue'] . $s['linkWidthUnit'],
            'colorboxInitialHeight' => $s['initialHeight'],
            'colorboxInitialWidth' => $s['initialWidth'],
            'autoColorboxJavaScript' => $s['autoColorboxJavaScript'],
            'autoHideFlash' => $s['autoHideFlash'],
            'autoColorbox' => $s['autoColorbox'],
            'autoColorboxGalleries' => $s['autoColorboxGalleries'],
            'addZoomOverlay' => $s['addZoomOverlay'],
            'useGoogleJQuery' => $s['useGoogleJQuery'],
            'colorboxAddClassToLinks' => $s['colorboxAddClassToLinks']
        ];
        wp_localize_script('colorbox', 'jQueryColorboxSettingsArray', $props);
    }

    /**
     * Enqueues the wrapper JavaScript file.
     */
    public function addColorboxWrapperJS() {
        $path = $this->isTrue('debugMode') ? 'js/jquery-colorbox-wrapper.js' : 'js/jquery-colorbox-wrapper-min.js';
        wp_enqueue_script('colorbox-wrapper', JQUERYCOLORBOX_PLUGIN_URL . '/' . $path, ['colorbox'], JQUERYCOLORBOX_VERSION, $this->colorboxSettings['javascriptInFooter']);
    }

        /**
     * Enqueues the main Colorbox JavaScript file.
     *
     * @return void
     */
    public function addColorboxJS() {
        $path = $this->isTrue('debugMode') ? 'js/jquery.colorbox.js' : 'js/jquery.colorbox-min.js';
        wp_enqueue_script(
            'colorbox',
            JQUERYCOLORBOX_PLUGIN_URL . '/' . $path,
            [ 'jquery' ],
            COLORBOXLIBRARY_VERSION,
            $this->colorboxSettings['javascriptInFooter']
        );
    }

    /**
     * Enqueues jQuery from Google CDN if enabled, otherwise uses WordPress default.
     *
     * @return void
     */
    public function addJQueryJS() {
        if ($this->isTrue('useGoogleJQuery')) {
            $version = JQUERYLIBRARY_VERSION;
            $path = $this->isTrue('debugMode') ? "jquery-$version.js" : "jquery-$version.min.js";
            $url = "https://code.jquery.com/$path";

            wp_deregister_script('jquery');
            wp_register_script('jquery', $url, false, $version, true);
        }

        wp_enqueue_script('jquery');
    }

    /**
     * Enqueues the plugin's CSS theme and optional zoom overlay.
     *
     * @return void
     */
    public function addColorboxCSS() {
        $theme = $this->colorboxSettings['colorboxTheme'];

        wp_register_style(
            'colorbox-' . $theme,
            JQUERYCOLORBOX_PLUGIN_URL . '/themes/' . $theme . '/colorbox.css',
            [],
            JQUERYCOLORBOX_VERSION,
            'screen'
        );
        wp_enqueue_style('colorbox-' . $theme);

        if ($this->isTrue('addZoomOverlay')) {
            wp_register_style(
                'colorbox-css',
                JQUERYCOLORBOX_PLUGIN_URL . '/css/jquery-colorbox-zoom.css',
                [],
                COLORBOXLIBRARY_VERSION
            );
            wp_enqueue_style('colorbox-css');
        }
    }

    /** 
     * Checks whether a given option is enabled (truthy).
     *
     * @param string $optionName Name of the option.
     * @return bool
     */
    public function isTrue($optionName) {
        return isset($this->colorboxSettings[$optionName]) && $this->colorboxSettings[$optionName];
    }

    /**
     * Checks whether a given option is disabled (falsy).
     *
     * @param string $optionName Name of the option.
     * @return bool
     */
    public function isFalse($optionName) {
        return isset($this->colorboxSettings[$optionName]) && !$this->colorboxSettings[$optionName];
    }
}
