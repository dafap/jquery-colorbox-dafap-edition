# 📘 Classe `Frontend`

Gère toutes les actions frontend du plugin jQuery Colorbox Dafap Edition pour WordPress.

- Namespace: `Dafap\JQueryColorbox\Classes` 
- Fichier: `includes/Classes/class-jquery-colorbox-frontend.php`
- Version: 5.0 
- Auteur: Alain Pomirol (Dafap), basé sur le travail original d'Arne Franken

## 🧩 Constructeur

```php
__construct(array $colorboxSettings, string $pluginName)
```

Initialise les hooks WordPress selon les paramètres du plugin.

## 🔍 Méthodes publiques
| **Méthode**                                            | **Description**                                                         |
|--------------------------------------------------------|-------------------------------------------------------------------------|
| `renderMetaLink()`	                                 | Affiche le lien meta dans le flux HTML via un template.                 |
| `addColorboxGroupIdToImages(string $content): string`	 | Ajoute une classe colorbox-<post_id> aux balises <img> dans le contenu. |
| `wpPostThumbnailClassFilter(array $attributes): array` | Ajoute une classe colorbox-<post_id> aux miniatures WordPress.          |
| `enqueueMyScripts()`	                                 | Met en file d’attente les scripts et styles nécessaires au frontend.    |
## 🔧 Méthodes privées

| **Méthode**                                            |	**Rôle**                                                            |
|--------------------------------------------------------|----------------------------------------------------------------------|
| `addColorboxCSS()`                                     |	Charge le thème CSS et le zoom overlay si activé.                   |
| `addColorboxJS()`	                                     | Charge le script principal Colorbox.                                 |
| `addColorboxWrapperJS()`                               |	Charge le wrapper JS selon le mode debug.                           |
| `addColorboxProperties()`	                             | Injecte les paramètres JS via wp_localize_script.                    |
| `formatColorboxDimension(string $settingName): string` | Formate les dimensions CSS à partir des réglages.                    |
| `isTrue(string $optionName): bool`                     | Vérifie si une option booléenne est activée.                         |
## 🧠 Dépendances
- `TemplateRenderTrait` : pour le rendu des templates .phtml
- `Config` : pour accéder aux constantes du plugin (URL, version, textdomain)