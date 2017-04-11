Swing News API
==============

This is testing project. Use on your own risk!

###Requirements
- PHP 7.1
- MySQL 5.6+


To run the code use you may use Docker.
```bash
docker-compose up -d
```
or just setup the environment yourself.  
### Manage the composer dependencies.
```bash
composer install
```
or if you use locally
```bash
php composer.phar install
```
### Create .env file to store configuration
```bash
cp .env.example .env
```
Note that in case of Docker Compose use, `MYSQL_HOST=news-db`, mysql service is on different container.
### Run the Tests
```
make test
```