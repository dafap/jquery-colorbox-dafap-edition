# Note Technique : `assets/js/jquery-colorbox.js`

## 🎯 Rôle et Architecture Générale

Le fichier `jquery-colorbox.js` (version minifiée : `jquery-colorbox.min.js`) est le moteur principal de la fenêtre modale (lightbox / diaporama), basé sur **Colorbox 1.6.4** et modernisé pour fonctionner de manière fiable avec **jQuery 3.x / 4.x** et les navigateurs mobiles récents.

Il gère :
- La construction et l'injection du DOM de la modale (`#colorbox` et `#cboxOverlay`).
- Le chargement asynchrone des contenus (images, galeries/diaporamas, contenus HTML inline, requêtes AJAX et iFrames).
- Le positionnement centré dans le viewport, les animations de transition et l'accessibilité clavier (piégeage du focus, touches Échap et flèches).

---

## 1. Modernisation Technique (Compatibilité jQuery 3+ & Navigateurs Modernes)

Par rapport à la version historique 1.6.4, les adaptations suivantes ont été apportées pour faciliter la maintenance et éviter les avertissements ou ruptures avec les versions récentes de WordPress :

| Zone modifiée | Ancienne implémentation | Implémentation modernisée | Raison |
| :--- | :--- | :--- | :--- |
| Détection de fonction | `$.isFunction(val)` | Helper interne `isFn(val)` (`typeof val === 'function'`) | `$.isFunction` est déprécié depuis jQuery 3.3 et supprimé en jQuery 4.0. |
| Gestion des événements | `.bind()` / `.unbind()` / `.live()` | `.on()` / `.off()` uniquement | Uniformisation sur l'API moderne de jQuery et suppression du code mort pour jQuery < 1.7. |
| Détection IE8 | `$.support.opacity === false` et `removeAttribute('filter')` | Supprimé | `$.support.opacity` a été retiré dès jQuery 1.9 et générait une erreur sur `$.support` undefined. |
| Masquage du bouton fermer | `$close.appendTo('<div/>')` | `$close.detach()` | Détachement propre du DOM sans création d'élément jetable. |
| Attributs d'iFrame | `iframe.frameBorder = 0`, `allowTransparency` | Supprimés (gérés en CSS) | Attributs HTML4 obsolètes. |
| Focus et déclencheurs | `.focus()`, `.click()` | `.trigger('focus')`, `.trigger('click')` | Syntaxe recommandée par jQuery 3+. |

---

## 2. Cycle de Vie d'une Ouverture (`launch` → `load` → `prep` → `position`)

1. **`appendHTML()` :** Construit une seule fois l'arborescence `#cboxOverlay` et `#colorbox` (grille de structure, conteneur `#cboxLoadedContent`, barre de titre, compteur, boutons Précédent/Suivant/Diaporama/Fermer) et l'attache au `<body>`.
2. **`launch(element)` :**
   - Instancie l'objet `Settings` qui fusionne par ordre de priorité : attributs HTML `data-cbox-*`, options passées à `.colorbox({...})`, et valeurs par défaut (`defaults`).
   - Identifie les éléments appartenant au même groupe `rel` via `getRelated()`.
   - Affiche l'overlay, active le piégeage du focus (`trapFocus`) pour l'accessibilité clavier, et déclenche l'événement `cbox_open`.
3. **`load()` :**
   - Calcule les contraintes de dimensions (`settings.w`, `settings.h`, `settings.mw`, `settings.mh`).
   - Détermine le type de contenu (`inline`, `iframe`, `html`, image via `isImage()`, ou chargement AJAX via `.load()`).
   - Pour les images, crée un objet `Image`, gère l'option Retina (`devicePixelRatio`), met l'image à l'échelle si `scalePhotos` est actif, et attache le clic sur la photo pour passer à l'image suivante.
4. **`prep(object)` & `position(speed, callback)` :**
   - Injecte le contenu prêt dans `#cboxLoadedContent`, met à jour les contrôles de navigation (compteur `{current} of {total}`, préchargement des images `index - 1` et `index + 1`, démarrage éventuel du diaporama `slideshow()`).
   - Centre la fenêtre dans le viewport (`window.innerWidth` / `winheight()`) en tenant compte du défilement ou du mode `fixed`.

---

## 3. Amélioration du Redimensionnement Dynamique (`publicMethod.resize`)

La méthode publique `$.colorbox.resize(options)` a été enrichie pour supporter directement les options `maxWidth` et `maxHeight` envoyées par `jquery-colorbox-wrapper.js` lors d'un changement d'orientation sur smartphone :

- Recalcule `settings.mw` et `settings.mh` à partir des nouvelles bornes `maxWidth` / `maxHeight`.
- Si une photo est affichée et que `scalePhotos` est actif, recalcule ses dimensions (`photo.style.width` et `photo.style.height`) en conservant strictement son ratio d'aspect naturel (`naturalWidth / naturalHeight`).
- Met à jour `settings.w` et `settings.h`, puis rappelle `publicMethod.position()` pour recentrer immédiatement la modale à l'écran.

---

## 4. API Publique et Événements

- **Méthodes publiques :** `$.colorbox.next()`, `$.colorbox.prev()`, `$.colorbox.close()`, `$.colorbox.resize()`, `$.colorbox.position()`, `$.colorbox.element()`, `$.colorbox.remove()`.
- **Événements déclenchés sur `document` :** `cbox_open`, `cbox_load`, `cbox_complete`, `cbox_cleanup`, `cbox_closed`, `cbox_purge`.