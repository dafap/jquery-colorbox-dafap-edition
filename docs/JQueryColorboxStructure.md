# Plugin Jquery Colorbox Dafap Edition
## Structure du plugin `jquery-colorbox-dafap-edition`
```
📁 .vscode
    📄 launch.json
    📄 settings.json
📁 assets
    📁 css
        📄 jquery-colorbox-zoom.css
        📄 jquery-colorbox.css
    📁 images
        📄 ajax-loader.gif
        📄 screenshot-1.jpg
        📄 screenshot-10.jpg
        📄 screenshot-11.jpg
        📄 screenshot-2.jpg
        📄 screenshot-3.jpg
        📄 screenshot-4.jpg
        📄 screenshot-5.jpg
        📄 screenshot-6.jpg
        📄 screenshot-7.jpg
        📄 screenshot-8.jpg
        📄 screenshot-9.jpg
        📄 super-mario-bros-1985.jpg
        📄 zoom.png
    📁 js
        📄 jquery-colorbox-admin.js
        📄 jquery-colorbox-admin.min.js
        📄 jquery-colorbox-wrapper.min.js
        📄 jquery-colorbox-wrapper.js
        📄 jquery-colorbox.min.js
        📄 jquery-colorbox.js
    📁 themes
        📁 theme1
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📄 loading.gif
                📄 loading_background.png
                📄 overlay.png
        📁 theme10
            📄 colorbox.css
            📁 images
                📄 close.png
                📄 loading.gif
                📄 next.png
                📄 prev.png
        📁 theme11
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📁 ie6
                📄 loading.gif
                📄 loading_background.png
                📄 overlay.png
        📁 theme2
            📄 colorbox.css
            📁 images
                📄 controls.png
                📄 loading.gif
        📁 theme3
            📄 colorbox.css
            📁 images
                📄 controls.png
                📄 loading.gif
        📁 theme4
            📄 colorbox.css
            📁 images
                📄 border1.png
                📄 border2.png
                📁 ie6
                📄 loading.gif
        📁 theme5
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📄 loading.gif
                📄 loading_background.png
        📁 theme6
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📄 loading.gif
                📄 loading_background.png
                📄 overlay.png
        📁 theme7
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📁 ie6
                📄 loading.gif
                📄 loading_background.png
                📄 overlay.png
        📁 theme8
            📄 colorbox.css
            📁 images
                📄 border.png
                📄 controls.png
                📄 loading.gif
                📄 loading_background.png
                📄 overlay.png
        📁 theme9
            📄 colorbox.css
            📁 images
                📄 border1.png
                📄 border2.png
                📄 controls.png
                📁 ie6
                📄 loading.gif
📄 composer.json
📁 docs
📁 includes
    📁 Classes
        📄 class-backend.php
        📄 class-frontend.php
    📁 Traits
        📄 trait-template-render.php
        📄 trait-utils.php
    📄 autoloader.php
    📄 class-config.php
    📄 class-jquery-colorbox.php
📄 jquery-colorbox-dafap-edition.php
📁 languages
📁 templates
    📁 backend
        📄 settings-page.phtml
    📁 frontend
        📄 meta-link.phtml
📄 zzz_doc-public-methods.php
📄 zzz_scan-structure.php
📄 zzz_test-autoloader.php
📄 zzz_verify-namespace.php
```
Cette structure est générée par le script `scan-structure.php`.

## Logique du plugin
1. Le fichier principal `jquery-colorbox-dafap-edition` 
  - charge l'autoloader 
  - initialise la classe `Dafap\JQueryColorbox\JQueryColorbox`.
2. La classe `Dafap\JQueryColorbox\JQueryColorbox` est la classe principale (ou bootstrap) du plugin. Elle gère :
  - l'initialisation
  - la configuration
  - le chargement des composants spécifiques au backend (administration) et au frontend (partie publique).
  - le hook de désinstallation

## La classe `Dafap\JQueryColorbox\JQueryColorbox`
### Objectifs de la classe
La classe `JQueryColorbox` sert de point d'entrée central pour l'ensemble du plugin. Son rôle est de :
- Initialiser les réglages du plugin à partir de la base de données ou en utilisant les valeurs par défaut.
- Définir les listes de valeurs (thèmes, unités, transitions).
- Gérer la localisation (traductions) des chaînes de caractères.
- Charger la logique spécifique à l'administration (`Backend`) ou à la partie publique (`Frontend`) en fonction du contexte.
- Gérer le hook de désinstallation.
### Initialisation et Constructeur (`__construct`)
Le constructeur est la partie la plus critique, car il exécute toute la logique d'initialisation du plugin.

