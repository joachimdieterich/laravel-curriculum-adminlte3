# Install

---

- [Step 1 - Clone curriculum from github](#section-1)
- [Step 2 - Configuration](#section-2)
- [Step 3 - Setup database](#section-3)
- [Step 4 - Start Server](#section-4)

<a name="section-1"></a>
## Step 1 - Clone curriculum from github

> To run this project, you must have PHP 7 installed as a prerequisite.

Begin by cloning this repository to your machine, and installing all Composer dependencies.

```bash
git clone https://github.com/joachimdieterich/laravel-curriculum-adminlte3.git
cd laravel-curriculum-adminlte3 && composer install

composer dump-autoload

npm install
npm run
```

<a name="section-2"></a>
## Step 2 - Configuration

Next, rename `.env.example` to `.env` create a new database and reference its name and username/password within the project's `.env` file. In the example below, we've named the database, "curriculum."

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=curriculum
DB_USERNAME=root
DB_PASSWORD=root
```

<a name="section-3"></a>
## Step 3 - Setup database

Migrate

```bash
php artisan key:generate
php artisan migrate --seed
``` 

<a name="section-4"></a>
## Step 4 - Start Server


### Start server on local environment

```bash
php artisan serve
``` 

### Deploy to production
[Laravel documentation] (https://laravel.com/docs/6.x/deployment)

<a name="section-5"></a>
## Step 5 - Login

Login:

username: admin@curriculumonline.de
password: password