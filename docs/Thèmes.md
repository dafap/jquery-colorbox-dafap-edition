# 🎨 Documentation des thèmes CSS de jQuery Colorbox Dafap Edition

Ce document synthétise les caractéristiques visuelles, techniques et fonctionnelles des **11 thèmes CSS** examinés (`colorbox.css`), en mettant en évidence leurs familles de parenté, leurs différences clés et les points de vigilance pour la maintenance.

---

## 🧭 Vue d'ensemble et familles de thèmes

Les 11 feuilles de style se répartissent en **5 familles distinctes** selon leur mise en page, le traitement des bordures/coins, l'emplacement des contrôles et le type d'assets graphiques mobilisés :

| Famille | Fichiers concernés | Ambiance & Cadre | Contrôles & Titre | Ressources graphiques principales |
| :--- | :--- | :--- | :--- | :--- |
| **Famille A : Cadre classique 21px (`-100` / `-129`)** | `theme6/colorbox.css`, `theme8/colorbox.css` | Overlay motif (`overlay.png`), bordures 21px, fond blanc `#fff` | Boutons 25×25px en bas, titre/compteur en bas | `overlay.png`, `controls.png`, `border.png`, `loading.gif`, `loading_background.png` |
| **Famille B : Cadre 21px décalé (`-101` / `-130`)** | `theme1/colorbox.css`, `theme7/colorbox.css` | Identique à la Famille A, avec décalage X de 1px sur les coins | Identique à la Famille A | Mêmes assets que la Famille A |
| **Famille C : Cadre sombre / contrôles en haut** | `theme2/colorbox.css`, `theme3/colorbox.css` | `theme2/colorbox` : overlay clair `#fff`, boîte noire `#000` ; `theme3/colorbox` : overlay noir `#000`, contenu blanc `#fff` dans cadre noir | Contrôles en haut ou latéraux (`theme3/colorbox`), gestion diaporama marquée | `controls.png`, `loading.gif` (+ `loading_background.png` sur `theme2/colorbox`) |
| **Famille D : Bordures 25px & boutons textuels** | `theme4/colorbox.css`, `theme9/colorbox.css` | Overlay blanc `#fff`, bordures 25px, fond blanc `#fff` | Boutons textuels (`#444`) en bas, compteur décalé (`left:100px`) | `border1.png`, `border2.png`, `loading.gif` |
| **Famille E : Cadre 14px / bas 43px** | `theme5/colorbox.css` | Overlay noir `#000`, boîte blanche `#fff`, bordures 14px, bas 43px | Boutons 23×23px et titre/compteur gras sous la fenêtre (`bottom:-25px` / `-29px`) | `controls.png`, `border.png`, `loading.gif`, `loading_background.png` |
| **Famille F : Coins arrondis & ombres** | `theme10/colorbox.css`, `theme11/colorbox.css` | Overlay noir `#000`, contenu arrondi (`border-radius:10px`), pastilles à `box-shadow` | Flèches latérales (`prev.png`/`next.png`) & fermeture (`close.png`) à opacité variable (`0.5` → `1`) | `prev.png`, `next.png`, `close.png`, `loading.gif` |

---

## 🔍 Analyse détaillée par famille et par thème

### 1. Famille A : `theme6/colorbox.css` et `theme8/colorbox.css`
* **Ressemblances :** Les `colorbox.css` partagent la même architecture et les mêmes valeurs de sprites.
* **Traits caractéristiques :**
  * Arrière-plan de modale : `#cboxOverlay` utilise `url(images/overlay.png) repeat 0 0`.
  * Cadre périphérique de **21px** : coins (`#cboxTopLeft`, `#cboxTopRight`, `#cboxBottomLeft`, `#cboxBottomRight`) de `21px × 21px` sur `images/controls.png` aux positions X `-100px` (gauche) et `-129px` (droite), et Y `0` (haut) et `-29px` (bas).
  * Bords centraux : côtés gauche/droite de `21px` de large (`images/controls.png` répété en Y), haut/bas de `21px` de haut (`images/border.png` répété en X aux offsets `0 0` et `0 -29px`).
  * Zone de contenu : `#cboxContent` blanc (`#fff`) avec `overflow: hidden`, `#cboxLoadedContent` avec `margin-bottom: 28px` pour réserver la barre inférieure.
  * Contrôles : `#cboxPrevious`, `#cboxNext` et `#cboxClose` de `25px × 25px` alignés en bas (`bottom: 0`), texte masqué par `text-indent: -9999px`, `#cboxSlideshow` en bleu `#0092ef` (`bottom: 4px; right: 30px`).

