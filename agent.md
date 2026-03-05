# Instructions pour l'Agent - Mini rb (Airbnb Clone)

Ce projet est une application web simplifiée type Airbnb nommée "mini rb", développée avec Laravel.

## Contraintes Techniques
- **Langage Principal** : PHP uniquement.
- **Frontend** : Utilisation de Blade (moteur de template Laravel) avec Tailwind CSS via CDN. 
- **PAS de frameworks JS complexes** (Vue, React) ni de gestionnaires de paquets frontend (NPM/Yarn) pour les vues, afin de rester sur une architecture PHP pure.
- **Authentification** : Gérée manuellement via `AuthController` (pas de Laravel Breeze/Jetstream pour éviter les dépendances NPM).
- **Rôles & Permissions** : Système de rôles intégré (`client`, `admin`).
  - Middleware `AdminMiddleware` pour protéger les zones sensibles.
  - `AnnoncePolicy` pour restreindre la modification/suppression des annonces aux propriétaires ou aux administrateurs.

## Structure Actuelle
- **Modèles** :
  - `User` : Gère les utilisateurs et leurs rôles.
>>>>>>> SEARCH
  - `AuthController` : Inscription, Connexion, Déconnexion.
  - `AnnonceController` : Affichage de la liste, détails, et création d'annonces.
  - `AuthController` : Inscription, Connexion, Déconnexion.
  - `AnnonceController` : Gestion complète des annonces (CRUD) avec vérification des permissions.
  - `Annonce` : Gère les logements (titre, description, prix, ville, image URL).
- **Contrôleurs** :
  - `AuthController` : Inscription, Connexion, Déconnexion.
  - `AnnonceController` : Affichage de la liste, détails, et création d'annonces.
- **Routes** : Définies dans `routes/web.php`.
- **Vues** : Situées dans `resources/views/`, utilisant Tailwind CSS.

## Prochaines étapes suggérées
1.  **Système de Réservation** : Implémenter la logique dans `AnnonceController` ou un nouveau `ReservationController` pour permettre aux utilisateurs de réserver des dates.
2.  **Espace Utilisateur** : Créer une page "Mes Annonces" et "Mes Réservations".
3.  **Gestion des Images** : Permettre l'upload de fichiers locaux au lieu d'URLs externes.
4.  **Recherche** : Ajouter une barre de recherche par ville sur la page d'accueil.

## Commandes Utiles
- `php artisan serve` : Lancer le projet.
- `php artisan migrate` : Mettre à jour la base de données.
