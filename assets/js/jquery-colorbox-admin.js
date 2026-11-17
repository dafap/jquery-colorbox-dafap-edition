/**
 * @package jQueryColorboxDaFAPEdition
 * @file assets/js/jquery-colorbox-admin.js
 * @description Script JavaScript pour la page d'administration de jQuery Colorbox.
 * @version 5.0.0
 * @since 5.0.0
 * @license GPL-2.0-or-later
 * Copyright (c) 2025 DaFAP (http://www.dafap.fr)
 * @author DaFAP
 * @fileoverview Ce script gère les interactions utilisateur dans la page d'administration du plugin jQuery Colorbox.
 * 
 * Note: Ce script dépend de jQuery et doit être chargé dans l'administration WordPress.
 * Les variables PHP sont transférées via wp_localize_script dans le fichier PHP principal du plugin.
 */

jQuery(document).ready(function ($) {
    // Utilisation des variables transférées via wp_localize_script
    const settingsName = jqcAdminData.settingsName;
    const pluginUrl = jqcAdminData.pluginUrl;

    /**
     * Gère la logique d'interdépendance entre un bouton radio et une zone de texte.
     * @param {string} baseName - Nom de base du réglage (ex: 'maxWidth').
     */
    function handleCustomInputLogic(baseName) {
        const radioSelector = `input[name='${settingsName}[${baseName}]']`;
        const customInputSelector = `input[name='${settingsName}[${baseName}Value]']`;
        const customRadioId = `#jquery-colorbox-${baseName}-custom-radio`;

        // 1. Vider la zone de texte personnalisée si un radio prédéfini est sélectionné
        $(radioSelector).on('click', function () {
            if ($(this).attr('id') !== customRadioId.substring(1)) {
                $(customInputSelector).val('');
            }
        });

        // 2. Cocher le radio "Personnalisé" si l'utilisateur entre du texte
        $(customInputSelector).on('focus', function () {
            $(customRadioId).prop('checked', true);
        });
    }

    // Appliquer la logique de gestion des entrées personnalisées à tous les champs répétitifs
    ['maxWidth', 'maxHeight', 'width', 'height', 'linkWidth', 'linkHeight'].forEach(handleCustomInputLogic);


    // --- Logique des Checkboxes à Exclusion Mutuelle et Dépendances ---

    const autoBox = `input[name='${settingsName}[autoColorbox]']`;
    const autoGalleries = '#jquery-colorbox-autoColorboxGalleries';
    const warningOff = '#jquery-colorbox-colorboxWarningOff';

    // Exclusion Mutuelle (AutoColorbox vs AutoColorboxGalleries)
    $(autoBox).on('click', function () {
        if ($(this).is(':checked')) {
            $(autoGalleries).prop('checked', false);
            // Dépendance 1: Désactiver l'avertissement si autoColorbox est actif
            $(warningOff).prop('checked', true);
        } else {
            // Dépendance 2: Activer l'avertissement si autoColorbox est désactivé
            $(warningOff).prop('checked', false);
        }
    });

    $(autoGalleries).on('click', function () {
        if ($(this).is(':checked')) {
            $(autoBox).prop('checked', false);
        }
    });


    // --- Logique de Prévisualisation du Thème ---
    $("#jquery-colorbox-theme").on('change', function () {
        const themeStr = $("option:selected", this).val();
        // Extrait le numéro du thème de la valeur sélectionnée (ex: 'theme2' -> '2')
        const match = themeStr.match(/\d+/i);
        const number = match ? match[0] : '-999'; // Valeur par défaut si pas de correspondance
        if (number !== '-999') {
            const imgTag = `<img src="${pluginUrl}assets/images/screenshot-${number}.jpg" />`;
            $("#jquery-colorbox-theme_screenshot_image").empty().html(imgTag).fadeIn();
        } else {
            // Option par défaut ou non-thème sélectionné
            $("#jquery-colorbox-theme_screenshot_image").empty().fadeOut();
        }
    });
});