# 📘 Trait `TemplateRenderTrait`

Fournit une méthode utilitaire pour charger et inclure les fichiers de template (`.phtml`) du plugin **jQuery Colorbox Dafap Edition** en exposant des variables locales dans le contexte d'exécution.

* **Namespace :** `Dafap\JQueryColorbox\Traits`
* **Fichier :** `includes/Traits/TemplateRenderTrait.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap)

---

## 🔍 Méthodes publiques

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `templateRender()` | `public function templateRender(string \$templatePath, array $data = []): void` \vert{} Vérifie l'existence du template dans `templates/`, extrait `$data` en variables locales et inclut le fichier. |

---

## 🧠 Dépendances et constantes

* **Constantes globales :**
  * `JQC_PATH` : chemin absolu vers la racine du plugin, utilisé pour construire le chemin vers le dossier `templates/`.
  * `JQC_TEXTDOMAIN` : domaine de traduction utilisé pour le message d'erreur affiché en mode `WP_DEBUG`.
* **Constantes / fonctions WordPress :**
  * `WP_DEBUG`, `esc_html__()`, `esc_html()` (lorsqu'un template est introuvable et que `WP_DEBUG` est actif).

---

## ⚙️ Détails de fonctionnement de la méthode

### `templateRender(string $templatePath, array$data = []): void`
1. Construit le chemin complet du fichier de template via `rtrim(JQC_PATH, '/\\') . '/templates/' . ltrim($templatePath, '/\\')`. 
2. Vérifie l'existence du fichier avec `file_exists($file)`.
3. Si le fichier est introuvable :
   * lorsque `defined('WP_DEBUG') && WP_DEBUG` est vrai, affiche un bloc d'alerte `.notice.notice-error` contenant le chemin du fichier manquant ;
   * retourne immédiatement (`return;`) sans tenter d'inclure le fichier.
4. Si `$data` n'est pas vide, appelle `extract($data, EXTR_OVERWRITE)` afin de rendre chaque clé du tableau disponible comme variable dans le template.
5. Inclut le fichier avec `include $file;`.  

---  

## 🛠️ Points de vigilance pour la maintenance
  * **Utilisation de `extract($data, EXTR_OVERWRITE)` :** Les clés de `$data` deviennent des variables locales dans la portée de `templateRender()` et peuvent écraser d'éventuelles variables locales existantes dans cette méthode (`$file`, `$templatePath`, `$data`). Veillez à nommer explicitement les clés passées dans `$data` (comme le font `Frontend` et `Backend`).
  * **Chemin du template (`$templatePath`) :** Le paramètre est attendu relatif au sous-dossier `templates/` (par exemple `'frontend/meta-link.phtml'` ou `'backend/settings-page.phtml'`).
  * **Sortie directe (`echo` / `include`) :** La méthode affiche directement le rendu et ne capture pas la sortie dans une chaîne de caractères.