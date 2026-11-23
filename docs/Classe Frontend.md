# 📘 Classe `Frontend`

Gère le comportement public (*frontend*) du plugin **jQuery Colorbox Dafap Edition** pour WordPress : enfilage des styles et scripts, ajout automatique des classes de groupe Colorbox aux images et miniatures, rendu du lien meta et transmission de la configuration vers JavaScript.

* **Namespace :** `Dafap\JQueryColorbox\Classes`
* **Fichier :** `includes/Classes/Frontend.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken

---

## 🧩 Constructeur

```php
public function __construct(array $colorboxSettings)
```

Enregistre les réglages utilisateur dans `\$this->colorboxSettings` et rattache les hooks et filtres WordPress selon les options actives :

* `wp_meta` → `renderMetaLink()` (sauf si `removeLinkFromMetaBox` est évalué à `true`).
* `the_content` et `the_excerpt` (avec une priorité `100`) → `addColorboxGroupIdToImages()` lorsque `autoColorbox` est actif.
* `wp_get_attachment_image_attributes` → `wpPostThumbnailClassFilter()` lorsque `autoColorboxGalleries` ou `autoColorbox` est actif.
* `wp_enqueue_scripts` → `enqueueFrontendScripts()`.

---

## 🔍 Méthodes publiques

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `renderMetaLink()` | `public function renderMetaLink(): void` | Affiche le lien d'information du plugin dans le bloc *meta* du site public via le template `frontend/meta-link.phtml`. |
| `addColorboxGroupIdToImages()` | `public function addColorboxGroupIdToImages(string \$content): string` | Ajoute la classe `colorbox-<post_id>` aux balises `<img>` dans le contenu ou l'extrait (sans écraser une classe `colorbox-` déjà présente). |
| `wpPostThumbnailClassFilter()` | `public function wpPostThumbnailClassFilter(array \$attributes): array` | Ajoute la classe `colorbox-<post_id>` aux attributs HTML des miniatures de publications. |
| `enqueueFrontendScripts()` | `public function enqueueFrontendScripts(): void` | Coordonne l'enfilage de `jquery`, des feuilles de style, de la bibliothèque Colorbox, du wrapper et des variables localisées. |

---

## 🔧 Méthodes privées

| Méthode | Signature | Rôle |
| :--- | :--- | :--- |
| `addColorboxCSS()` | `private function addColorboxCSS(): void` | Enfile le thème CSS (`assets/themes/<theme>/colorbox.css`) et, si `addZoomOverlay` est actif, `assets/css/jquery-colorbox-zoom.css`. |
| `addColorboxJS()` | `private function addColorboxJS(): void` | Enfile `assets/js/jquery-colorbox.js` (en mode `debugMode`) ou `assets/js/jquery-colorbox.min.js` (handle `colorbox`, dépendant de `jquery`). |
| `addColorboxWrapperJS()` | `private function addColorboxWrapperJS(): void` | Enfile `assets/js/jquery-colorbox-wrapper.js` (en mode `debugMode`) ou `assets/js/jquery-colorbox-wrapper.min.js` (handle `colorbox-wrapper`, dépendant de `colorbox`). |
| `formatColorboxDimension()` | `private function formatColorboxDimension(string \$settingName): string` | Formate une dimension (`<Valeur><Unité>`, unité par défaut `%`) ou retourne `'false'` si l'option vaut `'false'`. |
| `addColorboxProperties()` | `private function addColorboxProperties(): void` | Injecte `jQueryColorboxSettingsArray` via `wp_localize_script('colorbox', ...)` avec les libellés traduits et réglages normalisés en chaînes `'true'`/`'false'`. |

---

## 🧠 Dépendances et constantes

* **Traits :**
  * `TemplateRenderTrait` : fournit `templateRender()` pour le rendu des gabarits `.phtml`.
  * `UtilsTrait` : fournit notamment `isTrue(string $optionName): bool` pour vérifier si une option booléenne de `$colorboxSettings` est activée.
* **Classe & constantes globales :**
  * `Dafap\JQueryColorbox\Config` : utilise `Config::REPO_URL` et `Config::COLORBOX_VERSION`.
  * Constantes globales : `JQC_TEXTDOMAIN`, `JQC_VERSION` et `JQC_URL`.

---

## ⚙️ Détails de fonctionnement des méthodes

### `renderMetaLink(): void`
Appelle `$this->templateRender('frontend/meta-link.phtml', [...])` en transmettant `pluginName` (`__('jQuery Colorbox', JQC_TEXTDOMAIN)`), `repoUrl` (`Config::REPO_URL`), `pluginVersion` (`JQC_VERSION`) et `textDomain` (`JQC_TEXTDOMAIN`).  ### `addColorboxGroupIdToImages(string $content): string`
* Récupère `$post` global et vérifie qu'il s'agit d'un objet valide ; sinon retourne `$content` tel quel.
* Construit la chaîne `' colorbox-' . $post->ID`. * Passe d'abord toutes les balises `<img>` possédant déjà un attribut `class` : si la classe contient déjà `colorbox-`, elle n'est pas modifiée ; sinon `' colorbox-' . $post->ID` est ajoutée à la fin de l'attribut `class`.
* Ajoute ensuite un attribut `class=" colorbox-<post_id>"` sur les balises `<img>` comportant `src` ou `href` qui n'ont pas été traitées par la première passe.

### `wpPostThumbnailClassFilter(array $attributes): array` * Vérifie l'existence de l'objet global `$post` ; retourne `$attributes` inchangé si absent ou non objet. * Concatène `' colorbox-' . $post->ID` à `$attributes['class']` (initialisé à `''` si absent).  ### `enqueueFrontendScripts(): void` Exécute séquentiellement : 1. `wp_enqueue_script('jquery')` ; 2. `$this->addColorboxCSS()` ;
3. `$this->addColorboxJS()` ; 4. `$this->addColorboxWrapperJS()` ;
5. `$this->addColorboxProperties()`.  ### `addColorboxCSS(): void` * Lit `$this->colorboxSettings['colorboxTheme']` (repli sur `'theme1'`).
* Enregistre et enfile le style `'colorbox-' . $theme` pointant vers `JQC_URL . 'assets/themes/' . $theme . '/colorbox.css'` avec la version `JQC_VERSION` et le média `'screen'`.
* Si `$this->isTrue('addZoomOverlay')` est vrai, enregistre et enfile `'colorbox-zoom-css'` (`JQC_URL . 'assets/css/jquery-colorbox-zoom.css'`, version `Config::COLORBOX_VERSION`).  ### `addColorboxJS(): void` * Sélectionne `assets/js/jquery-colorbox.js` si `debugMode` est vrai, sinon `assets/js/jquery-colorbox.min.js`. * Enfile le handle `'colorbox'` avec dépendance `['jquery']`, version `Config::COLORBOX_VERSION` et placement en pied de page selon `(bool) $this->colorboxSettings['javascriptInFooter']`.

### `addColorboxWrapperJS(): void`
* Sélectionne `assets/js/jquery-colorbox-wrapper.js` si `debugMode` est vrai, sinon `assets/js/jquery-colorbox-wrapper.min.js`.
* Enfile le handle `'colorbox-wrapper'` avec dépendance `['colorbox']`, version `JQC_VERSION` et placement en pied de page selon `(bool) $this->colorboxSettings['javascriptInFooter']`.  ### `formatColorboxDimension(string $settingName): string`
* Si `($this->colorboxSettings[$settingName] ?? 'false') === 'false'`, retourne `'false'`.
* Sinon, concatène `($this->colorboxSettings[$settingName . 'Value'] ?? '')` et `($this->colorboxSettings[$settingName . 'Unit'] ?? '%')`.

### `addColorboxProperties(): void`
Construit le tableau `$props` contenant la version (`jQueryColorboxVersion`), les chaînes traduisibles (`colorboxClose`, `colorboxNext`, `colorboxPrevious`, `colorboxSlideshowStart`, `colorboxSlideshowStop`, `colorboxCurrent`, `colorboxXhrError`, `colorboxImgError`), les options booléennes converties en `'true'`/`'false'` (`colorboxSlideshow`, `colorboxSlideshowAuto`, `colorboxScalePhotos`, `colorboxPreloading`, `colorboxOverlayClose`, `colorboxLoop`, `colorboxEscKey`, `colorboxArrowKey`, `colorboxScrolling`), les paramètres d'animation/affichage (`colorboxOpacity`, `colorboxTransition`, `colorboxSpeed`, `colorboxSlideshowSpeed`, `colorboxInitialHeight`, `colorboxInitialWidth`), les dimensions formatées (`colorboxImageMaxWidth`, `colorboxImageMaxHeight`, `colorboxImageHeight`, `colorboxImageWidth`, `colorboxLinkHeight`, `colorboxLinkWidth`) et les drapeaux d'activation (`autoColorboxJavaScript`, `autoHideFlash`, `autoColorbox`, `autoColorboxGalleries`, `addZoomOverlay`, `colorboxAddClassToLinks`), puis appelle `wp_localize_script('colorbox', 'jQueryColorboxSettingsArray', $props)`.

---

## 🛠️ Points de vigilance pour la maintenance

* **Accès à `$post` global :** `addColorboxGroupIdToImages()` et `wpPostThumbnailClassFilter()` dépendent de `$post->ID` ; hors d'un contexte de publication valide, elles retournent le contenu ou les attributs inchangés.
* **Expression régulière dans `addColorboxGroupIdToImages()` :** Le second `preg_replace()` insère `class=" colorbox-<id>"` sur les balises `<img>` contenant `src` ou `href` ; veillez à ce qu'une balise ne reçoive pas deux attributs `class` si sa structure HTML est atypique.
* **Clé `autoHideFlash` :** Toujours transmise dans `addColorboxProperties()`, alors que la gestion Flash a été retirée de `jquery-colorbox-wrapper.js` lors de sa modernisation.
* **Accès direct à `$this->colorboxSettings['javascriptInFooter']` :** Dans `addColorboxJS()` et `addColorboxWrapperJS()`, la clé est lue directement sans opérateur `??` ; assurez-vous qu'elle est toujours définie dans `$colorboxSettings`.