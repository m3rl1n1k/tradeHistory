# Symfony Docker installer

## About 
Symfony 6.3.*@dev
php-fpm 8.1
Mysql latest
nginx latest

## Installation
### Step 1
Copy `.env.install.dist` file to `.env.install`

Change `PROJECT_NAME` in `.env.install`

### Step 2
```console
docker-compose --env-file .env.install up --build
```
OR
```console
/bin/bash project.sh -i
```

This may take a few minutes. Wait for completion.

### Step 3
Profit

## Use

For use 
```console
docker-compose --env-file .env.local up
```
OR
```console
/bin/bash project.sh
```

for connect to php container with .env file in windows
```console
docker-compose --env-file .env.local exec -it "php" /bin/bash
```