# Note Technique : `assets/js/jquery-colorbox-admin.js`

## 🎯 Rôle et Architecture Générale

Le fichier `jquery-colorbox-admin.js` (version minifiée : `jquery-colorbox-admin.min.js`) gère l'interactivité de la page de réglages du plugin **jQuery Colorbox (DaFAP Edition)** dans l'administration WordPress.

Il assure :
1. La cohérence entre les boutons radio prédéfinis et les champs de saisie personnalisés (dimensions).
2. Les règles d'exclusion mutuelle et de dépendance entre les modes d'automatisation du plugin.
3. L'aperçu dynamique des captures d'écran lors de la sélection d'un thème Colorbox.

Le script est encapsulé dans `jQuery(function ($) { 'use strict'; ... });` et utilise la délégation d'événements (`$(document).on(...)`).

---

## 1. Réception Sécurisée des Données PHP (`wp_localize_script`)

Les paramètres du contexte WordPress sont transmis via l'objet global `window.jqcAdminData` :

- `settingsName` : nom du tableau d'options enregistré via la Settings API de WordPress (repli sécurisé sur `'jqueryColorboxSettings'` si absent).
- `pluginUrl` : URL racine du répertoire du plugin permettant de charger les images d'aperçu des thèmes.

> **Sécurisation :** L'accès à `window.jqcAdminData` passe par un objet de repli (`window.jqcAdminData || {}`) afin d'éviter toute exception JavaScript si le script est chargé sur un écran sans localisation.

---

## 2. Gestion des Champs de Dimensions Personnalisées (`handleCustomInputLogic`)

Pour chacun des réglages de dimensions (`maxWidth`, `maxHeight`, `width`, `height`, `linkWidth`, `linkHeight`), l'interface propose des boutons radio de valeurs prédéfinies ainsi qu'une option **Personnalisé** accompagnée d'un champ texte (`${settingsName}[${baseName}Value]`) :

| Action utilisateur | Événement écouté | Comportement appliqué |
| :--- | :--- | :--- |
| Sélection d'un bouton radio prédéfini | `change` sur `input[name="${settingsName}[${baseName}]"]` | Si le bouton sélectionné n'est pas `#jquery-colorbox-${baseName}-custom-radio`, le champ texte personnalisé est vidé automatiquement. |
| Focus ou saisie dans le champ texte personnalisé | `focus input` sur `input[name="${settingsName}[${baseName}Value]"]` | Coche automatiquement le bouton radio `#jquery-colorbox-${baseName}-custom-radio`. |

---

## 3. Règles d'Exclusion Mutuelle et Dépendances des Options

Les cases à cocher d'automatisation obéissent à une logique métier stricte pour éviter des configurations contradictoires :

- **Activation de `autoColorbox` :**
  - Décoche automatiquement `autoColorboxGalleries` (`#jquery-colorbox-autoColorboxGalleries`) afin d'éviter un double traitement.
  - Coche automatiquement `colorboxWarningOff` (`#jquery-colorbox-colorboxWarningOff`) pour masquer l'avertissement d'inactivité.
- **Désactivation de `autoColorbox` :**
  - Décoche `colorboxWarningOff` afin de réactiver l'alerte de configuration si nécessaire.
- **Activation de `autoColorboxGalleries` :**
  - Décoche automatiquement `autoColorbox` (exclusion mutuelle réciproque).

---

## 4. Prévisualisation Dynamique des Thèmes (`#jquery-colorbox-theme`)

Lors du changement de sélection dans la liste déroulante `#jquery-colorbox-theme` :

1. Le script extrait le numéro du thème via l'expression régulière `/\d+/` (ex. `'theme2'` → `'2'`).
2. Si un numéro valide est trouvé et que `pluginUrl` est défini :
   - Il construit une balise `<img>` sécurisée (`escapeAttr` sur les attributs `src` et `alt`) pointant vers `${pluginUrl}assets/images/screenshot-${number}.jpg`.
   - Il attache un gestionnaire `.on('error', ...)` qui masque proprement la zone d'aperçu si le fichier image correspondant est manquant.
   - Il affiche l'image avec une transition `.fadeIn()` dans `#jquery-colorbox-theme_screenshot_image`.
3. Si aucune correspondance numérique n'est trouvée (option par défaut ou thème personnalisé sans capture), le conteneur d'aperçu est vidé et masqué via `.fadeOut()`.