### 2. Famille B : `theme1/colorbox.css` et `theme7/colorbox.css`
* **Ressemblances :** Les `colorbox.css` sont des variantes quasi identiques entre elles et très proches de la Famille A.
* **Différences avec la Famille A :**
  * **Offsets horizontaux des coins** sur `images/controls.png` : `-101px` à gauche (au lieu de `-100px`) et `-130px` à droite (au lieu de `-129px`).
  * **Historique IE7/IE8 :** Dans leur version brute d'origine, ils incluaient le correctif `.cboxIE` (`filter: progid:DXImageTransform.Microsoft.gradient(...)`) pour la transparence alpha des PNG.

### 3. Famille C : `theme2/colorbox.css` et `theme3/colorbox.css`
Bien que regroupés ici pour leur placement des métadonnées/contrôles en partie supérieure ou latérale, **ces `colorbox.css` diffèrent fortement** dans leur contraste et leur ergonomie :

* **`theme2/colorbox.css` :**
  * **Contraste inversé du cadre :** overlay blanc (`#cboxOverlay{background:#fff}`), mais `#cboxContent` et `#cboxLoadedContent` sur fond noir (`#000`) avec `margin-top: 32px` et `overflow: visible`.
  * **Barre supérieure compacte :** `#cboxTitle` en haut à gauche (`top: -22px; left: 0; color: #000`), `#cboxCurrent` masqué (`text-indent: -9999px; right: 205px`), et boutons de `20px × 20px` (`top: -20px`) basés sur `images/controls.png`.
  * **Diaporama explicite :** règles dédiées `.cboxSlideshow_on` et `.cboxSlideshow_off` ajustant le décalage horizontal de `#cboxPrevious` (`right: 66px`) et l'icône de `#cboxSlideshow` (`right: 44px`).

* **`theme3/colorbox.css` :**
  * **Ambiance sombre classique :** `#cboxOverlay` noir (`#000`), `#cboxContent` noir (`margin-top: 20px`), mais `#cboxLoadedContent` sur fond blanc (`#fff`) encadré par `border: 5px solid #000`.
  * **Métadonnées en haut :** `#cboxTitle` (`left: 0`) et `#cboxCurrent` (`right: 0`) à `top: -20px` en gris clair (`#ccc`), `#cboxSlideshow` à `top: -20px; right: 90px` en blanc (`#fff`).
  * **Navigation latérale et fermeture en coin :** `#cboxPrevious` et `#cboxNext` sont centrés verticalement sur les bords (`top: 50%; margin-top: -32px; width: 28px; height: 65px`), tandis que `#cboxClose` (`38px × 19px`) est placé en haut à droite (`top: 5px; right: 5px`).

