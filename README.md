# htl-workshop

## Setup and run the project locally

### Setup environment

Fill in the environment variable with your values.

````text
APP_SECRET=...
ALANCAPTCHA_API_KEY=...
````

### Install dependencies and run app

````bash
docker compose -f compose.development.yaml up
docker exec -it htl_workshop_dev_web bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
````
