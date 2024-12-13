# Mise en Place et Fonctionnement de l'Application

## 1.1. Changement de Base de Données

### Configurer la connexion à la base de données

Dans le fichier `.env`, modifiez la ligne `DATABASE_URL` pour qu'elle pointe vers votre base de données :

```dotenv
DATABASE_URL="postgresql://postgres:postgres@127.0.0.1:8889/VOTRE_DATABASE"
```

### Créer la base de données :
Si la base de données n'existe pas encore, exécutez la commande suivante pour la créer :
```dotenv
php bin/console doctrine:database:create
```

## 1.2 Lancer la migration
### Générer la migration : 
```dotenv
php bin/console make:migration
```

### Appliquer la migration : 
```dotenv
php bin/console doctrine:migrations:migrate
```

# Postman
Un fichier JSON est disponible à la source du projet.

# Routes de l'Application

## 1. Routes de la Gestion des Réservations

### 1.1. Récupérer toutes les réservations
- **Méthode** : GET
- **Route** : `/reservation`
  
### 1.2. Récupérer une réservation par son ID
- **Méthode** : GET
- **Route** : `/reservation/{id}`

### 1.3. Créer une réservation
- **Méthode** : POST
- **Route** : `/reservation`

### 1.4. Ajouter une réservation à un utilisateur
- **Méthode** : POST
- **Route** : `/reservation/user/{id}/{email}`

### 1.5. Supprimer une réservation
- **Méthode** : DELETE
- **Route** : `/reservation/{id}`

---

## 2. Routes de la Gestion des Utilisateurs

### 2.1. Connexion de l'utilisateur (Login)
- **Méthode** : POST
- **Route** : `/login`

### 2.2. Récupérer tous les utilisateurs
- **Méthode** : GET
- **Route** : `/user`

### 2.3. Créer un utilisateur
- **Méthode** : POST
- **Route** : `/user`

### 2.4. Supprimer un utilisateur
- **Méthode** : DELETE
- **Route** : `/user/{id}`

### 2.5. Mettre à jour les informations d'un utilisateur
- **Méthode** : PUT
- **Route** : `/user/{id}`

### 2.6. Récupérer les réservations d'un utilisateur
- **Méthode** : GET
- **Route** : `/user/reservation/{id}`

### 2.7. Récupérer un utilisateur par son ID
- **Méthode** : GET
- **Route** : `/user/{id}`