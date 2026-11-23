# 📘 Classe `JQueryColorbox`

Classe principale d'orchestration du plugin **jQuery Colorbox Dafap Edition** pour WordPress. Elle implémente le modèle *Singleton*, charge la traduction, gère les réglages persistés et initialise soit l'interface d'administration (`Backend`), soit le rendu public (`Frontend`).

* **Namespace :** `Dafap\JQueryColorbox`
* **Fichier :** `includes/JQueryColorbox.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken

---

## 🧩 Constructeur et instance (*Singleton*)

```php
public static function get_instance(): self
private function __construct()
```

* Le constructeur `__construct()` est déclaré `private` afin d'empêcher l'instanciation directe et de garantir une instance unique via `get_instance()`.
* `get_instance()` initialise `self::\$instance` au premier appel et retourne l'instance partagée (`self`).

---

## 🔍 Méthodes publiques

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `get_instance()` | `public static function get_instance(): self` | Retourne l'instance unique (*Singleton*) de la classe `JQueryColorbox`. |
| `run()` | `public function run(): void` | Démarre le plugin : rattache le chargement des traductions et le lien d'action, charge/valide les réglages et instancie `Backend` ou `Frontend`. |
| `loadTextdomain()` | `public function loadTextdomain(): void` | Charge le domaine de traduction du plugin depuis le sous-dossier `/languages`. |
| `addPluginActionLinks()` | `public function addPluginActionLinks(array \$links): array` | Ajoute le lien *Settings* vers la page de réglages du plugin en tête de la liste des actions sur l'écran *Extensions*. |

---

## 🧠 Dépendances et constantes

* **Classes du plugin :**
  * `Dafap\JQueryColorbox\Config` : utilisé pour `Config::SETTINGS_NAME`, `Config::SETTINGS_PAGE_SLUG` et `Config::getDefaultSettings()`.
  * `Dafap\JQueryColorbox\Classes\Backend` : instancié lorsque `is_admin()` est vrai.
  * `Dafap\JQueryColorbox\Classes\Frontend` : instancié lorsque `is_admin()` est faux.
* **Constantes globales :**
  * `JQC_BASENAME` : nom de base du fichier principal du plugin (utilisé pour le filtre `plugin_action_links_` et `plugin_basename()`).
  * `JQC_TEXTDOMAIN` : identifiant de traduction du plugin.

---

## ⚙️ Détails de fonctionnement des méthodes

### `run(): void`
1. Rattache `loadTextdomain()` sur l'action `init` avec la priorité `5`.
2. Rattache `addPluginActionLinks()` sur le filtre `'plugin_action_links_' . JQC_BASENAME`.
3. Récupère l'option `Config::SETTINGS_NAME` via `get_option()` (castée en `array`) et les valeurs par défaut via `Config::getDefaultSettings()`.
4. Vérifie la présence de la clé `'jQueryColorboxVersion'` dans `$usersettings` (`$validSettings`).
5. Fusionne `$usersettings` avec `$defaultArray` via `wp_parse_args()` si valide, ou utilise `$defaultArray` sinon. 6. Si `!$validSettings`, enregistre `$defaultArray` en base via `update_option(Config::SETTINGS_NAME, $defaultArray)`.
7. Selon `is_admin()`, instancie `new Backend(\$this->colorboxSettings)` ou `new Frontend($this->colorboxSettings)`.  ### `loadTextdomain(): void` * Appelle `load_plugin_textdomain(JQC_TEXTDOMAIN, false, dirname(plugin_basename(JQC_BASENAME)) . '/languages')`.  ### `addPluginActionLinks(array $links): array`
* Construit un lien HTML `<a href="...">` pointant vers `admin_url('options-general.php?page=' . Config::SETTINGS_PAGE_SLUG)` avec le texte traduit `__('Settings', JQC_TEXTDOMAIN)`.
* Insère ce lien au début de `$links` avec `array_unshift()` et retourne le tableau mis à jour.  

---  

## 🛠️ Points de vigilance pour la maintenance
  * **Clé `'jQueryColorboxVersion'` comme critère de validité :** Si l'utilisateur supprime ou réinitialise les réglages (`delete_option` dans `Backend::handleDelete()`), `$validSettings` devient faux et `run()` réécrit immédiatement les réglages par défaut en base via `update_option()`.

  * **Appel à `is_admin()` dans `run()` :** Si `run()` est exécuté avant que `is_admin()` soit pleinement fiable dans le cycle WordPress, assurez-vous que l'appel à `JQueryColorbox::get_instance()->run()` intervient au bon moment du chargement du plugin.

  * **Convention de nommage des méthodes :** `get_instance()` utilise le `snake_case`, tandis que les autres méthodes de la classe (`loadTextdomain`, `addPluginActionLinks`) suivent le `camelCase`.