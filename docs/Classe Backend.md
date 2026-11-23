# 📘 Classe `Backend`

Gère toutes les actions, menus, enregistrements de réglages et scripts de l'administration WordPress pour le plugin **jQuery Colorbox Dafap Edition**.

* **Namespace :** `Dafap\JQueryColorbox\Classes`
* **Fichier :** `includes/Classes/Backend.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken

---

## 🧩 Constructeur

```php
public function __construct(array $colorboxSettings)
```

Enregistre les réglages utilisateur dans `$this->colorboxSettings`, charge les valeurs par défaut via `Config::getDefaultSettings()` dans `$this->colorboxDefaultSettings`, et rattache les hooks d'administration WordPress :

* `admin_post_jQueryColorboxDeleteSettings` → `handleDelete()`
* `admin_post_jQueryColorboxUpdateSettings` → `handleUpdate()`
* `admin_enqueue_scripts` → `enqueueAdminScripts()`
* `admin_menu` → `registerAdminMenu()`
* `admin_notices` → `registerAdminWarning()`

---

## 🔍 Méthodes publiques

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `enqueueAdminScripts()` | `public function enqueueAdminScripts(string \$hook): void` | Enfile le script d'administration (`jquery-colorbox-admin.js` ou `.min.js`) sur la page de réglages du plugin et injecte `jqcAdminData`. |
| `registerAdminMenu()` | `public function registerAdminMenu(): void` | Ajoute la page d'options *jQuery Colorbox* sous le menu *Réglages* de WordPress (`manage_options`). |
| `renderSettingsPage()` | `public function renderSettingsPage(): void` | Prépare les données de configuration et affiche le gabarit `backend/settings-page.phtml`. |
| `handleUpdate()` | `public function handleUpdate(): void` | Vérifie les droits et le nonce, assainit les données soumises via `sanitizeSettings()`, met à jour l'option WordPress et redirige. |
| `handleDelete()` | `public function handleDelete(): void` | Vérifie les droits et le nonce, supprime l'option WordPress des réglages et redirige. |
| `registerAdminWarning()` | `public function registerAdminWarning(): void` | Affiche un avertissement dans l'administration si `autoColorbox` et `colorboxWarningOff` sont tous deux inactifs. |

---

## 🔧 Méthodes privées

| Méthode | Signature | Rôle |
| :--- | :--- | :--- |
| `sanitizeSettings()` | `private function sanitizeSettings(array \$submittedData): array` | Normalise les cases à cocher (`'true'`/`'false'`), borne/convertit les valeurs numériques et nettoie les chaînes de caractères à partir de `Config::getDefaultSettings()`. |

---

## 🧠 Dépendances et constantes

* **Constante de classe :**
  * `private const NONCE_ACTION = 'jquery-colorbox_settings-options';`
* **Traits :**
  * `TemplateRenderTrait` : fournit `templateRender()` pour le rendu de `backend/settings-page.phtml`.
  * `UtilsTrait` : fournit notamment `isTrue(string $optionName): bool` pour vérifier si une option booléenne (comme `debugMode`) est active. * **Classe & constantes globales :** * `Dafap\JQueryColorbox\Config` : utilise `Config::SETTINGS_PAGE_SLUG`, `Config::SETTINGS_NAME`, `Config::COLORBOX_VERSION`, `Config::getDefaultSettings()`, `Config::getThemes()`, `Config::getUnits()` et `Config::getTransitions()`. * Constantes globales : `JQC_TEXTDOMAIN` et `JQC_URL`.  ---  ## ⚙️ Détails de fonctionnement des méthodes  ### `enqueueAdminScripts(string $hook): void`
* Vérifie que `$hook` contient `Config::SETTINGS_PAGE_SLUG` ; retourne immédiatement dans le cas contraire. * Choisit `assets/js/jquery-colorbox-admin.js` si `$this->isTrue('debugMode')` vaut `true`, sinon `assets/js/jquery-colorbox-admin.min.js`.
* Enfile le handle `'jquery-colorbox-admin'` avec dépendance `['jquery']`, version `'1.1.0'`, et chargement dans le pied de page (`true`).
* Localise l'objet global `'jqcAdminData'` contenant `settingsName` (`Config::SETTINGS_NAME`) et `pluginUrl` (`JQC_URL`).

### `registerAdminMenu(): void`
* Appelle `add_options_page()` avec le titre de page `__('jQuery Colorbox Settings', JQC_TEXTDOMAIN)`, le libellé de menu `__('jQuery Colorbox', JQC_TEXTDOMAIN)`, la capacité `'manage_options'`, le slug `Config::SETTINGS_PAGE_SLUG` et le rappel `[$this, 'renderSettingsPage']`.  ### `renderSettingsPage(): void` * Calcule `$plugin_url` via `plugin_dir_url(dirname(__DIR__, 2) . '/jquery-colorbox-dafap-edition.php')`.
* Appelle `$this->templateRender('backend/settings-page.phtml', [...])` en transmettant `settingsName`, `settings`, `themes`, `units`, `transitions`, `defaults`, `pluginName`, `pluginUrl`, `textDomain`, `colorboxVersion` et `nonceAction`.  ### `handleUpdate(): void` * Vérifie `current_user_can('manage_options')` (sinon `wp_die()`) et `check_admin_referer(self::NONCE_ACTION)`. * Récupère `$_POST[Config::SETTINGS_NAME] ?? []`, applique `$this->sanitizeSettings(...)`, enregistre via `update_option(Config::SETTINGS_NAME, $validated_settings)` puis redirige vers `options-general.php?page=<slug>&updated=true`.

### `handleDelete(): void`
* Vérifie `current_user_can('manage_options')` (sinon `wp_die()`) et `check_admin_referer(self::NONCE_ACTION)`.
* Supprime l'option via `delete_option(Config::SETTINGS_NAME)` puis redirige vers `options-general.php?page=<slug>&deleted=true`.

### `registerAdminWarning(): void`
* Lit `get_option(Config::SETTINGS_NAME, [])`.
* Si `empty($settings['autoColorbox'])` et `empty($settings['colorboxWarningOff'])`, affiche un bloc d'avertissement `.notice.notice-warning` échappé avec `esc_html__()`.

### `sanitizeSettings(array $submittedData): array` * Part de `Config::getDefaultSettings()` comme base pour `$newSettings`.
* Convertit chaque clé listée dans `$checkboxKeys` en `'true'` si présente dans `$submittedData`, ou `'false'` sinon.
* Pour les autres clés, reprend `$submittedData[$key] ?? $defaultValue`. * Applique `absint()` aux clés de `$numericKeys` (et prévoit un branchement `max(0.0, min(1.0, floatval($value)))` lorsque `$key === 'opacity'`), ou `sanitize_text_field()` sur les chaînes de caractères.

---

## 🛠️ Points de vigilance pour la maintenance

* **Clé `'opacity'` dans `sanitizeSettings()` :** Le test `if ($key === 'opacity')` est placé à l'intérieur du bloc `if (in_array($key, $numericKeys, true))`, alors que `'opacity'` ne figure pas dans la liste `$numericKeys` définie au début de la méthode.
* **Clés héritées dans `$checkboxKeys` :** Des options comme `'autoHideFlash'` ou `'useGoogleJQuery'` figurent encore dans `$checkboxKeys` (et dans `Config::getDefaultSettings()`) ; vérifiez leur utilité si ces fonctionnalités ont été retirées.
* **Version du script d'administration :** `enqueueAdminScripts()` passe `'1.1.0'` en version de script, distincte de `JQC_VERSION` ou `Config::COLORBOX_VERSION`.