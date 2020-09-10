# Roomies

App to make easier people's life at home.

Stack:
- Symfony 5.1
- Docker infra: PHP 7.4 + mariaDB + nginx

## Requirements

Please make sure you have the following software installed. If not, please, install them:

* [Docker](https://docs.docker.com/install/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Docker

Create .env file for Docker and modify it in case you need it:
```bash
cd infra/docker/local
cp dist.env .env
```

Build and start containers:
```bash
docker-compose build
docker-compose up -d
```

Build node container:
```bash
docker build -t roomies-node node/ --no-cache
```

## Database

Let's create the database for this project:
```bash
docker exec -t roomies-db mysql -e "CREATE DATABASE IF NOT EXISTS roomies"
docker exec -t roomies-db mysql -e "GRANT ALL ON roomies.* TO 'roomies'@'%' IDENTIFIED BY 'roomies'"
```

## App

Create Symfony .env file for your local environment:
```bash
cd </project/root>
cp .env .env.local
```

Install vendors via composer
```bash
docker exec -it roomies-php composer install
```

Install node dependencies
```bash
cd </project/root>
docker run -it -v $(pwd):/home/app roomies-node npm install
```

To access node container
```bash
docker run -it -v $(pwd):/home/app roomies-node bash
```

Create database schema
```bash
docker exec -it roomies-php bin/console doctrine:schema:create
```

Update your /etc/host file
```bash
sudo echo "127.0.0.1 roomies.loc" >> /etc/hosts
```

## Assets 

Build assets
```bash
docker run -it -v $(pwd):/home/app roomies-node yarn encore dev
```

Watch mode
```bash
docker run -it -v $(pwd):/home/app roomies-node yarn encore dev --watch
```

## Code quality
```bash
docker exec -it roomies-php bin/qualitify.sh
```


#### That's all!
 
Open your browser and go to:
* http://roomies.loc
