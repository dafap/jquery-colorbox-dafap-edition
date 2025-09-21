=== jQuery Colorbox Dafap Edition ===
Contributors: dafap, arnefranken
Tags: lightbox, colorbox, gallery, image, overlay, jquery, responsive
Requires at least: 6.0
Tested up to: 6.7
Stable tag: 5.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Refonte complète du plugin jQuery Colorbox pour WordPress, optimisée pour WordPress 6.7+ et PHP 8.2+. Cette édition Dafap propose une architecture orientée objet, une traduction différée, une compatibilité renforcée, et une intégration automatique dans les contenus existants.

Le plugin ajoute un effet lightbox élégant aux images, galeries, vidéos et contenus HTML, en s'appuyant sur la bibliothèque jQuery Colorbox.

Cette version est basée sur le travail original d'Arne Franken, avec attribution complète. Elle est maintenue par Alain Pomirol (Dafap) dans le cadre d'une démarche open source rigoureuse et respectueuse.

== Installation ==

1. Téléchargez le plugin ou clonez le dépôt GitHub.
2. Placez le dossier dans `/wp-content/plugins/jquery-colorbox-dafap-edition/`.
3. Activez le plugin via le menu Extensions de WordPress.
4. Configurez les options dans Réglages > jQuery Colorbox.

== Frequently Asked Questions ==

= Cette version est-elle compatible avec les anciens articles ? =
Oui. Le plugin applique automatiquement l'effet Colorbox aux images et galeries existantes, sans nécessiter de modification manuelle.

= Puis-je personnaliser le thème Colorbox ? =
Oui. Plusieurs thèmes sont disponibles dans les réglages du plugin.

= Le plugin est-il traduit ? =
Oui. La traduction est chargée dynamiquement via le hook `init`, conformément aux bonnes pratiques WordPress.

== Screenshots ==

1. Exemple d'image avec effet Colorbox
2. Interface de configuration du plugin
3. Galerie WordPress avec Colorbox actif

== Changelog ==

= 5.0 =
* Refactorisation complète en architecture orientée objet
* Compatibilité WordPress 6.7+ et PHP 8.2+
* Traduction différée via `init`
* Normalisation des fins de ligne via `.gitattributes`
* Sécurisation des sorties HTML
* Ajout de commentaires PHPDoc
* Attribution à l’auteur initial conservée

== Upgrade Notice ==

= 5.0 =
Version majeure avec refonte complète. Vérifiez la compatibilité avec vos thèmes et plugins avant mise à jour.

== Credits ==

Plugin original développé par Arne Franken. Cette édition est maintenue par Alain Pomirol (Dafap) et distribuée sous licence GPL.
