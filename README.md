# PREREQUIS
-> Xampp (avec PHP >=8.2) <br>
-> Git
-> Scoop (gestionnaire de packages, permettant d'automatiser l'installation et la maintenance d'outils) : 
```
iex (new-object net.webclient).downloadstring('https://get.scoop.sh')
```
-> Symfony-cli : 
```
scoop install symfony-cli
```
-> Composer (gestionnaire de dépendances PHP) : https://getcomposer.org/download/  <br>
-> NodeJS (pour les dépendances JS)  <br>
 <br>

# CONFIGURATION DU PROJET
<i>Les étapes suivantes sont juste indiquées à titre indicatif (ne pas les faire)</i> <br>
```
symfony new ressources_relationnelles --webapp
```
-> Installation de webpack qui nous permettra de compiler nos fichiers
```
composer require symfony/webpack-encore-bundle
```
-> Installation des dépendances JS
```
npm install
```
-> Installation du framework javascript
```
npm i @symfony/stimulus-bridge
```
-> Installation de l'ORM de symfony : 
```
composer require symfony/orm-pack
```
-> Création de compte par vérification mail (à configurer plus tard) : 
```
composer require symfonycasts/verify-email-bundle
```

# INSTALLATION DU PROJET
-> Installer les dépendances PHP : 
```
composer install
```
-> Installer les dépendances JS :  
```
npm install
```
-> Créer un fichier .env.local à la racine et rajouter les infos de connexion à la BDD : 
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/ressources_relationnelles?serverVersion=10.11.2-MariaDB&charset=utf8mb4"
```
-> Création de la BDD : 
```
php bin/console doctrine:database:create
```
-> Appliquer les migrations pour avoir la BDD à jour : 
```
php bin/console doctrine:migrations:migrate
```
-> Lancer le projet : 
```
symfony server:start
```
-> Compilation du js et scss : 
```
npm run watch
```

# STRUCTURE PROJET
## ASSETS (compilé dans public>build)
-> /Controllers : Code javascript <br>
-> /Styles : Code SCSS <br>

## PUBLIC
-> /assets/images : icônes et images utilisées pour le site <br>
-> /assets/ressources : fichiers json utilisés dans l'intégration des templates (ex: cgu du client) <br>
-> /assets/uploads : corresponds aux fichiers intégrés des ressources (à classer dans des dossiers comportant chacun l'id de sa ressource) <br>

<h2>TEMPLATES</h2>
-> base.html.twig : base de la vue <br>
-> /sections : composants global du projet <br>
