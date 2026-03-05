# Mini-Rb - Clone d'Airbnb (Projet Laravel)

**Mini-Rb** est une application web légère inspirée d'Airbnb, permettant aux utilisateurs de s'inscrire, de se connecter et de publier des annonces de logements. Ce projet a été conçu pour être simple, efficace et construit avec une architecture orientée PHP/Blade.

## 🚀 Fonctionnalités

- **Authentification complète** : Inscription, connexion et déconnexion sécurisées.
- **Gestion des Annonces** : 
  - Consultation de la liste des logements disponibles en page d'accueil.
  - Page de détails pour chaque annonce avec descriptif complet.
  - Formulaire de création d'annonce (Titre, Ville, Adresse, Prix, Chambres, Image).
- **Design Moderne** : Interface responsive et épurée utilisant **Tailwind CSS**.
- **Base de données** : Modèles Eloquent pour la gestion des utilisateurs et des annonces.

## 🛠️ Technologies utilisées

- **Backend** : [Laravel](https://laravel.com/) (Framework PHP)
- **Frontend** : Blade Templates & Tailwind CSS (via CDN)
- **Base de données** : MySQL / SQLite (selon configuration `.env`)
- **Version Control** : Git & GitHub

## 📂 Structure du Projet

Le projet suit l'architecture standard de Laravel avec quelques spécificités :
- `app/Http/Controllers/AuthController.php` : Logique d'authentification personnalisée.
- `app/Http/Controllers/AnnonceController.php` : Gestion CRUD des annonces.
- `resources/views/` : Dossiers `auth/` et `annonces/` contenant les templates Blade.
- `routes/web.php` : Définition de toutes les routes de l'application.

## ⚙️ Installation locale

1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/Imane-777/Mini-Rb.git
   cd Mini-Rb
   ```

2. **Installer les dépendances PHP** :
   ```bash
   composer install
   ```

3. **Configurer l'environnement** :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *N'oubliez pas de configurer vos accès à la base de données dans le fichier `.env`.*

4. **Lancer les migrations** :
   ```bash
   php artisan migrate
   ```

5. **Lancer le serveur de développement** :
   ```bash
   php artisan serve
   ```
   L'application sera accessible sur `http://127.0.0.1:8000`.

---
*Réalisé par Imane & Naima.*
