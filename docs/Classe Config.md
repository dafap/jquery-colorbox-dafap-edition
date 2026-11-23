# 📘 Classe `Config`

Centralise les constantes et les tableaux de configuration par défaut du plugin **jQuery Colorbox Dafap Edition** pour WordPress.

* **Namespace :** `Dafap\JQueryColorbox`
* **Fichier :** `includes/Config.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken

---

## 📌 Constantes publiques

| Constante | Valeur | Rôle |
| :--- | :--- | :--- |
| `DEBUG_MODE` | `'false'` | Valeur par défaut de l'option `debugMode` (chaîne `'false'`). |
| `REPO_URL` | `'https://github.com/dafap/jquery-colorbox-dafap-edition'` | URL du dépôt GitHub du plugin. |
| `NAME_RAW` | `'jQuery Colorbox'` | Nom brut du plugin. |
| `SETTINGS_NAME` | `'jquery-colorbox_settings'` | Nom de l'option enregistrée dans la table `wp_options` de WordPress. |
| `SETTINGS_PAGE_SLUG` | `'jquery-colorbox-settings'` | Identifiant (*slug*) de la page de réglages dans l'administration. |
| `COLORBOX_VERSION` | `'1.6.4'` | Version de la bibliothèque JavaScript Colorbox embarquée. |

---

## 🔍 Méthodes statiques publiques

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `getDefaultSettings()` | `public static function getDefaultSettings(): array` | Retourne le tableau complet des réglages par défaut du plugin. |
| `getThemes()` | `public static function getThemes(): array` | Retourne la liste traduite des thèmes visuels disponibles (`theme1` à `theme11`). |
| `getUnits()` | `public static function getUnits(): array` | Retourne la liste traduite des unités de dimension (`px`, `em`, `rem`, `%`). |
| `getTransitions()` | `public static function getTransitions(): array` | Retourne la liste traduite des effets de transition (`elastic`, `fade`, `none`). |

---

## 🧠 Dépendances et constantes globales

* **Constantes globales :**
  * `JQC_VERSION` : version courante du plugin, injectée dans la clé `'jQueryColorboxVersion'` de `getDefaultSettings()`.
  * `JQC_TEXTDOMAIN` : domaine de traduction utilisé pour les libellés retournés par `getThemes()`, `getUnits()` et `getTransitions()`.

---

## ⚙️ Détails de fonctionnement des méthodes

### `getDefaultSettings(): array`
Retourne un tableau associatif couvrant l'ensemble des paramètres du plugin :
* **Version & thème :** `jQueryColorboxVersion` (`JQC_VERSION`), `colorboxTheme` (`'theme1'`).
* **Dimensions (largeur, hauteur, max, liens) :** `maxWidth`, `maxWidthValue`, `maxWidthUnit` (`'%'`), `maxHeight`, `maxHeightValue`, `maxHeightUnit` (`'%'`), `height`, `heightValue`, `heightUnit` (`'%'`), `width`, `widthValue`, `widthUnit` (`'%'`), `linkHeight`, `linkHeightValue`, `linkHeightUnit` (`'%'`), `linkWidth`, `linkWidthValue`, `linkWidthUnit` (`'%'`), `initialWidth` (`'300'`), `initialHeight` (`'100'`).
* **Comportement d'ouverture & galeries :** `autoColorbox` (`true`), `autoColorboxGalleries` (`false`), `autoColorboxJavaScript` (`false`), `colorboxAddClassToLinks` (`false`), `scalePhotos` (`false`), `displayScrollbar` (`false`), `draggable` (`false`), `preloading` (`false`), `overlayClose` (`false`), `disableLoop` (`false`), `disableKeys` (`false`).
* **Diaporama & transitions :** `slideshow` (`false`), `slideshowAuto` (`false`), `slideshowSpeed` (`'2500'`), `opacity` (`'0.85'`), `transition` (`'elastic'`), `speed` (`'350'`).
* **Affichage, chargement & divers :** `autoHideFlash` (`false`), `colorboxWarningOff` (`false`), `colorboxMetaLinkOff` (`false`), `javascriptInFooter` (`false`), `debugMode` (`self::DEBUG_MODE`), `addZoomOverlay` (`false`), `removeLinkFromMetaBox` (`true`).

### `getThemes(): array`
Retourne les clés `'theme1'` à `'theme11'` associées aux libellés traduits `'Theme #1'` à `'Theme #11'` via `__(\$label, JQC_TEXTDOMAIN)`.

### `getUnits(): array`
Retourne les clés `'px'`, `'em'`, `'rem'` et `'%'` associées aux libellés traduits `'Pixels'`, `'Ems'`, `'Rems'` et `'Pourcentage'` via `__($label, JQC_TEXTDOMAIN)`.  ### `getTransitions(): array` Retourne les clés `'elastic'`, `'fade'` et `'none'` associées aux libellés traduits `'Élasticité'`, `'Fondu'` et `'Aucun'` via `__($label, JQC_TEXTDOMAIN)`.

---

## 🛠️ Points de vigilance pour la maintenance

* **Hétérogénéité des types dans `getDefaultSettings()` :** Certaines options booléennes par défaut sont des booléens natifs (`autoColorbox => true`, `scalePhotos => false`), tandis que d'autres sont des chaînes (`maxWidth => 'false'`, `debugMode => self::DEBUG_MODE`). Assurez-vous que les consommateurs (`Frontend`, `Backend`, `JQueryColorbox`) gèrent ces deux formats de manière cohérente[cite: 2, 3, 4, 5].
* **Options héritées ou non exploitées :** Des clés comme `autoHideFlash` ou `draggable` figurent dans `getDefaultSettings()` ; vérifiez régulièrement si elles sont encore nécessaires dans l'interface ou le code front-end.
* **Thèmes déclarés :** `getThemes()` liste 11 thèmes (`theme1` à `theme11`) ; vérifiez que les dossiers correspondants existent bien dans `assets/themes/`.