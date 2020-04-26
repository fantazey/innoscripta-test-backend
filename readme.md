This repo is backend app with rest api for pizza shop
In `client` folder stored submodule with frontend application.


Development deploy:
- Install php dependencies `composer install`
- Install build dependencies `npm install`
- Install dependencies for `client` submodule using `npm install`
- Update .env file: set user and password for mysql database
- Run `php bin/console doctrine:database:create` to create database
- Run `php bin/console doctrine:migrations:migrate` to apply all necessary migrations
- Build frontend app with `npm run build` from this project root directory
 
 

 
