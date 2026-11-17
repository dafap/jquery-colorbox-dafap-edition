# jQuery Colorbox Wrapper : `js/jquery-colorbox-wrapper.js`

## 📝 Note Technique : Analyse de la Stratégie des Scripts jQuery Colorbox

Ce script JavaScript gère l'initialisation et le comportement de la lightbox **Colorbox** dans un contexte WordPress/plugin, en adaptant dynamiquement les liens et les images de la page selon les réglages utilisateur.

La stratégie principale repose sur la **détection conditionnelle** des éléments à modifier (liens/images) et la **surcharge dynamique des options Colorbox** pour chaque élément.

---

### 1. Initialisation et Gestion des Dépendances (Document Ready Block)

L'exécution commence uniquement lorsque le DOM est prêt, enveloppée dans `jQuery(document).ready()`.

* **Sécurité du `console` :** La fonction `emulateConsoleForIE()` est immédiatement appelée pour **empêcher les erreurs JavaScript dans les anciennes versions d'Internet Explorer** (IE 9 et inférieures) qui ne définissent pas nativement l'objet `console`. C'est une mesure de compatibilité robuste.
* **Chargement de la Configuration :** Le script vérifie l'existence de l'objet global `jQueryColorboxSettingsArray`.
    * Si cet objet (normalement injecté par `wp_localize_script` côté PHP) n'est pas un objet valide (`typeof jQueryColorboxSettingsArray !== 'object'`), les **valeurs par défaut** sont chargées via `getColorboxConfigDefaults()`. Cette stratégie garantit que le script ne plante jamais, même en cas d'échec de l'injection PHP.
* **Exécution Conditionnelle :** Plusieurs fonctions sont exécutées uniquement si les réglages utilisateur le permettent (par exemple, si `jQueryColorboxSettingsArray.autoColorboxJavaScript` est `true`).

---

### 2. Adaptation du Contenu (Ajout de Classes)

Le script modifie le DOM avant l'appel à Colorbox pour garantir que les sélecteurs fonctionnent :

| Fonction | Condition d'Exécution | Rôle |
| :--- | :--- | :--- |
| `colorboxAddManualClass()` | Si `autoColorboxJavaScript` est `true` | Ajoute la classe `colorbox-manual` à **toutes les balises `<img>`** qui n'ont pas déjà une classe liée à Colorbox. Cela permet l'application automatique de Colorbox aux images simples. |
| `colorboxAddClassToLinks()` | Si `colorboxAddClassToLinks` est `true` | **Ajoute la classe `colorbox-link`** aux balises `<a>` dont l'attribut `href` pointe vers un fichier image (`.jpg`, `.png`, etc.) et qui ne contiennent pas déjà le texte `img` dans leur corps. |

---

### 3. Gestion du Flash (Ancienne Compatibilité)

Les fonctions `colorboxHideFlash()` et `colorboxShowFlash()` gèrent un problème courant des anciennes technologies Flash (via les balises `<object>` et `<embed>`).

* **Stratégie :** Ces objets étaient souvent rendus par-dessus tout le contenu. Pour les masquer lors de l'ouverture de la lightbox, le script écoute l'événement `cbox_open` et règle leur style `visibility` à `"hidden"`.
* **Rétablissement :** Lors de la fermeture, il écoute `cbox_closed` pour rétablir `visibility` à `"visible"`.

---

### 4. Sélecteur Principal et Initialisation (colorboxSelector)

La fonction `colorboxSelector()` est le cœur de l'initialisation, divisée en deux passes :

1.  **Liens avec Image Intégrée (`colorboxImage`) :**
    * Cible les liens `<a>` qui **contiennent une image** ayant une classe Colorbox (`a:has(img[class*=colorbox-])`).
    * Pour chaque lien trouvé, elle crée une **copie locale des réglages** (`ColorboxLocal = jQuery.extend(true,{},jQueryColorboxSettingsArray)`) afin de pouvoir modifier les dimensions (largeur/hauteur) pour s'adapter spécifiquement au type "image".
    * Elle utilise les classes sur l'image ou le lien (ex: `colorbox-123` ou `colorbox-manual`) pour déterminer le `rel` (groupement) de la galerie. Si c'est `colorbox-manual`, `rel` est réglé sur `"nofollow"` (pas de groupement).

2.  **Liens Simples (`colorboxLink`) :**
    * Cible les liens ayant la classe ajoutée dynamiquement ou manuellement (`a[class*=colorbox-link]`).
    * **Analyse le `href` :** Si le lien pointe vers une image, il utilise les dimensions des images. Si ce n'est pas une image, il détermine s'il s'agit de **contenu interne** (via un fragment `#...`, réglant `colorboxInline = true`) ou d'une **page externe** (réglant `colorboxIframe = true`).

---

### 5. L'Appel Final (`colorboxWrapper`)

La fonction `colorboxWrapper()` est l'interface finale avec la bibliothèque Colorbox :

* **Correction des Booléens (Bug WP) :** Un bloc essentiel convertit les chaînes `"true"` et `"false"` en vrais booléens (`true`/`false`). Ceci est un contournement nécessaire pour le comportement de la fonction WordPress `wp_localize_script`, qui exporte les booléens PHP sous forme de chaînes JavaScript.
* **Application :** Elle appelle la méthode `jQuery(link).colorbox({...})`, injectant toutes les options de l'objet `ColorboxLocal` (transition, vitesse, dimensions, textes d'internationalisation, etc.) pour initialiser la lightbox sur l'élément.