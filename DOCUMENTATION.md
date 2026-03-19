# Documentation Technique — Mini-Rb

*Réalisé par Naima & Imane*

---

## 1. Présentation du projet

Mini-Rb est une application web inspirée d'Airbnb, permettant à des utilisateurs de publier des annonces de logements, effectuer des réservations, gérer les statuts de ces réservations, et laisser des avis.

---

## 2. Stack technique

| Couche | Technologie |
|---|---|
| Backend | PHP 8.x / Laravel 12 |
| Frontend | Blade + Tailwind CSS (CDN) |
| Base de données | SQLite (dev) / MySQL (prod) |
| Authentification | Manuelle via AuthController |
| Permissions | Laravel Policies + Middleware |
| Tests | PHPUnit via `php artisan test` |

---

## 3. Structure de la base de données

### Table `users`
| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| name | string | Nom de l'utilisateur |
| email | string | Email unique |
| password | string | Mot de passe hashé |
| role | string | `voyageur`, `hote`, ou `admin` |
| created_at | timestamp | Date d'inscription |

### Table `annonces`
| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| user_id | bigint | Clé étrangère → users |
| titre | string | Titre de l'annonce |
| description | text | Description du logement |
| adresse | string | Adresse complète |
| ville | string | Ville |
| prix_par_nuit | decimal | Prix par nuit en $ |
| nombre_de_chambres | integer | Nombre de chambres |
| image | string | Chemin vers l'image uploadée |

### Table `reservations`
| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| user_id | bigint | Clé étrangère → users (voyageur) |
| annonce_id | bigint | Clé étrangère → annonces |
| start_date | date | Date d'arrivée |
| end_date | date | Date de départ |
| total_price | decimal | Prix total calculé |
| status | enum | `pending`, `accepted`, `refused`, `cancelled` |

### Table `avis`
| Colonne | Type | Description |
|---|---|---|
| id | bigint | Clé primaire |
| reservation_id | bigint | Clé étrangère → reservations |
| user_id | bigint | Clé étrangère → users |
| rating | integer | Note de 1 à 5 |
| comment | text | Commentaire |

---

## 4. Rôles & Permissions

| Rôle | Capacités |
|---|---|
| `voyageur` | Parcourir les annonces, faire des réservations, laisser des avis |
| `hote` | Tout ce que voyageur peut faire + publier/modifier/supprimer ses annonces, accepter/refuser des réservations |
| `admin` | Tout ce que hote peut faire + accès au dashboard admin, supprimer n'importe quel utilisateur ou annonce |

Les permissions sur les annonces sont gérées via `AnnoncePolicy` (Laravel Policy).
L'accès admin est protégé par `AdminMiddleware`.

---

## 5. Routes principales

### Publiques
| Méthode | URL | Description |
|---|---|---|
| GET | `/` | Liste des annonces avec filtres |
| GET | `/annonces/{id}` | Détail d'une annonce |
| GET | `/register` | Page d'inscription |
| GET | `/login` | Page de connexion |

### Authentifiées
| Méthode | URL | Description |
|---|---|---|
| GET | `/annonces/create` | Formulaire création annonce |
| POST | `/annonces` | Créer une annonce |
| PUT | `/annonces/{id}` | Modifier une annonce |
| DELETE | `/annonces/{id}` | Supprimer une annonce |
| POST | `/annonces/{id}/reserver` | Créer une réservation |
| GET | `/mes-reservations` | Dashboard voyageur/hôte |
| PATCH | `/reservations/{id}/accept` | Accepter une réservation |
| PATCH | `/reservations/{id}/refuse` | Refuser une réservation |
| PATCH | `/reservations/{id}/cancel` | Annuler une réservation |
| POST | `/reservations/{id}/avis` | Laisser un avis |
| DELETE | `/avis/{id}` | Supprimer un avis |
| GET | `/admin` | Dashboard admin |
| DELETE | `/admin/users/{id}` | Supprimer un utilisateur |
| DELETE | `/admin/annonces/{id}` | Supprimer une annonce |

---

## 6. Logique métier

### Vérification de disponibilité
Avant toute réservation, le système vérifie qu'il n'existe pas de réservation existante (`pending` ou `accepted`) dont les dates se chevauchent avec les dates demandées. Si chevauchement détecté → erreur retournée à l'utilisateur.

### Calcul du prix total
```
total = nombre de nuits × prix_par_nuit
nombre de nuits = end_date - start_date (en jours)
```

### Cycle de vie d'une réservation
```
pending → accepted (par l'hôte)
pending → refused  (par l'hôte)
pending → cancelled (par le voyageur)
accepted → cancelled (par le voyageur)
```

### Conditions pour laisser un avis
- La réservation doit avoir le statut `accepted`
- L'utilisateur doit être le voyageur de cette réservation
- L'utilisateur ne doit pas avoir déjà laissé un avis pour cette réservation

---

## 7. Sécurisation

- Mots de passe hashés via `bcrypt` (Laravel Hash facade)
- Protection CSRF sur tous les formulaires (`@csrf`)
- Validation des données entrantes dans chaque Controller
- Policies Laravel pour les actions sur les annonces
- Middleware `auth` sur toutes les routes sensibles
- Index de base de données sur les clés étrangères fréquemment interrogées (`user_id`, `annonce_id`, `status`)

---

## 8. Tests

Les tests sont écrits avec PHPUnit et se trouvent dans `tests/Feature/`.

| Fichier | Modules couverts |
|---|---|
| `MiniRbModuleTest.php` | Auth, CRUD annonces, rôles, recherche/filtres |
| `ReservationAvisTest.php` | Réservations, disponibilité, statuts, avis |

Pour lancer tous les tests :
```bash
php artisan test
```

---

## 9. Installation locale
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```