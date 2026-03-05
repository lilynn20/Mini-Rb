# Mini-Rb - Clone d'Airbnb (Projet Laravel)

**Mini-Rb** est une application web légère inspirée d'Airbnb, permettant aux utilisateurs de s'inscrire, de se connecter et de publier des annonces de logements.

## 🚀 Fonctionnalités

- **Authentification complète** : Inscription, connexion et déconnexion sécurisées.
- **Gestion des Rôles & Permissions** : 
  - Rôles `client` et `admin`.
  - Seul le propriétaire d'une annonce ou un administrateur peut la modifier ou la supprimer.
- **Gestion des Annonces (CRUD)** : 
  - Consultation, création, modification et suppression des logements.
- **Design Moderne** : Interface responsive utilisant **Tailwind CSS**.

## 🛠️ Technologies utilisées

- **Backend** : [Laravel](https://laravel.com/)
- **Frontend** : Blade & Tailwind CSS (CDN)
- **Base de données** : MySQL / SQLite
- **Permissions** : Policies Laravel & Custom Middleware

## ⚙️ Installation locale

1. **Cloner le dépôt** :
   ```bash
   git clone https://github.com/Imane-777/Mini-Rb.git
   cd Mini-Rb
   ```

2. **Installer les dépendances** :
   ```bash
   composer install
   ```

3. **Configurer l'environnement** :
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Lancer les migrations** :
   ```bash
   php artisan migrate
   ```

5. **Lancer le serveur** :
   ```bash
   php artisan serve
   ```

---
*Réalisé par Imane & Naima.*
