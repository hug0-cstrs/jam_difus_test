# :soccer: Football Manager Pro

<div align="center">
  <img src="assets/favicon/soccer-ball.svg" alt="Logo Football Manager Pro" width="120"/>
  
  ![PHP](https://img.shields.io/badge/PHP-7.4.33-blue)
  ![MySQL](https://img.shields.io/badge/MySQL-8.0.4-orange)
  ![jQuery](https://img.shields.io/badge/jQuery-3.6.0-blue)
  ![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.0-purple)
  ![PHPUnit](https://img.shields.io/badge/PHPUnit-9.6-green)
  ![Tests](https://img.shields.io/badge/Tests-41%20passed-brightgreen)
</div>

## 📖 À propos

Football Manager Pro est une application CRUD (Create, Read, Update, Delete) robuste et testée, dédiée à la gestion des joueurs de football. Cette application a été développée dans le cadre d'un test technique avec un focus particulier sur la qualité du développement front-end.

### ✨ Fonctionnalités principales

- 📋 **Gestion complète des joueurs**

  - Consultation détaillée des profils
  - Ajout avec validation des données
  - Modification en temps réel
  - Suppression sécurisée

- 🔍 **Recherche et filtrage avancés**

  - Recherche instantanée par nom
  - Filtrage par équipe et position
  - Combinaison de critères de recherche

- 🎨 **Interface utilisateur moderne**

  - Design responsive avec Bootstrap 5
  - Animations fluides avec AOS
  - Mode sombre/clair adaptatif
  - Modales interactives

- 🧪 **Tests exhaustifs**
  - 41 tests unitaires et d'intégration
  - Couverture complète des fonctionnalités CRUD
  - Base de données de test isolée
  - Mock des entrées PHP pour les tests

## 🛠️ Architecture technique

### Backend (PHP 7.4.33)

- **Structure MVC simplifiée**

  - Séparation claire des responsabilités
  - Gestion modulaire des composants
  - Templates réutilisables

- **Gestion de base de données**

  - MySQL 8.0.4 avec PDO
  - Requêtes préparées
  - Transactions sécurisées

- **Tests automatisés**
  - PHPUnit 9.6
  - Tests unitaires et d'intégration
  - Base de données temporaire pour les tests
  - Mocking des entrées/sorties

### Frontend

- **Framework et bibliothèques**

  - Bootstrap 5.3.0 pour le responsive design
  - jQuery 3.6.0 pour les interactions AJAX
  - Font Awesome pour les icônes
  - AOS pour les animations au scroll

- **Architecture JavaScript**
  - Modules bien organisés
  - Gestion des événements optimisée
  - Validation côté client
  - Feedback utilisateur en temps réel

## 📥 Installation et configuration

### Prérequis

- PHP 7.4.33 ou supérieur
- MySQL 8.0.4 ou supérieur
- Serveur web (Apache/Nginx)
- PHPUnit 9.6 pour les tests
- Composer 2.7.7 ou supérieur

### Installation

1. **Clonage du dépôt**

```bash
git clone https://github.com/hug0-cstrs/jam_difus_test.git
cd football-manager-pro
```

2. **Installation des dépendances**

```bash
# Installation des dépendances PHP via Composer
composer install
```

3. **Configuration de la base de données**

```bash
# Création de la base de données
mysql -u root -p < database.sql

# Configuration de la connexion
# Éditer includes/db.php avec vos paramètres
```

4. **Peupler la base de données**

```bash
php scripts/seed.php
```

5. **Installation de PHPUnit**

```bash
# Téléchargement de PHPUnit
curl -LO https://phar.phpunit.de/phpunit-9.6.phar
chmod +x phpunit-9.6.phar
```

### Lancement de l'application

1. **Démarrage du serveur de développement**

```bash
# Placez-vous dans le dossier du projet
cd football-manager-pro

# Démarrez le serveur PHP intégré
php -S localhost:8000

# L'application est maintenant accessible à :
http://localhost:8000
```

2. **Vérification de l'installation**

- Ouvrez votre navigateur et accédez à `http://localhost:8000`
- La page d'accueil devrait afficher la liste des joueurs
- Testez les fonctionnalités :
  - ➕ Ajout d'un nouveau joueur via le bouton "Ajouter"
  - 🔍 Recherche d'un joueur existant
  - ✏️ Modification d'un joueur
  - 🗑️ Suppression d'un joueur

3. **Arrêt du serveur**

- Utilisez `Ctrl+C` dans le terminal pour arrêter le serveur PHP
- Le serveur redémarre automatiquement en cas de modification des fichiers PHP

## 🧪 Suite de tests

### Structure des tests

- **Tests unitaires**

  - `PlayerCRUDTest` : Tests CRUD basiques
  - `GetPlayersTest` : Tests de recherche et filtrage
  - `TemplateManagerTest` : Tests du gestionnaire de templates

- **Tests d'intégration**
  - `PlayerManagementTest` : Test du cycle de vie complet
  - `DbTest` : Tests de connexion à la base de données

### Exécution des tests

```bash
# Exécution de tous les tests
./phpunit-9.6.phar --debug tests/

# Résultats actuels
✅ 41 tests réussis
✅ 106 assertions validées
✅ 0 échec
```

### Base de données de test

- Table temporaire créée pour chaque test
- Isolation complète de la base de production
- Nettoyage automatique après chaque test
- Données de test cohérentes et réutilisables

## 📁 Structure détaillée du projet

```
football-manager-pro/
├── ajax/                   # Endpoints AJAX
├── assets/                 # Ressources statiques
├── includes/              # Classes et configurations
├── templates/            # Templates HTML modulaires
└── tests/               # Suite de tests complète
    ├── Unit/           # Tests unitaires
    ├── Integration/    # Tests d'intégration
    └── bootstrap.php   # Configuration des tests
```

## 🔐 Sécurité

- Protection contre les injections SQL (PDO)
- Validation des entrées côté serveur
- Gestion sécurisée des fichiers
- Messages d'erreur contrôlés
- Base de données de test isolée

## 📈 Points forts du projet

1. **Qualité du code**

   - Architecture claire et modulaire
   - Conventions de nommage cohérentes
   - Documentation détaillée
   - Tests exhaustifs

2. **Robustesse**

   - Gestion des erreurs complète
   - Validation des données
   - Tests automatisés
   - Base de données sécurisée

3. **Maintenabilité**

   - Structure de projet claire
   - Code commenté
   - Tests comme documentation
   - Composants réutilisables

4. **Expérience utilisateur**
   - Interface intuitive
   - Feedback en temps réel
   - Performance optimisée
   - Design responsive

## 🎯 Choix techniques justifiés

1. **PHPUnit pour les tests**

   - Framework de test mature
   - Support des mocks et stubs
   - Intégration facile
   - Documentation riche

2. **Base de données temporaire pour les tests**

   - Isolation complète
   - Performance optimale
   - Pas de pollution des données
   - Exécution parallèle possible

3. **Architecture modulaire**
   - Maintenance facilitée
   - Tests unitaires simplifiés
   - Évolutivité assurée
   - Réutilisation du code

## 🔜 Pistes d'amélioration

1. **Tests**

   - Ajout de tests de performance
   - Tests end-to-end
   - Tests de sécurité automatisés

2. **Sécurité**

   - Implémentation de CSRF tokens
   - Validation plus poussée des entrées
   - Audit de sécurité complet

3. **Performance**

   - Cache des requêtes
   - Optimisation des requêtes SQL
   - Minification des assets

4. **Gestion des dépendances**
   - Utilisation de Composer pour toutes les dépendances PHP
   - Migration de PHPUnit et autres outils vers Composer