### 4. Famille D : `theme4/colorbox.css` et `theme9/colorbox.css`
* **Ressemblances :** Ces `colorbox.css` partagent exactement la même structure visuelle et fonctionnelle.
* **Traits caractéristiques :**
  * **Fond clair uniforme :** `#cboxOverlay` et `#cboxContent` en blanc (`#fff`).
  * **Bordures de 25px à double sprite :** les 6 zones hautes/basses (`#cboxTopLeft`, `#cboxTopCenter`, `#cboxTopRight`, `#cboxBottomLeft`, `#cboxBottomCenter`, `#cboxBottomRight`) de `25px` utilisent `images/border1.png`, tandis que les côtés (`#cboxMiddleLeft`, `#cboxMiddleRight`) de `25px` de large utilisent `images/border2.png`.
  * **Boutons textuels (sans sprite d'icône) :** contrairement aux autres thèmes à sprites, `#cboxPrevious`, `#cboxNext`, `#cboxSlideshow` et `#cboxClose` affichent du texte en `color: #444` en bas (`bottom: 0`) sur des positions horizontales distinctes (`left: 0`, `left: 63px`, `right: 42px`, `right: 0`), avec `#cboxCurrent` décalé à `left: 100px`.

### 5. Famille E : `theme5/colorbox.css`
* **Traits caractéristiques :**
  * **Contraste mixte et bordures asymétriques :** overlay noir (`#000`) et boîte blanche (`#fff`) avec `overflow: visible` sur `#cboxContent`. Les côtés et le haut font **14px**, mais la bande inférieure (`#cboxBottomLeft`, `#cboxBottomCenter`, `#cboxBottomRight`) atteint **43px** de haut.
  * **Titres en gras sous la boîte :** `#cboxTitle` et `#cboxCurrent` sont placés sous le cadre (`bottom: -25px`) en `font-weight: bold` et `color: #7C7C7C`.
  * **Boutons de 23px sous la boîte :** `#cboxPrevious`, `#cboxNext`, `#cboxClose` et `#cboxSlideshow` (`23px × 23px`) sont alignés à `bottom: -29px` sur `images/controls.png` avec gestion complète des états `.cboxSlideshow_on` et `.cboxSlideshow_off`.

### 6. Famille F : `theme10/colorbox.css` et `teme11/colorbox.css`
* **Ressemblances :** `theme10/colorbox.css` et `theme11/colorbox.css` reposent sur le même langage visuel moderne (coins arrondis et ombres).
* **Traits caractéristiques :**
  * **Coins arrondis et ombres portées :** `#cboxLoadedContent` et `#cboxLoadingOverlay` ont `border-radius: 10px` ; `#cboxTitle`, `#cboxCurrent` et `#cboxSlideshow` adoptent l'aspect de pastilles arrondies (`border-radius: 10px`) avec `box-shadow: 1px 1px 3px #888`.
  * **Contrôles dédiés avec effet d'opacité :** au lieu d'un sprite de bordure complet, ils utilisent `images/prev.png` (`64px × 32px`, `top: 36%; margin-top: -32px`), `images/next.png` (`64px × 32px`) et `images/close.png` (`22px × 22px`, `bottom: 10px; right: 10px`), passant de `opacity: 0.5` au repos à `opacity: 1` au survol ou au focus.
  * **Différence notable de maturité :** `theme11/colorbox.css` intègre déjà le nettoyage des préfixes/filtres legacy, la transition d'opacité (`transition: opacity 0.2s ease`), `overflow-wrap: break-word` et le style `:focus-visible` ; veillez toutefois à placer le bloc de reset (`margin: 0`) **avant** les règles individuelles des boutons afin de ne pas écraser `margin-top: -32px` sur `#cboxPrevious` et `#cboxNext`.

---

## 🛠️ Synthèse des points d'attention lors de la modernisation

1. **Dépendances aux images locales (`images/...`) :** Chaque thème attend un jeu précis d'images dans son dossier `images/` (`controls.png`, `border.png`, `border1.png`, `border2.png`, `overlay.png`, `loading.gif`, `loading_background.png`, `prev.png`, `next.png`, `close.png`). Modifier les dimensions ou offsets d'un thème sans vérifier son image casserait l'alignement.
2. **Suppression du code legacy :** Les anciens fichiers comportaient `-moz-`/`-webkit-` (`box-sizing`, `border-radius`, `box-shadow`, `-webkit-overflow-scrolling`), `-ms-interpolation-mode: bicubic`, `filter: alpha(opacity=...)` et les blocs `.cboxIE` (`filter: progid:DXImageTransform.Microsoft.gradient(...)`)[cite: 2, 3, 4, 5, 6, 7, 8, 9, 10, 11].
3. **Accessibilité clavier :** Remplacer la suppression systématique de `outline` par `#colorbox button:focus-visible` et renforcer les gris faibles (comme `#949494` ou `#999` sur fond clair) permet d'améliorer la lisibilité sans altérer l'esthétique globale.