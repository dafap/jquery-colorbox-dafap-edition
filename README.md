# jQuery Colorbox Dafap Edition

Refonte complète et modernisation de l'extension WordPress **jQuery Colorbox**, optimisée pour **WordPress 6.7+** et **PHP 8.2+ / 8.4+**. Ce plugin permet d'afficher vos images, galeries WordPress et contenus multimédias dans une fenêtre modale (lightbox) fluide, personnalisable et réactive.

## 🎯 Objectifs de cette édition

Basé sur le travail historique d'Arne Franken, cette édition maintenue par **Dafap** apporte une remise à niveau technique complète pour garantir la pérennité, la sécurité et les performances du plugin sur les environnements modernes :

- **Architecture PSR-4 stricte :** Abandon de l'ancien chargement manuel au profit de Composer et d'un découpage orienté objet clair (`Config`, `JQueryColorbox`, `Backend`, `Frontend`, `Traits`).
- **Mises à jour automatiques via GitHub :** Intégration de *Plugin Update Checker (PUC v6)* permettant de déployer les nouvelles versions directement depuis le tableau de bord WordPress.
- **Compatibilité PHP moderne :** Typage strict (`declare(strict_types=1)`), suppression des avertissements et dépréciations PHP 8.x.
- **Gestion unifiée des configurations :** Extraction dynamique des métadonnées (`get_file_data()`) et centralisation des constantes globales (`JQC_VERSION`, `JQC_URL`, `JQC_PATH`, `JQC_TEXTDOMAIN`).
- **Internationalisation (i18n) :** Chargement différé des traductions (`jquery-colorbox-fr_FR.mo`) conforme aux recommandations WordPress.

---

## 📂 Architecture du projet

```text
jquery-colorbox-dafap-edition/
├── assets/
│   ├── css/                # Feuilles de style additionnelles (ex: effet zoom)
│   ├── images/             # Ressources graphiques et aperçus des thèmes
│   ├── js/                 # Bibliothèque Colorbox (1.6.4), wrapper front et scripts admin
│   └── themes/             # 11 thèmes visuels prêts à l'emploi (theme1 à theme11)
├── includes/
│   ├── Classes/
│   │   ├── Backend.php     # Gestion du menu admin, des réglages et de la validation
│   │   └── Frontend.php    # Injection des scripts, styles et filtres sur le site public
│   ├── Traits/
│   │   ├── TemplateRenderTrait.php # Moteur de rendu des vues .phtml
│   │   └── UtilsTrait.php          # Méthodes utilitaires partagées
│   ├── Config.php          # Constantes, réglages par défaut, listes de thèmes et unités
│   └── JQueryColorbox.php  # Classe principale (Singleton) orchestrant le plugin
├── languages/              # Fichiers de traduction (.po / .mo)
├── templates/
│   ├── backend/
│   │   └── settings-page.phtml # Interface de configuration dans l'administration
│   └── frontend/
│       └── meta-link.phtml     # Lien d'attribution optionnel
├── vendor/                 # Dépendances gérées par Composer (Autoloader + PUC v6)
├── composer.json           # Définition des dépendances et du mapping PSR-4
├── jquery-colorbox-dafap-edition.php # Point d'entrée principal du plugin
└── README.md               # Documentation du projet
```

---

## 🚀 Installation et Déploiement

### 1. Installation manuelle ou via Git
Clonez le dépôt directement dans le répertoire `wp-content/plugins/` de votre installation WordPress :

```bash
cd wp-content/plugins/
git clone https://github.com/dafap/jquery-colorbox-dafap-edition.git
cd jquery-colorbox-dafap-edition
```

### 2. Installation des dépendances Composer
Générez l'autoloader PSR-4 optimisé et installez *Plugin Update Checker* :

```bash
composer install --no-dev -o
```
*(En environnement de développement, utilisez simplement `composer dump-autoload -o` après modification des classes).*

### 3. Activation
Rendez-vous dans l'interface d'administration WordPress (**Extensions > Extensions installées**) et activez **jQuery Colorbox Dafap Edition**.

---

## ⚙️ Configuration

Une fois activé, un lien rapide **Réglages** apparaît sous le nom de l'extension, ou via le menu **Réglages > jQuery Colorbox**.

### Principales options disponibles :
- **Automatisation globale (`autoColorbox`) :** Applique automatiquement l'effet Colorbox à toutes les images insérées dans les articles, pages et galeries, en les regroupant par publication.
- **Galeries uniquement (`autoColorboxGalleries`) :** Restreint l'effet aux seules galeries natives de WordPress.
- **Autres images (`autoColorboxJavaScript`) :** Étend l'effet aux images situées hors du contenu principal (ex. barres latérales/widgets).
- **Apparence & Thèmes :** Choix parmi **11 thèmes visuels** avec prévisualisation instantanée dans l'administration.
- **Dimensions & Comportement :** Contrôle fin des dimensions maximales (`px`, `%`, `em`, `rem`), vitesse de transition (`elastic`, `fade`, `none`), opacité de l'arrière-plan, mode diaporama automatique et effet de survol (zoom overlay).
- **Optimisation :** Option permettant de charger les scripts JavaScript dans le pied de page (`wp_footer`) pour améliorer les performances de chargement.

---

## 🛠️ Développement et Maintenance

- **Mode Débogage :** La constante `Config::DEBUG_MODE` permet de basculer automatiquement entre les versions non minifiées (`.js`) et minifiées (`.min.js`) des scripts front et admin, tout en activant les journaux dans la console du navigateur.
- **Mises à jour :** Toute nouvelle *Release* publiée sur le dépôt GitHub (`https://github.com/dafap/jquery-colorbox-dafap-edition`) sera automatiquement détectée par WordPress via PUC v6.

---

## 📜 Crédits et Licence

- **Auteur de la refonte (Dafap Edition) :** Alain Pomirol — [Dafap](https://www.dafap.fr)
- **Auteur original du plugin :** Arne Franken
- **Bibliothèque sous-jacente :** jQuery Colorbox v1.6.4 par Jack Moore (Licence MIT)
- **Licence du plugin :** GPL-2.0-or-later