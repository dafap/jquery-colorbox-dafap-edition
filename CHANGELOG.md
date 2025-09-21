
---

## 📝 `CHANGELOG.md` — Historique des modifications

```markdown
# Changelog — jQuery Colorbox Dafap Edition

## [5.0] — 2025-09-21
### Ajouté
- Création du dépôt GitHub
- Ajout du fichier `.gitattributes` pour normalisation des fins de ligne
- Refactorisation complète en architecture orientée objet
- Passage du nom du plugin via propriété de classe
- Traduction différée via `init`
- Compatibilité WordPress 6.7+ et PHP 8.2+
- Sécurisation des sorties HTML (`esc_html`, `esc_attr`)
- Ajout de commentaires PHPDoc sur toutes les méthodes

### Modifié
- Suppression des appels directs à `JQUERYCOLORBOX_NAME`
- Remplacement des `array(& $this, ...)` par `[ $this, ... ]`
- Réorganisation des hooks frontend

### À venir
- Ajout d’un shortcode `[colorbox]`
- Interface d’administration modernisée
- Publication sur WordPress.org (fork ou nouvelle entrée)

