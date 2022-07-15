# walk-of-art-back

## Accéder directement à l'api hébergé sur heroku

Notre api est disponible [ici](https://walk-of-art.herokuapp.com/api/docs)

## Guide d'installation pour le développement

### Installer la base du projet

Cloner le dépôt depuis https://github.com/quentingrchr/walk-of-art-back.git vers le dossier de l'application
```bash
git clone https://github.com/quentingrchr/walk-of-art-back.git [my-app-name]
```

Aller ensuite au dossier de l'api de l'application
```bash
cd [my-app-name]\app
```
Remplacer `[my-app-name]` avec le nom de dossier souhaité pour l'application.

### Installer le Docker

Utiliser cette commande pour installer l'environnement docker et directement le démarrer
```bash
docker-compose up -d
```

### La suite est à effectué directement dans le conteneur php :

### Installer les dépendances Composer

```bash
composer install
```

#### Générer keypair

Ensuite générer les clés privées/public jwt pour verifier/signer les jetons.
``` bash
php bin/console lexik:jwt:generate-keypair
```

### Installer les modules Node

Installer les modules
```bash
yarn install
```

Compiler les modules
```bash
yarn build
```

### Installer la base de données

Importer les tables de la base de données
```bash
php bin/console doctrine:migrations:migrate
```

### Configuré le smtp

Dé-commenter et éditer la variable d'env suivante dans le `.env` pour configurer le smtp
```bash
#MAILER_DSN=smtp://user:pass@smtp.example.com:25
```

## Tester l'api

### Accéder à l'api en local

Accéder à l'api à l'adresse suivante en local
http://localhost/api/docs
