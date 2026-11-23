# 📘 Trait `UtilsTrait`

Fournit des méthodes utilitaires partagées par les classes du plugin **jQuery Colorbox Dafap Edition**, notamment l'évaluation homogène des options booléennes issues des réglages.

* **Namespace :** `Dafap\JQueryColorbox\Traits`
* **Fichier :** `includes/Traits/UtilsTrait.php` (conforme PSR-4)
* **Type strict :** `declare(strict_types=1);`
* **Auteur :** Alain Pomirol (Dafap)

---

## 🔍 Méthodes protégées

| Méthode | Signature | Description |
| :--- | :--- | :--- |
| `isTrue()` | `protected function isTrue(string \$key): bool` | Vérifie si l'option `$key` dans `$this->colorboxSettings` est définie et correspond à une valeur vraie (`true`, `'true'`, `1` ou `'1'`). |

---

## 🧠 Dépendances et hypothèses de contexte

* **Propriété attendue sur la classe utilisatrice :** La méthode `isTrue()` accède à `$this->colorboxSettings`. Toute classe utilisant `UtilsTrait` (comme `Frontend` ou `Backend`) doit définir et alimenter la propriété `$colorboxSettings`.

---

## ⚙️ Détails de fonctionnement de la méthode

### `isTrue(string $key): bool` 
1. Vérifie si `$this->colorboxSettings[$key]` est défini via `!isset($this->colorboxSettings[$key])` ; retourne `false` si la clé est absente. 
2. Récupère la valeur dans `$val = $this->colorboxSettings[$key]`.
3. Évalue strictement l'égalité avec l'une des représentations considérées comme vraies : `true`, `'true'`, `1` ou `'1'`, et retourne le résultat booléen.

---

## 🛠️ Points de vigilance pour la maintenance

* **Normalisation des formats de réglages :** Comme `Config::getDefaultSettings()` et les formulaires/options WordPress peuvent mélanger des booléens natifs, des entiers et des chaînes (`'true'` / `'false'`), `isTrue()` permet de traiter ces variantes de manière uniforme.
* **Présence de `$this->colorboxSettings` :** Si `UtilsTrait` est réutilisé dans une classe ne déclarant pas `$colorboxSettings`, PHP émettra un avertissement/erreur sur la propriété non déclarée.