# curriculum 
[![Build Status](https://travis-ci.org/joachimdieterich/laravel-curriculum-adminlte3.svg?branch=master)](https://travis-ci.org/joachimdieterich/laravel-curriculum-adminlte3)
[![StyleCI](https://github.styleci.io/repos/184726557/shield?branch=master)](https://github.styleci.io/repos/184726557)

## Important Information
Work in progress - recoding curriculum in laravel - only use for test purposes 

curriculum is a learning platform where teachers can create topic-based learning objectives. The resulting curricula can be linked with learning groups and be viewed by learning group members. This will give students, teachers (and parents) a good overview of the objectives to be achieved. Not yet reached objectives are shown in red - if a objective is achieved, it will be shown in green or orange (if achieved with help). So curriculum provides a good overview of the current learning status. More information at http://www.curriculumonline.de 

## Installation

### Step 1.

> To run this project, you must have PHP 7 installed as a prerequisite.

Begin by cloining this repository to your machine, and installing all Composer dependencies.

```bash
git clone https://github.com/joachimdieterich/laravel-curriculum-adminlte3.git
cd laravel-curriculum-adminlte3 && composer install

composer dump-autoload

npm install
npm run
```

### Step 2. 

Next, rename `.env.example` to `.env` create a new database and reference its name and username/password within the project's `.env` file. In the example below, we've named the database, "curriculum."

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=curriculum
DB_USERNAME=root
DB_PASSWORD=root
```

### Step 3.

Migrate

```
php artisan key:generate
php artisan migrate --seed
``` 

### Step 4.

Start server

```
php artisan serve
``` 

### Step 5.

Login:

username: admin@curriculumonline.de
password: password

### Step 6.
Generate oAuth2 clients
```
php artisan passport:install
```
##
OpenApi Documentation Endpoint

Generate openApi Documentation
```
php artisan l5-swagger:generate
```

localhost:[port]/api/documentation

## Testing

### Feature and Unit Tests
```
./vendor/bin/phpunit
```

### Browser Tests
```
php artisan dusk
```

## Progress

* 20190504 adding grades table
* 20190504 adding subjects_type table
* 20190503 adding subjects table
* 20190503 adding organization_types table
* 20190503 states now use ISO 3166-2
* 20190503 countries now use ISO 3166-1
* 20190503 adding code of conduct, contributing and license
* 20190503 initial Commit
* 20190503 adding country and state table
