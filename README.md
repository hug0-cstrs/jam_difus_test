# :soccer: Football Manager Pro

<div align="center">
  <img src="assets/favicon/soccer-ball.svg" alt="Logo Football Manager Pro" width="120"/>
  
  ![PHP](https://img.shields.io/badge/PHP-7.4.33-blue)
  ![MySQL](https://img.shields.io/badge/MySQL-8.0.4-orange)
  ![jQuery](https://img.shields.io/badge/jQuery-3.6.0-blue)
  ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-purple)
  ![PHPUnit](https://img.shields.io/badge/PHPUnit-9.6-green)
</div>

## 📖 À propos

Football Manager Pro est une application CRUD (Create, Read, Update, Delete) dédiée à la gestion des joueurs de football. Elle permet de :

- 📋 **Consulter** la liste des joueurs et leurs caractéristiques détaillées (âge, position, équipe, etc.)
- ➕ **Ajouter** de nouveaux joueurs à la base de données
- 📝 **Modifier** les informations des joueurs existants
- 🗑️ **Supprimer** les joueurs de la base de données

L'interface utilisateur moderne et intuitive rend ces opérations simples et accessibles.

### ✨ Caractéristiques principales

- 🎴 Interface moderne avec design de carte
- 🔍 Recherche en temps réel
- 🏷️ Filtrage par nom, prénom, équipe et position
- 📱 Design responsive
- 🎯 Actions CRUD complètes
- 🌓 Mode sombre/clair
- 🔄 Animations fluides
- 🧪 Tests unitaires complets
- 🔒 Sécurité renforcée

## 🛠️ Technologies utilisées

- **Backend**

  - PHP 7.4.33
  - MySQL 8.0.4
  - PDO pour la sécurité des requêtes
  - PHPUnit 9.6 pour les tests

- **Frontend**
  - HTML5 & CSS3
  - JavaScript (jQuery)
  - Bootstrap 5
  - Font Awesome
  - AOS (Animate On Scroll)

## ⚙️ Prérequis

- PHP 7.4.33 ou supérieur
- MySQL 8.0.4 ou supérieur
- Serveur web (Apache, Nginx)
- Composer
- PHPUnit 9.6

## 📥 Installation

1. **Clonez le dépôt**

```bash
git clone https://github.com/votre-username/jam_difus_test.git
cd football-manager-pro
```

2. **Créez la base de données**

```bash
mysql -u root -p < database.sql
```

3. **Configurez la connexion**

- Ouvrez `includes/db.php`
- Modifiez les paramètres selon votre configuration

4. **Peuplez la base de données**

```bash
php scripts/seed.php
```

5. **Configurez votre serveur web**

- Pointez votre serveur vers le dossier du projet

## 📁 Structure du projet

```
football-manager-pro/
├── ajax/                   # Scripts PHP pour les requêtes AJAX
│   ├── add_player.php
│   ├── delete_player.php
│   ├── get_player_details.php
│   └── get_players.php
│   └── update_player.php
│
├── assets/                 # Ressources statiques
│   ├── css/
│   │   └── style.css      # Styles personnalisés
│   ├── favicon/
│   │   └── soccer-ball.svg # Logo de l'application
│   ├── js/
│   │   └── app.js         # JavaScript principal
│
├── includes/              # Fichiers d'inclusion PHP
│   └── db.php            # Configuration de la base de données
│
├── scripts/              # Scripts utilitaires
│   └── seed.php         # Script de peuplement
│
├── templates/            # Templates HTML
│   ├── components/      # Composants réutilisables
│   │   ├── navbar.html
│   │   ├── player_card.html
│   │   ├── player_details.html
│   │   └── search_filters.html
│   └── modals/          # Modales
│         ├── add_player_modal.html
│         ├── delete_confirmation_modal.html
│         ├── edit_player_modal.html
│         └── player_details_modal.html
│
├── tests/               # Tests unitaires
│   ├── AddPlayerTest.php
│   ├── bootstrap.php
│   ├── DbTest.php
│   ├── GetPlayerDetailsTest.php
│   ├── GetPlayersTest.php
│   ├── PhpInputStreamMock.php
│   └── TestCase.php
│
├── database.sql        # Structure de la base de données
├── index.php          # Point d'entrée de l'application
├── phpunit.xml        # Configuration PHPUnit
└── README.md
```

## 🔧 Configuration

### Base de données

La configuration de la base de données se trouve dans `includes/db.php`. Assurez-vous de définir :

- Nom d'hôte
- Nom de la base de données
- Nom d'utilisateur
- Mot de passe

### Tests

Les tests utilisent une base de données séparée. Configuration dans `tests/bootstrap.php`.

## 🧪 Tests

Pour installer PHPUnit, il faut télécharger le fichier phpunit-9.6.phar et donner les droits d'exécution:

```bash
curl -LO https://phar.phpunit.de/phpunit-9.6.phar
chmod +x phpunit-9.6.phar
```

Pour exécuter la suite de tests complète :

```bash
./phpunit-9.6.phar --debug tests/
```

### Couverture des tests

- ✅ Tests de connexion à la base de données
- ✅ Tests CRUD des joueurs
- ✅ Tests de validation des données
- ✅ Tests des filtres et de la recherche
- ✅ Tests de gestion des erreurs

## 🔐 Sécurité

- Protection contre les injections SQL (PDO)
- Headers HTTP sécurisés
- Messages d'erreur contrôlés

## 📱 Compatibilité

- ✅ Chrome (dernières versions)
- ✅ Firefox (dernières versions)
- ✅ Safari (dernières versions)
- ✅ Edge (dernières versions)

## 🚀 Améliorations futures

- 🛡️ **Sécurité renforcée**

  - Implémentation de CSRF tokens
  - Injection SQL
  - XSS

- 🌐 **Internationalisation**

  - Support multilingue

- 🤝 **Intégrations**

  - API externes de statistiques

- 📦 **Gestion des dépendances avec Composer**
  - Mise en place de Composer pour la gestion des dépendances
  - Création du dossier vendor pour les bibliothèques tierces
  - Automatisation de l'installation des dépendances via composer install
  - Gestion des versions des bibliothèques
