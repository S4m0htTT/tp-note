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