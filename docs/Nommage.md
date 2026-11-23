# Conventions de nommage et d'organisation (`Nommage.md`)

Ce document définit les règles de nommage des fichiers, dossiers, espaces de noms (*namespaces*) et ressources du plugin **jQuery Colorbox (DaFAP Edition)**, alignées sur **PSR-4** et sur l'uniformisation des scripts front-end et d'administration.

---

## 1. Architecture PHP (style PSR-4)

Avec l'adoption de la convention **PSR-4**, chaque fichier PHP correspond directement au nom de la classe, du trait, de l'exception ou de l'interface qu'il contient, sans préfixe du type `class-`, `trait-`, `exception-` ou `interface-`.

### Règles générales PSR-4
* **Casse exacte :** Le nom du fichier (`NomElement.php`) respecte exactement le nom en `PascalCase` / `CamelCase` de l'élément déclaré.
* **Correspondance dossier / namespace :** Chaque segment du *namespace* relatif à la racine PSR-4 du plugin correspond à un sous-dossier de même nom.
* **Un élément par fichier :** Chaque fichier PHP déclare une unique classe, interface, trait ou exception.

### Classes
* **Classes principales :** `Config.php` et `JQueryColorbox.php` sont placées dans `Includes/` (ou `src/` selon la racine PSR-4 configurée dans `composer.json`), sous le *namespace* `Dafap\JQueryColorbox`.
* **Autres classes :** Placées dans `Includes/Classes/` (ou le sous-dossier `Classes/` correspondant), sous le *namespace* `Dafap\JQueryColorbox\Classes`.
  * Le nom de la classe est en `PascalCase` / `CamelCase` (ex. `JQueryColorboxFrontend`).
  * Le nom du fichier est identique au nom de la classe suivi de `.php` (ex. `JQueryColorboxFrontend.php`).

### Traits
* Placés dans le dossier `Traits/` sous le *namespace* `Dafap\JQueryColorbox\Traits` (ou le sous-namespace correspondant).
* Le nom du trait est en `PascalCase` / `CamelCase` et se termine par `Trait` (ex. `MonSpecialTrait`).
* Le nom du fichier reprend le nom exact du trait avec l'extension `.php` (ex. `MonSpecialTrait.php`).

### Exceptions
* Placées dans le dossier `Exceptions/` sous le *namespace* `Dafap\JQueryColorbox\Exceptions` (ou le sous-namespace correspondant).
* Le nom de l'exception est en `PascalCase` / `CamelCase` et se termine par `Exception` (ex. `MaSpecialeException`).
* Le nom du fichier reprend le nom exact de l'exception avec l'extension `.php` (ex. `MaSpecialeException.php`).

### Interfaces
* Placées dans le dossier `Interfaces/` sous le *namespace* `Dafap\JQueryColorbox\Interfaces` (ou le sous-namespace correspondant).
* Le nom de l'interface est en `PascalCase` / `CamelCase` et se termine par `Interface` (ex. `MonSpecialInterface`).
* Le nom du fichier reprend le nom exact de l'interface avec l'extension `.php` (ex. `MonSpecialInterface.php`).

---

## 2. Scripts JavaScript (`assets/js/`)

Les scripts JavaScript suivent une convention uniforme en `kebab-case` avec des tirets (et non des points dans le nom de base) :

* **Source non minifiée :** `<nom>.js`
* **Version minifiée :** `<nom>.min.js`

### Fichiers concernés
| Nom de base | Fichier source | Fichier minifié | Rôle |
| :--- | :--- | :--- | :--- |
| `jquery-colorbox` | `jquery-colorbox.js` | `jquery-colorbox.min.js` | Bibliothèque Colorbox (remplace l'ancien `jquery.colorbox.js` / `jquery.colorbox-min.js`) |
| `jquery-colorbox-wrapper` | `jquery-colorbox-wrapper.js` | `jquery-colorbox-wrapper.min.js` | Logique d'initialisation, détection des médias et adaptation responsive / rotation (remplace l'ancien `jquery-colorbox-wrapper-min.js`) |
| `jquery-colorbox-admin` | `jquery-colorbox-admin.js` | `jquery-colorbox-admin.min.js` | Interactions de la page de réglages dans l'administration WordPress |

---

## 3. Synthèse de correspondance

| Élément | Namespace / Dossier | Nom de l'élément | Nom du fichier |
| :--- | :--- | :--- | :--- |
| Classe principale | `Dafap\JQueryColorbox` (`Includes/`) | `Config` | `Config.php` |
| Classe principale | `Dafap\JQueryColorbox` (`Includes/`) | `JQueryColorbox` | `JQueryColorbox.php` |
| Classe secondaire | `Dafap\JQueryColorbox\Classes` (`Includes/Classes/`) | `JQueryColorboxFrontend` | `JQueryColorboxFrontend.php` |
| Trait | `Dafap\JQueryColorbox\Traits` (`Traits/`) | `MonSpecialTrait` | `MonSpecialTrait.php` |
| Exception | `Dafap\JQueryColorbox\Exceptions` (`Exceptions/`) | `MaSpecialeException` | `MaSpecialeException.php` |
| Interface | `Dafap\JQueryColorbox\Interfaces` (`Interfaces/`) | `MonSpecialInterface` | `MonSpecialInterface.php` |
| Script JS (lib) | `assets/js/` | — | `jquery-colorbox.js` / `jquery-colorbox.min.js` |
| Script JS (wrapper) | `assets/js/` | — | `jquery-colorbox-wrapper.js` / `jquery-colorbox-wrapper.min.js` |
| Script JS (admin) | `assets/js/` | — | `jquery-colorbox-admin.js` / `jquery-colorbox-admin.min.js` |