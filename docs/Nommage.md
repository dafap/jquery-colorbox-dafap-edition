# Nommage des fichiers

## Classes
- les classes `Config` et `JQueryColorbox` sont dans le dossier Includes
  - namespace : `Dafap\JQueryColorbox`
  - nom du fichier : `class-config.php` et `class-jquery-colorbox.php`
- les autres classes se trouve dans le dossier Includes/Classes 
  - le nom de la classe est en CamelCase
  - namspace : Dafap\JQueryColorbox\Classes
  - nom du fichier : 
      - commence par `class-` 
      - suivi de la transformation du nom de la classe par la méthode `kebab-case`, 
      - à l'exception des mots :
         - `JQuery`     qui donne `jquery`, 
         - `HTML`       qui donne `html`,
         - `XML`        qui donne `xml`,
         - `JSON`       qui donne `json`

Exemple:
- la classe `JQueryColorboxFrontend` est dans le fichier `class-jquery-colorbox-frontend`
## Traits
- les traits sont dans le dossier `Traits`
- le nom d'un trait est en `CamelCase` et se termine par `Trait`
- le nom de son fichier est en `kebab-case`avec les mêmes exceptions que pour les classes
- le nom du fichier commence par `trait-`
- dans la transformation en `kebab-case`, on retire la terminaison `-trait`

Exemple:
- le trait `MonSpecialTrait` est dans le fichier `trait-mon-special.php`
## Exceptions
- les exceptions sont dans le dossier `Exceptions`
- le nom d'une exception est en `CamelCase` et se termine par `Exception`
- le nom de son fichier est en `kebab-case`avec les mêmes exceptions que pour les classes
- le nom du fichier commence par `exception-`
- dans la transformation en `kebab-case`, on retire la terminaison `-exception`

Exemple:
- l'exception `MaSpecialeException` est dans le fichier `exception-ma-speciale.php`

## Interfaces
- les interfaces sontr dans le dossier `Interfaces`
- le nom d'un interface est en `CamelCase` et se termine par `Interface`
- le nom de son fichier est en `kebab-case`avec les mêmes exceptions que pour les classes
- le nom du fichier commence par `interface-`
- dans la transformation en `kebab-case`, on retire la terminaison `-interface`

Exemple:
- l'interface `MonSpecialInterface` est dans le fichier `interface-mon-special.php`