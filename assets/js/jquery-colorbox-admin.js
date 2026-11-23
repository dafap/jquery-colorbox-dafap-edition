/**
 * @package jQueryColorboxDaFAPEdition
 * @file assets/js/jquery-colorbox-admin.js
 * @description Script JavaScript pour la page d'administration de jQuery Colorbox.
 * @version 1.1.0
 * @since 1.1.0
 * @license GPL-2.0-or-later
 * Copyright (c) 2025-2026 DaFAP (http://www.dafap.fr)
 * @author DAFAP
 * @fileoverview Ce script gère les interactions utilisateur dans la page d'administration du plugin jQuery Colorbox.
 *
 * Note: Ce script dépend de jQuery et doit être chargé dans l'administration WordPress.
 * Les variables PHP sont transférées via wp_localize_script dans le fichier PHP principal du plugin.
 */

jQuery(function ($) {
    'use strict';

    const adminData = window.jqcAdminData || {};
    const settingsName = adminData.settingsName || 'jqueryColorboxSettings';
    const pluginUrl = adminData.pluginUrl || '';

    /**
     * Échappe les caractères spéciaux pour une utilisation sûre dans un attribut HTML.
     * @param {string} str
     * @returns {string}
     */
    function escapeAttr(str) {
        return String(str).replace(/[&<>"']/g, function (char) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            };
            return map[char];
        });
    }

    /**
     * Gère la logique d'interdépendance entre un bouton radio et une zone de texte.
     * @param {string} baseName - Nom de base du réglage (ex: 'maxWidth').
     */
    function handleCustomInputLogic(baseName) {
        const radioSelector = `input[name="${settingsName}[${baseName}]"]`;
        const customInputSelector = `input[name="${settingsName}[${baseName}Value]"]`;
        const customRadioId = `jquery-colorbox-${baseName}-custom-radio`;

        // 1. Vider la zone de texte personnalisée si un radio prédéfini est sélectionné
        $(document).on('change', radioSelector, function () {
            if ($(this).attr('id') !== customRadioId) {
                $(customInputSelector).val('');
            }
        });

        // 2. Cocher le radio "Personnalisé" si l'utilisateur entre du texte
        $(document).on('focus input', customInputSelector, function () {
            $(`#${customRadioId}`).prop('checked', true);
        });
    }

    // Appliquer la logique de gestion des entrées personnalisées à tous les champs répétitifs
    ['maxWidth', 'maxHeight', 'width', 'height', 'linkWidth', 'linkHeight'].forEach(handleCustomInputLogic);

    // --- Logique des Checkboxes à Exclusion Mutuelle et Dépendances ---

    const autoBoxSelector = `input[name="${settingsName}[autoColorbox]"]`;
    const autoGalleriesSelector = '#jquery-colorbox-autoColorboxGalleries';
    const warningOffSelector = '#jquery-colorbox-colorboxWarningOff';

    // Exclusion mutuelle (autoColorbox vs autoColorboxGalleries) et dépendance sur colorboxWarningOff
    $(document).on('change', autoBoxSelector, function () {
        if ($(this).is(':checked')) {
            $(autoGalleriesSelector).prop('checked', false);
            $(warningOffSelector).prop('checked', true);
        } else {
            $(warningOffSelector).prop('checked', false);
        }
    });

    $(document).on('change', autoGalleriesSelector, function () {
        if ($(this).is(':checked')) {
            $(autoBoxSelector).prop('checked', false);
        }
    });

    // --- Logique de Prévisualisation du Thème ---
    $(document).on('change', '#jquery-colorbox-theme', function () {
        const themeStr = String($(this).val() || '');
        const match = themeStr.match(/\d+/);
        const $preview = $('#jquery-colorbox-theme_screenshot_image');

        if (match && pluginUrl) {
            const number = match[0];
            const safeSrc = escapeAttr(`${pluginUrl}assets/images/screenshot-${number}.jpg`);
            const safeAlt = escapeAttr(`Aperçu du thème ${number}`);
            const $img = $('<img>', {
                src: safeSrc,
                alt: safeAlt
            }).on('error', function () {
                $preview.empty().hide();
            });

            $preview.empty().append($img).fadeIn();
        } else {
            $preview.empty().fadeOut();
        }
    });
});