# Note Technique : `assets/js/jquery-colorbox-wrapper.js`

## 🎯 Rôle et Architecture Générale

Le fichier `jquery-colorbox-wrapper.js` (version minifiée : `jquery-colorbox-wrapper.min.js`) constitue la couche d'orchestration entre les réglages WordPress du plugin **jQuery Colorbox (DaFAP Edition)** et la bibliothèque `jquery-colorbox.js`.

Son rôle est de :
1. Récupérer la configuration injectée côté PHP via `wp_localize_script` (`window.jQueryColorboxSettingsArray`).
2. Préparer automatiquement les balises `<img>` et `<a>` du DOM selon les options actives.
3. Calculer dynamiquement les dimensions maximales (`maxWidth` / `maxHeight`) selon la taille réelle du viewport (`window.innerWidth` / `window.innerHeight`), afin d'éviter toute troncature sur smartphone.
4. Initialiser Colorbox sur chaque lien éligible et réajuster la modale en temps réel lors d'une rotation d'écran.

L'ensemble du code est encapsulé dans une IIFE `(function($) { 'use strict'; ... })(jQuery);` afin d'éviter toute pollution de l'espace de noms global.

---

## 1. Initialisation et Gestion des Dépendances

L'exécution démarre lorsque le DOM est prêt via `$(function() { ... })` :

- **Chargement sécurisé de la configuration :** Le script vérifie que `window.jQueryColorboxSettingsArray` est bien un objet. À défaut, il charge un jeu de valeurs de repli via `getColorboxConfigDefaults()`, garantissant que le script ne lève jamais d'erreur bloquante même si l'injection PHP échoue.
- **Préparation conditionnelle du DOM :**
  - Si `autoColorboxJavaScript === 'true'`, `colorboxAddManualClass()` marque les images non encore configurées.
  - Si `colorboxAddClassToLinks === 'true'`, `colorboxAddClassToLinks()` marque les liens directs vers des images.
- **Lancement et écoute responsive :** `colorboxSelector()` attache Colorbox aux éléments ciblés, puis `bindResponsiveResize()` active la surveillance du redimensionnement et de l'orientation de l'écran.

> **Nettoyage effectué :** Les anciennes routines héritées d'Internet Explorer 9 (`emulateConsoleForIE`) et de la gestion d'Adobe Flash (`colorboxHideFlash` / `colorboxShowFlash`) ont été supprimées.

---

## 2. Préparation Automatique du Contenu (DOM)

| Fonction | Condition d'exécution | Rôle technique |
| :--- | :--- | :--- |
| `colorboxAddManualClass()` | `autoColorboxJavaScript === 'true'` | Parcourt les balises `<img>` et ajoute la classe `colorbox-manual` à celles qui ne possèdent pas encore de classe contenant `colorbox`. |
| `colorboxAddClassToLinks()` | `colorboxAddClassToLinks === 'true'` | Parcourt les balises `<a>` ne contenant pas d'enfant `<img>` (`$link.find('img').length === 0`) et dont le `href` correspond à une extension image (`.jpg`, `.jpeg`, `.gif`, `.png`, `.bmp`, `.webp`), puis leur ajoute la classe `colorbox-link`. |

---

## 3. Adaptation Responsive et Rotation d'Écran

Pour éviter que le diaporama ne déborde sur la droite sur mobile, le script ne se fie plus uniquement à des dimensions fixes configurées pour ordinateur :

- **`getResponsiveMaxDimensions(configuredMaxWidth, configuredMaxHeight)` :**
  - Lit la largeur (`window.innerWidth`) et la hauteur (`window.innerHeight`) réelles de la fenêtre visible.
  - Applique un ratio de sécurité adapté au format d'écran :
    - **Petit écran (`< 768 px`) :** `92 %` de la largeur et `82 %` de la hauteur du viewport.
    - **Écran moyen / grand (`>= 768 px`) :** `90 %` de la largeur et `88 %` de la hauteur du viewport.
  - Si une valeur maximale en pixels est définie dans les réglages WordPress, elle est bornée via `Math.min(...)` pour ne jamais dépasser la capacité du viewport mobile.
- **`bindResponsiveResize()` :**
  - Écoute les événements `resize` et `orientationchange` sur `window` avec un anti-rebond (*debounce*) de `200 ms`.
  - Si la modale Colorbox est ouverte (`$('#cboxOverlay').is(':visible')`), elle recalcule les dimensions et appelle `$.colorbox.resize({ maxWidth, maxHeight })` pour adapter immédiatement l'image et le cadre après une rotation de 90°.

---

## 4. Sélection des Cibles (`colorboxSelector`)

La détection s'effectue en deux passes distinctes :

1. **Liens contenant une image (`colorboxImage`) :**
   - Sélecteur : `a:has(img[class*=colorbox-]):not(.colorbox-off)`.
   - Clone la configuration globale dans `ColorboxLocal` (`$.extend(true, {}, ...)`), applique les dimensions responsive, puis détermine l'identifiant de galerie (`rel`) à partir de la classe `colorbox-[0-9]+` ou `colorbox-manual` (converti en `'nofollow'` pour désactiver le groupement).
   - Récupère le titre de la légende depuis l'attribut `title` de l'image, ou à défaut depuis son attribut `alt`.
   - Ajoute l'overlay de survol (`colorboxAddZoomOverlayToImages`) si `addZoomOverlay === 'true'`.

2. **Liens simples (`colorboxLink`) :**
   - Sélecteur : `a[class*=colorbox-link]`.
   - Extrait le groupe éventuel (`colorbox-link-[0-9]+`) ou applique `'nofollow'`.
   - Si le `href` pointe vers une image, applique les dimensions responsive d'image.
   - Sinon, bascule automatiquement en mode **contenu interne** (`colorboxInline = true` si le `href` commence par `#`) ou en mode **iFrame** (`colorboxIframe = true` pour une URL externe).

---

## 5. Normalisation et Appel Final (`colorboxWrapper`)

- **Conversion des booléens WordPress :** La fonction `wp_localize_script()` sérialise les booléens PHP en chaînes de caractères (`"true"` / `"false"`). `colorboxWrapper()` reconvertit systématiquement ces chaînes en vrais booléens JavaScript avant l'appel à la bibliothèque.
- **Mise à l'échelle garantie :** `scalePhotos` est activé par défaut (`true`) afin que Colorbox redimensionne proportionnellement les photos dans les limites calculées.
- **Initialisation :** Appelle `$(link).colorbox({...})` avec l'ensemble des paramètres de transition, vitesse, navigation, accessibilité, internationalisation et diaporama.