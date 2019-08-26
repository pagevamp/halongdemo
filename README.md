# Demo Project

This Project is setup using docker. 
We have followed Test Driven Development approach to minimize bugs. 
Php Cs Fixer is enabled to follow PHP coding standards as defined in PSR-2.

This Project is created for demo purposes and is missing many dependencies.

## Installation
* `git clone git@github.com:pagevamp/halongdemo.git`
* `cd halongdemo`
* `docker-compose up -d`
* `chmod -R 777 storage`
* `cp .env.example .env`
* `docker exec -i api.halongdemo.pv composer install`
* `docker exec -i api.halongdemo.pv php artisan migrate`
* `docker exec -i api.halongdemo.pv php artisan db:seed`