#### A. Vérifications et Hooks WordPress
* Il effectue une vérification de base (`!function_exists('plugins_url')`) pour s'assurer qu'il est bien exécuté dans un environnement WordPress.
* Il enregistre trois actions sur le hook `init` de WordPress, avec des priorités distinctes :
  - `jquery_colorbox_load_textdomain` (priorité 5) : Charge les fichiers de traduction.
  - `localize_labels` (priorité 10) : Localise les chaînes de thèmes, unités et transitions.
  - `init_backend` (priorité 15) : Charge la logique Backend ou Frontend.

#### B. Gestion des Réglages
La logique de chargement des réglages est robuste :
* Il récupère les réglages existants de la base de données avec `get_option(Config::getSettingsName())`.
* Il obtient les réglages par défaut via `Config::getDefaultSettings()`.
* Il valide les réglages de la base de données via `validateSettingsInDatabase()`. Cette validation est minimale (elle vérifie seulement la présence de la clé `'jQueryColorboxVersion'`).
* Il détermine les réglages finaux (`$this->colorboxSettings`) :
  - Si les réglages de la base de données sont valides, il fusionne les réglages utilisateur avec les réglages par défaut (`wp_parse_args`), s'assurant que les réglages par défaut servent de _fallback_ pour toute clé manquante.
  - S'ils ne sont pas valides, il utilise simplement les réglages par défaut.
* Si les réglages étaient invalides (`!$validSettings`), il met à jour la base de données avec les réglages par défaut.

#### C. Définition des Constantes
Il initialise les propriétés de la classe qui contiennent des listes de choix pour les utilisateurs :
- `$this->colorboxThemes = Config::getThemes();`
- `$this->colorboxUnits = Config::getUnits();`
- `$this->colorboxTransitions = Config::getTransitions();`
- `$this->dummyThemeNumberArray` est définie pour des besoins spécifiques (probablement de compatibilité ou d'extension comme indiqué dans le commentaire).

#### D. Désinstallation
- Il enregistre la méthode statique `uninstallJqueryColorbox` pour être exécutée lors de la désinstallation du plugin, s'il est désactivé et supprimé.

### Méthodes de Localisation et de Configuration
`jquery_colorbox_load_textdomain()`
- Utilise `load_plugin_textdomain` pour charger les fichiers de traduction (`.mo`, `.po`).
- Définit la constante globale `JQUERYCOLORBOX_NAME` avec le nom traduit du plugin, si elle n'est pas déjà définie.

`localize_labels()`
- C'est ici que les libellés des options sont traduits après le chargement du text domain.
- Il itère sur `$this->colorboxThemes` pour traduire chaque libellé.
- Il surcharge les propriétés `$this->colorboxUnits` et `$this->colorboxTransitions` avec des tableaux contenant les valeurs traduites.

### Chargement Conditionnel (`init_backend`)
Cette méthode est le cœur de la logique de chargement conditionnel du plugin. Elle s'exécute sur le hook `init` (priorité 15).

* Elle vérifie si l'utilisateur est dans l'interface d'administration de WordPress (`if (is_admin())`).
  - Si `true` (Backend) : Elle instancie la classe `Backend`. Cette classe sera responsable de l'affichage de la page de réglages, de la gestion des options, etc. Elle lui passe les réglages et les listes d'options nécessaires.
  - Si `false` (Frontend) : Elle instancie la classe `Frontend`. Cette classe sera responsable de l'enfilement des scripts et des styles Colorbox sur le site public, et de l'exécution de la logique JavaScript.

### Gestion des Réglages et Désinstallation
`validateSettingsInDatabase($colorboxSettings)`
* Méthode de validation très simple qui s'assure que les réglages sont ceux attendus par la version actuelle du plugin en vérifiant la présence de la clé `'jQueryColorboxVersion'`. Cela permet de forcer l'utilisation des réglages par défaut si un ancien format de réglage (ou un réglage corrompu) est trouvé.

`jQueryColorboxDefaultSettings()`
Retourne un tableau complet des réglages par défaut du plugin. C'est la source unique de vérité pour les valeurs initiales et les valeurs de repli (fallback). Elle inclut la version du plugin et la version de la librairie JavaScript Colorbox de Jack Moore (http://www.jacklmoore.com/colorbox/).

`uninstallJqueryColorbox()`
Méthode statique exécutée lors de la désinstallation. Elle supprime l'enregistrement des réglages du plugin de la table `wp_options` avec `delete_option(Config::getSettingsName())`, assurant un nettoyage complet des données du plugin.