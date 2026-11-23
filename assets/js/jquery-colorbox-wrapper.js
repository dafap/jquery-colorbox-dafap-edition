/**
 * jQuery Colorbox Wrapper (Dafap Edition)
 */
(function($) {
  'use strict';

  var COLORBOX_INTERNAL_LINK_PATTERN = /^#.*/;
  var COLORBOX_SUFFIX_PATTERN = /\.(?:jpe?g|gif|png|bmp|webp)/i;
  var COLORBOX_MANUAL = 'colorbox-manual';
  var COLORBOX_OFF = 'colorbox-off';
  var COLORBOX_CLASS_PATTERN = 'colorbox-[0-9]+';
  var COLORBOX_LINK_CLASS_PATTERN = 'colorbox-link-[0-9]+';

  var ColorboxLocal = {};

  /**
   * Calcule maxWidth et maxHeight en fonction de la taille réelle de la fenêtre
   * afin d'éviter toute troncature sur smartphone en portrait ou paysage.
   */
  function getResponsiveMaxDimensions(configuredMaxWidth, configuredMaxHeight) {
    var vw = window.innerWidth || document.documentElement.clientWidth || 0;
    var vh = window.innerHeight || document.documentElement.clientHeight || 0;
    var isSmallScreen = vw > 0 && vw < 768;

    // Marge confortable pour éviter le débordement latéral ou vertical sur mobile
    var viewportMaxW = isSmallScreen ? Math.round(vw * 0.92) : Math.round(vw * 0.90);
    var viewportMaxH = isSmallScreen ? Math.round(vh * 0.82) : Math.round(vh * 0.88);

    var finalMaxW = viewportMaxW ? viewportMaxW + 'px' : configuredMaxWidth;
    var finalMaxH = viewportMaxH ? viewportMaxH + 'px' : configuredMaxHeight;

    // Si une valeur max est configurée en pixels, on borne au maximum du viewport
    if (configuredMaxWidth && typeof configuredMaxWidth === 'string' && configuredMaxWidth.indexOf('px') !== -1 && viewportMaxW) {
      var numW = parseInt(configuredMaxWidth, 10);
      if (!isNaN(numW) && numW > 0) {
        finalMaxW = Math.min(numW, viewportMaxW) + 'px';
      }
    }

    if (configuredMaxHeight && typeof configuredMaxHeight === 'string' && configuredMaxHeight.indexOf('px') !== -1 && viewportMaxH) {
      var numH = parseInt(configuredMaxHeight, 10);
      if (!isNaN(numH) && numH > 0) {
        finalMaxH = Math.min(numH, viewportMaxH) + 'px';
      }
    }

    return {
      maxWidth: finalMaxW,
      maxHeight: finalMaxH
    };
  }

  /**
   * Ajoute la classe colorbox-link aux liens pointant vers des images
   * qui ne contiennent pas déjà une balise <img>.
   */
  function colorboxAddClassToLinks() {
    $('a').each(function(_, link) {
      var $link = $(link);
      if ($link.find('img').length > 0) {
        return;
      }

      var linkClass = $link.attr('class');
      var linkHref = $link.attr('href');

      if (linkHref && linkHref.match(COLORBOX_SUFFIX_PATTERN)) {
        if (!linkClass || !linkClass.match('colorbox')) {
          $link.addClass('colorbox-link');
        }
      }
    });
  }

  /**
   * Ajoute la classe colorbox-manual aux balises <img> qui n'ont pas encore de classe colorbox.
   */
  function colorboxAddManualClass() {
    $('img').each(function(_, image) {
      var $img = $(image);
      var imgClass = $img.attr('class');
      if (!imgClass || !imgClass.match('colorbox')) {
        $img.addClass(COLORBOX_MANUAL);
      }
    });
  }

  /**
   * Sélectionne les liens d'images et les liens colorbox-link.
   */
  function colorboxSelector() {
    $('a:has(img[class*=colorbox-]):not(.colorbox-off)').each(function(index, link) {
      ColorboxLocal = $.extend(true, {}, window.jQueryColorboxSettingsArray);

      var dims = getResponsiveMaxDimensions(
        ColorboxLocal.colorboxImageMaxWidth,
        ColorboxLocal.colorboxImageMaxHeight
      );

      ColorboxLocal.colorboxMaxWidth = dims.maxWidth;
      ColorboxLocal.colorboxMaxHeight = dims.maxHeight;
      ColorboxLocal.colorboxHeight = ColorboxLocal.colorboxImageHeight;
      ColorboxLocal.colorboxWidth = ColorboxLocal.colorboxImageWidth;

      var linkHref = $(link).attr('href');
      if (linkHref && linkHref.match(COLORBOX_SUFFIX_PATTERN)) {
        colorboxImage(index, link);
      }
    });

    $('a[class*=colorbox-link]').each(function(index, link) {
      ColorboxLocal = $.extend(true, {}, window.jQueryColorboxSettingsArray);

      var linkHref = $(link).attr('href');
      if (linkHref) {
        colorboxLink(index, link, linkHref);
      }
    });
  }

  /**
   * Prépare les options Colorbox pour un lien contenant une image.
   */
  function colorboxImage(index, link) {
    var $link = $(link);
    var $image = $link.find('img:first');
    var linkClasses = $link.attr('class');

    ColorboxLocal.colorboxGroupId = null;

    if (linkClasses) {
      ColorboxLocal.colorboxGroupId = linkClasses.match(COLORBOX_CLASS_PATTERN) || linkClasses.match(COLORBOX_MANUAL);
    }

    if (!ColorboxLocal.colorboxGroupId) {
      var imageClasses = $image.attr('class');
      if (imageClasses && !imageClasses.match(COLORBOX_OFF)) {
        ColorboxLocal.colorboxGroupId = imageClasses.match(COLORBOX_CLASS_PATTERN) || imageClasses.match(COLORBOX_MANUAL);
      }

      if (ColorboxLocal.colorboxGroupId) {
        ColorboxLocal.colorboxGroupId = ColorboxLocal.colorboxGroupId.toString().split('-')[1];

        if (ColorboxLocal.colorboxGroupId === 'manual') {
          ColorboxLocal.colorboxGroupId = 'nofollow';
        }

        var imageTitle = $image.attr('title');
        var imageAlt = $image.attr('alt');
        ColorboxLocal.colorboxTitle = imageTitle !== undefined ? imageTitle : (imageAlt !== undefined ? imageAlt : '');

        if (window.jQueryColorboxSettingsArray.addZoomOverlay === 'true') {
          colorboxAddZoomOverlayToImages($link, $image);
        }

        colorboxWrapper(link);
      }
    }
  }

  /**
   * Prépare les options Colorbox pour un lien avec classe colorbox-link.
   */
  function colorboxLink(index, link, linkHref) {
    var $link = $(link);
    var groupMatch = ($link.attr('class') || '').match(COLORBOX_LINK_CLASS_PATTERN);

    if (groupMatch !== null) {
      ColorboxLocal.colorboxGroupId = groupMatch.toString().split('-')[2];
    } else {
      ColorboxLocal.colorboxGroupId = 'nofollow';
    }

    var linkTitle = $link.attr('title');
    ColorboxLocal.colorboxTitle = linkTitle !== undefined ? linkTitle : '';

    if (linkHref.match(COLORBOX_SUFFIX_PATTERN)) {
      var dims = getResponsiveMaxDimensions(
        ColorboxLocal.colorboxImageMaxWidth,
        ColorboxLocal.colorboxImageMaxHeight
      );
      ColorboxLocal.colorboxMaxWidth = dims.maxWidth;
      ColorboxLocal.colorboxMaxHeight = dims.maxHeight;
      ColorboxLocal.colorboxHeight = ColorboxLocal.colorboxImageHeight;
      ColorboxLocal.colorboxWidth = ColorboxLocal.colorboxImageWidth;
    } else {
      ColorboxLocal.colorboxMaxWidth = false;
      ColorboxLocal.colorboxMaxHeight = false;
      ColorboxLocal.colorboxHeight = ColorboxLocal.colorboxLinkHeight;
      ColorboxLocal.colorboxWidth = ColorboxLocal.colorboxLinkWidth;

      if (linkHref.match(COLORBOX_INTERNAL_LINK_PATTERN)) {
        ColorboxLocal.colorboxInline = true;
      } else {
        ColorboxLocal.colorboxIframe = true;
      }
    }

    colorboxWrapper(link);
  }

  /**
   * Normalise les booléens issus de wp_localize_script et appelle $.colorbox().
   */
  function colorboxWrapper(link) {
    $.each(ColorboxLocal, function(key, value) {
      if (value === 'false') {
        ColorboxLocal[key] = false;
      } else if (value === 'true') {
        ColorboxLocal[key] = true;
      }
    });

    // Garantit la mise à l'échelle des photos sur écrans réduits
    var scalePhotosValue = ColorboxLocal.colorboxScalePhotos !== undefined ? ColorboxLocal.colorboxScalePhotos : true;

    $(link).colorbox({
      transition: ColorboxLocal.colorboxTransition,
      speed: parseInt(ColorboxLocal.colorboxSpeed, 10) || 350,
      title: ColorboxLocal.colorboxTitle,
      rel: ColorboxLocal.colorboxGroupId,
      scalePhotos: scalePhotosValue,
      scrolling: ColorboxLocal.colorboxScrolling,
      opacity: ColorboxLocal.colorboxOpacity,
      preloading: ColorboxLocal.colorboxPreloading,
      overlayClose: ColorboxLocal.colorboxOverlayClose,
      escKey: ColorboxLocal.colorboxEscKey,
      arrowKey: ColorboxLocal.colorboxArrowKey,
      loop: ColorboxLocal.colorboxLoop,
      current: ColorboxLocal.colorboxCurrent,
      previous: ColorboxLocal.colorboxPrevious,
      next: ColorboxLocal.colorboxNext,
      close: ColorboxLocal.colorboxClose,
      xhrError: ColorboxLocal.colorboxXhrError,
      imgError: ColorboxLocal.colorboxImgError,
      iframe: ColorboxLocal.colorboxIframe,
      inline: ColorboxLocal.colorboxInline,
      width: ColorboxLocal.colorboxWidth,
      height: ColorboxLocal.colorboxHeight,
      initialWidth: ColorboxLocal.colorboxInitialWidth,
      initialHeight: ColorboxLocal.colorboxInitialHeight,
      maxWidth: ColorboxLocal.colorboxMaxWidth,
      maxHeight: ColorboxLocal.colorboxMaxHeight,
      slideshow: ColorboxLocal.colorboxSlideshow,
      slideshowSpeed: parseInt(ColorboxLocal.colorboxSlideshowSpeed, 10) || 2500,
      slideshowAuto: ColorboxLocal.colorboxSlideshowAuto,
      slideshowStart: ColorboxLocal.colorboxSlideshowStart,
      slideshowStop: ColorboxLocal.colorboxSlideshowStop
    });
  }

  /**
   * Ajoute l'effet de survol zoom sur les liens d'images.
   */
  function colorboxAddZoomOverlayToImages($link, $image) {
    var $zoomHover = $('<span class="zoomHover" style="opacity: 0; margin: 0; padding: 0;"></span>');

    $link.css('float', $image.css('float'));
    $link.append($zoomHover);
    $link.addClass('zoomLink');

    $link.hover(
      function() {
        $zoomHover.stop().animate({ opacity: 0.8 }, 300);
        $image.stop().animate({ opacity: 0.6 }, 300);
      },
      function() {
        $zoomHover.stop().animate({ opacity: 0 }, 300);
        $image.stop().animate({ opacity: 1 }, 300);
      }
    );
  }

  /**
   * Valeurs de repli si jQueryColorboxSettingsArray n'est pas défini.
   */
  function getColorboxConfigDefaults() {
    return {
      colorboxInline: false,
      colorboxIframe: false,
      colorboxGroupId: '',
      colorboxTitle: '',
      colorboxWidth: false,
      colorboxHeight: false,
      colorboxMaxWidth: false,
      colorboxMaxHeight: false,
      colorboxSlideshow: false,
      colorboxSlideshowAuto: false,
      colorboxScalePhotos: true,
      colorboxPreloading: false,
      colorboxOverlayClose: false,
      colorboxLoop: false,
      colorboxEscKey: true,
      colorboxArrowKey: true,
      colorboxScrolling: false,
      colorboxOpacity: '0.85',
      colorboxTransition: 'elastic',
      colorboxSpeed: '350',
      colorboxSlideshowSpeed: '2500',
      colorboxClose: 'close',
      colorboxNext: 'next',
      colorboxPrevious: 'previous',
      colorboxSlideshowStart: 'start slideshow',
      colorboxSlideshowStop: 'stop slideshow',
      colorboxCurrent: '{current} of {total} images',
      colorboxXhrError: 'This content failed to load.',
      colorboxImgError: 'This image failed to load.',
      colorboxImageMaxWidth: false,
      colorboxImageMaxHeight: false,
      colorboxImageHeight: false,
      colorboxImageWidth: false,
      colorboxLinkHeight: false,
      colorboxLinkWidth: false,
      colorboxInitialHeight: 100,
      colorboxInitialWidth: 300,
      autoColorboxJavaScript: false,
      autoColorbox: false,
      autoColorboxGalleries: false,
      colorboxAddClassToLinks: false,
      useGoogleJQuery: false,
      addZoomOverlay: false
    };
  }

  /**
   * Recalcule les dimensions si l'écran est redimensionné ou tourné (smartphone).
   */
  function bindResponsiveResize() {
    var resizeTimer;

    $(window).on('resize orientationchange', function() {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function() {
        if ($('#cboxOverlay').is(':visible') && $.colorbox && typeof $.colorbox.resize === 'function') {
          var dims = getResponsiveMaxDimensions(
            window.jQueryColorboxSettingsArray.colorboxImageMaxWidth,
            window.jQueryColorboxSettingsArray.colorboxImageMaxHeight
          );
          $.colorbox.resize({
            maxWidth: dims.maxWidth,
            maxHeight: dims.maxHeight
          });
        }
      }, 200);
    });
  }

  $(function() {
    if (typeof window.jQueryColorboxSettingsArray !== 'object') {
      window.jQueryColorboxSettingsArray = getColorboxConfigDefaults();
    }

    if (window.jQueryColorboxSettingsArray.autoColorboxJavaScript === 'true') {
      colorboxAddManualClass();
    }

    if (window.jQueryColorboxSettingsArray.colorboxAddClassToLinks === 'true') {
      colorboxAddClassToLinks();
    }

    colorboxSelector();
    bindResponsiveResize();
  });
})(jQuery);