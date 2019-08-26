# Demo Project

This Project is created only for demo. It contains only a small portion of code extracted from a separate project.
Please use this code base for reference purposes only. 

## Installation
* `git clone git@github.com:pagevamp/halongdemo.git`
* `cd halongdemo`
* `docker-compose up -d`
* `chmod -R 777 storage`
* `cp .env.example .env`
* `docker exec -i api.halongdemo.pv composer install`
* `docker exec -i api.halongdemo.pv php artisan migrate`
* `docker exec -i api.halongdemo.pv php artisan db:seed`


