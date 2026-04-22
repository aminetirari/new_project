# NutriMind

Application PHP (MVC) de gestion de nutrition, repas, planification et blog
(posts & commentaires), prévue pour tourner sous **XAMPP**.

## Installation sous XAMPP

1. **Cloner** le repo dans `htdocs` :
   ```bash
   cd /path/to/xampp/htdocs
   git clone https://github.com/aminetirari/new_project.git nutrimind
   ```
2. **Lancer** Apache et MySQL depuis le panneau XAMPP.
3. **Créer la base** dans phpMyAdmin : créer une base nommée `nutrimind`, puis
   importer `database_setup.sql` (crée `posts` + `comments` et insère un
   jeu d'essai). Prérequis : la table `user` existe déjà (créée par le reste
   de l'application).
4. **Accéder à l'application** :
   - Front : http://localhost/nutrimind/views/index.php
   - Blog : http://localhost/nutrimind/views/posts_list.php
   - Admin : http://localhost/nutrimind/views/backoffice/index.php
     (nécessite un utilisateur avec `role = 'admin'`)

## Connexion MySQL

Configurée dans `config/Database.php` :
- host : `localhost`
- base : `nutrimind`
- user : `root`
- password : *(vide)*

## Arborescence

```
config/        # Configuration DB
controllers/   # Contrôleurs PHP (ex. PostController)
models/        # Modèles PDO (Post, Comment, User, ...)
views/         # Vues front-office
views/backoffice/  # Interface admin
database_setup.sql # Schéma posts/commentaires + seed
```

## Fonctionnalités Blog (posts & commentaires)

- **Front-office**
  - Liste des posts (`views/posts_list.php`) avec compteur de commentaires
  - Détail d'un post (`views/post.php`) : contenu + fil de commentaires
  - Ajout de commentaire (utilisateur connecté)
  - Suppression de son propre commentaire (ou de n'importe quel commentaire pour un admin)
- **Back-office admin** (`views/backoffice/`)
  - Liste des posts avec actions modifier/supprimer
  - Création d'un post
  - Édition d'un post
  - Suppression (cascade sur les commentaires)
