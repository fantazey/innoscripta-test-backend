# The Pizza

#### Test task for fullstack developer position

This repo is backend app with REST API
Stack: PHP7.2, Symfony 4

`client` submodule contains frontend application.
Stack: JS, React + Redux

Available for view/test [here](http://tmp.fantazey.ru)

Development deploy:
- Clone repo with `git clone --recurse-submodules git://......`
- Walk into dir with cloned repo
- Install php dependencies `composer install`
- Install build dependencies `npm install`
- Install dependencies for `client` submodule using `npm install` from `client` folder
- Execute `composer dump-var prod` and set db credential in root folder 
- Copy `client/config.js.dist` to `client/config.js` and fill in API URL for frontend application
- Run `php bin/console doctrine:database:create` to create database
- Run `php bin/console doctrine:migrations:migrate` to apply all necessary migrations
- Run `php bin/console init-menu` for initial setup db with some data
- Build frontend app with `npm run build` from this project root directory
- Use symfony built-in server or apache for run application 
 
 

 
