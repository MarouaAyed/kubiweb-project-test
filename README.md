# KUBIWEB – TEST SYMFONY 
## Maroua AYED – Fullstack Developer 

### Lancer le projet
Pour démarrer le projet, vous pouvez utiliser l'une des deux commandes suivantes :

1. Utiliser le serveur PHP intégré :
   ```bash
   php -S localhost:8000 -t public
   ```

2. Ou utiliser le serveur Symfony :
   ```bash
   symfony server:start
   ```

### Commandes de gestion de base de données

1. Créer une migration :
   ```bash
   php bin/console make:migration
   ```

2. Exécuter la migration pour mettre à jour la base de données :
   ```bash
   php bin/console doctrine:migrations:migrate
   ```

3. Charger les fixtures (données de test) :
   ```bash
   php bin/console doctrine:fixtures:load
   ```

4. Vider le cache :
   ```bash
   php bin/console cache:clear
   ```

### Partie 1 : Fonctionnalités CRUD

- **CRUD Produits**  
  Accéder à la liste des produits :  
  [http://localhost:8000/produits-list]
- **CRUD Fournisseurs**  
  Accéder à la liste des fournisseurs :  
  [http://localhost:8000/fournisseurs]

- **Gestion du stock de produits**  
  Voir le stock d'un produit :  
  [http://localhost:8000/produit/{id}/stock]
  (Remplacez `{id}` par l'identifiant du produit)

### Partie 2 : Gestion des commandes et inscription des clients

- **Formulaire d'inscription des clients** (avec gestion des erreurs)  
  [http://localhost:8000/inscription]

- **Liste des produits pour passer une commande**  
  [http://localhost:8000/produits]

- **Page de commande**  
  [http://localhost:8000/ma-commande]

- **Liste des commandes du client**  
  [http://localhost:8000/mes-commandes]

- **Détails d'une commande**  
  [http://localhost:8000/commande/details/{id}]
  (Remplacez `{id}` par l'identifiant de la commande)

### Partie 3 : API et Tests

   Documentation de l'API :  
   [http://localhost:8000/api/docs]

   Exécuter les tests unitaires pour valider les fonctionnalités :  
   ```bash
   php bin/phpunit
   ```

### Commandes pour rafraîchir la base de données en environnement de test

1. Supprimer et recréer le schéma de la base de données de test :
   ```bash
   php bin/console doctrine:schema:drop --env=test --force
   php bin/console doctrine:schema:create --env=test
   php bin/console doctrine:migrations:migrate --env=test
   php bin/console cache:clear --env=test
   php bin/console doctrine:fixtures:load --env=test
   ```

2. Rafraîchir la base de données en environnement de production :

   - Supprimer la base de données existante (si nécessaire) :
     ```bash
     php bin/console doctrine:database:drop --force --if-exists
     ```

   - Créer la base de données :
     ```bash
     php bin/console doctrine:database:create
     ```

   - Créer les migrations et appliquer :
     ```bash
     php bin/console make:migration
     php bin/console doctrine:migrations:migrate
     ```

   - Charger les fixtures :
     ```bash
     php bin/console doctrine:fixtures:load
     ```
```