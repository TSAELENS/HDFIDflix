# HDFIDflix

HDFIDflix est une application Symfony permettant de rechercher et consulter des films, séries et personnalités grâce à l’API TMDB.

## Fonctionnalités

- Affichage des films populaires
- Affichage des séries populaires
- Affichage des films les mieux notés
- Recherche de films, séries et personnes
- Classement des résultats par catégorie
- Consultation de la fiche détaillée d’un film ou d’une série
- Affichage du casting
- Affichage des bandes-annonces YouTube
- Affichage des plateformes de streaming, de location et d’achat
- Suggestions de contenus similaires
- Consultation de la biographie et de la filmographie d’une personne
- Interface responsive
- Gestion d’événements avec un listener et un subscriber
- Gestion des erreurs liées à l’API TMDB

## Technologies

PHP 8.5, Symfony 8.1, Twig, Bootstrap et API TMDB.

## Prérequis

Avant de lancer le projet, vous devez disposer de :

- PHP 
- Composer
- Symfony CLI
- Git
- Une clé d’API TMDB

## Installation

```bash
git clone https://github.com/TSAELENS/HDFIDflix.git
cd HDFIDflix
composer install
php bin/console importmap:install
```

Créez un fichier `.env.local` à la racine du projet :
```powershell
New-Item .env.local
```

```env
TMDB_API_TOKEN=votre_cle_api
```

Puis lancez l’application :

```bash
symfony serve
```

## Événements Symfony

Le projet utilise :

- un listener pour journaliser la consultation des fiches 
- un subscriber pour journaliser les recherches

## Vérifications

```bash
php bin/console lint:twig templates
php bin/console lint:container
```

## Tests

Les tests utilisent des réponses TMDB simulées et ne nécessitent ni token réel ni connexion à l’API.

Pour lancer les tests :

```bash
php bin/phpunit
```

## Auteur

Projet réalisé par **Théo Saelens**.

Les données sont fournies par TMDB et JustWatch.