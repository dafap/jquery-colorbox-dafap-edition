# jQuery Colorbox Admin : `js/jquery-colorbox-admin.js`

## 📝 Note Technique : Analyse de la Stratégie du Script d'Administration

Le script `jquery-colorbox-admin.js` a pour objectif principal d'améliorer l'**expérience utilisateur (UX)** sur la page de réglages WordPress en ajoutant de l'**interactivité** et de la **logique front-end** pour prévenir les configurations invalides. Il se concentre sur la gestion des champs interdépendants et la prévisualisation visuelle.

---

### 1. Initialisation et Injection de Dépendances

Le script suit la bonne pratique de s'exécuter à la fin du chargement du DOM (`jQuery(document).ready(function ($) { ... });`).

* **Variables Locales :** Il récupère immédiatement les variables globales `settingsName` (confirmé précédemment comme `jquery-colorbox_settings`) et `pluginUrl` (le chemin d'accès à la racine du plugin) qui sont injectées par PHP via `wp_localize_script` (sous l'objet `jqcAdminData`).
* **Encapsulation :** L'utilisation de l'**IIFE** (Immediately Invoked Function Expression) avec le passage de `jQuery` en tant que `$` garantit que le code utilise l'alias `$` en toute sécurité, même en mode "no-conflict" de WordPress.

---

### 2. Gestion de la Logique des Dimensions Personnalisées

La fonction `handleCustomInputLogic(baseName)` est la stratégie la plus élégante et réutilisable du script. Elle gère l'interdépendance des réglages de dimension (largeur, hauteur, etc.) qui peuvent être soit prédéfinis (radio) soit personnalisés (texte).

* **Stratégie du `handleCustomInputLogic` :**
    1.  **Désactivation Mutuelle (Radio -> Texte) :** Si l'utilisateur clique sur un bouton radio **prédéfini** (non personnalisé), la zone de texte correspondante (`[baseName]Value`) est immédiatement **vidée** (`.val('')`). Ceci assure que seule la valeur sélectionnée est soumise.
    2.  **Activation Automatique (Texte -> Radio) :** Si l'utilisateur **clique (focus)** dans la zone de texte de la valeur personnalisée, le bouton radio "Personnalisé" (`#jquery-colorbox-maxWidth-custom-radio`, par exemple) est **automatiquement coché** (`.prop('checked', true)`).
* **Réutilisation :** La fonction est appliquée via `forEach` à six ensembles de champs (`maxWidth`, `maxHeight`, etc.), garantissant une cohérence et une maintenance minimale.

---

### 3. Logique des Checkboxes à Exclusion Mutuelle et Dépendances

Cette section applique des règles métier spécifiques au plugin, gérant les conflits potentiels de configuration.

* **Exclusion Mutuelle :**
    * Si l'utilisateur coche `autoColorbox` (appliquer Colorbox automatiquement à toutes les images), le script décoche immédiatement `autoColorboxGalleries` (appliquer Colorbox aux galeries seulement) pour éviter un comportement ambigu ou redondant.
* **Dépendance des Avertissements (`warningOff`) :**
    * Si `autoColorbox` est **coché**, le script coche **automatiquement** `colorboxWarningOff` (désactiver l'avertissement). L'hypothèse est que si le plugin est utilisé en mode automatique, l'avertissement d'inactivité devient inutile.
    * Inversement, si `autoColorbox` est **décoché**, le script décoche **automatiquement** `colorboxWarningOff` pour s'assurer que l'utilisateur voit l'avertissement standard de WordPress (géré par `registerAdminWarning` côté PHP) indiquant que la fonction principale est désactivée.

---

### 4. Prévisualisation du Thème

Ce bloc améliore l'UX en offrant un aperçu visuel instantané du thème sélectionné.

* **Détection du Changement :** Le script écoute l'événement `change` sur le sélecteur de thème (`#jquery-colorbox-theme`).
* **Extraction :** Il extrait le numéro du thème (`number`) de la valeur sélectionnée (ex: `theme2` donne `2`).
* **Affichage Dynamique :** Il construit dynamiquement une balise `<img>` pointant vers le chemin attendu du fichier de capture d'écran (ex: `[pluginUrl]assets/images/screenshot-2.jpg`) et l'insère dans l'élément d'affichage prévu (`#jquery-colorbox-theme_screenshot_image`), avec un effet d'apparition progressif (`.fadeIn()`).
* **Gestion de l'Absence :** Si une option par défaut ou non-thème est sélectionnée, l'image de prévisualisation est masquée (`.fadeOut()`).