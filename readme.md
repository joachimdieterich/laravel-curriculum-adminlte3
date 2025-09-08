# curriculum 
[![Build Status](https://api.travis-ci.com/joachimdieterich/laravel-curriculum-adminlte3.svg?branch=master)](https://travis-ci.org/joachimdieterich/laravel-curriculum-adminlte3)
[![StyleCI](https://github.styleci.io/repos/184726557/shield?branch=master)](https://github.styleci.io/repos/184726557)
 
curriculum is a learning platform where teachers can create topic-based learning objectives. The resulting curricula can be linked with learning groups and be viewed by learning group members. This will give students, teachers (and parents) a good overview of the objectives to be achieved. Not yet reached objectives are shown in red - if a objective is achieved, it will be shown in green or orange (if achieved with help). So curriculum provides a good overview of the current learning status. More information at https://rlp.curriculumonline.de/features 

## Installation

### Laravel
- laravel Ver. 11

### Prerequisites
- PHP 8.2 Extensions: xml, dom, zip, curl, mbstring, bcmath, gd, mysqli, PDO, tokenizer, openssl, fileinfo, ctype, cli, common, opcache, readline
- ghostscript
- imagemagick
- git
- composer
- npm
- node (>=22.12.0)

### Step 1.

Begin by cloning this repository to your machine, and installing all Composer dependencies.
Make sure that your system is up-to-date.
```bash
sudo apt-get update
```

```bash
git clone https://github.com/joachimdieterich/laravel-curriculum-adminlte3.git
cd laravel-curriculum-adminlte3 
```
For production:
```bash
composer install --no-dev
npm install
npm update
npm run production
```
For development:
```bash
composer install
npm install
npm update
npm run build # needed to copy some files into /public
npm run dev
```

### Step 2. 

Next, rename `.env.example` to `.env` 

```bash
cp .env.example .env
```

Create a new database and reference its name and username/password within the project's `.env` file. In the example below, we've named the database, "curriculum."
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=curriculum
DB_USERNAME=root
DB_PASSWORD=root
```

For production change
```
APP_ENV=production
APP_DEBUG=false
```

Information about configurating `.env` in documentation `localhost:[port]/documentation`

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
The Password grant client can be used to access the API.

## Accessing API

### Getting access_token
```
POST https://localhost:[port]/oauth/token
header
Content-Type: 'application/json' 

form-data
grant_type: "client_credentials",
client_id: [id]
client_secret: [secret]
```
### Use access token to access API
```
e.g. GET 'http://localhost:[port]/api/v1/users' 

header 
Content-Type: 'application/json' 
X-Requested-With: 'XMLHttpRequest' 
Authorization: 'Bearer [access_token]'
``` 

## OpenApi Documentation Endpoint
 
Generate OpenApi Documentation
```
php artisan l5-swagger:generate
```

localhost:[port]/api/documentation
Info: If you want to use another host change L5_SWAGGER_CONST_HOST in `.env`


To use the following metadata endpoints, you have to set `password=[pw]` has to be set.
```
/v1/curricula/{curriculum}/metadataset?password={password}
/v1/curricula/metadatasets?password={password}
```
Route::get('curricula/{curriculum}/metadataset', 'CurriculaApiController@getSingleMetadataset');
The password is stored in configs table key = 'metadata_password', data_type = 'string'. (Use http://localhost:8000/configs/create)

## SSO with SAML2
Curriculum uses [aacotroneo/laravel-saml2](https://github.com/aacotroneo/laravel-saml2) to integrate a SP (service provider).

Set up the `.env` to get SSO working. Example:
```
SAML2_RLP_IDP_HOST=https://{idpUrl}
SAML2_RLP_IDP_ENTITYID=https://{idpUrl}/idp/SAML2/METADATA
SAML2_RLP_IDP_SSO_URL=https://{idpUrl}/idp/SAML2/REDIRECT/SSO
SAML2_RLP_IDP_SL_URL=https://{idpUrl}/idp/SAML2/REDIRECT/SLO
SAML2_RLP_IDP_x509=IDPcertificate

SAML2_RLP_SP_x509=SP certificate
SAML2_RLP_SP_PRIVATEKEY=privatekey

SAML2_RLP_IDP_CONTACT_NAME=name
SAML2_RLP_IDP_CONTACT_EMAIL=email

SAML2_RLP_IDP_ORG_NAME=org name
SAML2_RLP_IDP_ORG_URL=some url

SAML2_LOGOUT_ROUTE=some url
SAML2_LOGIN_ROUTE=some url
SAML2_ERROR_ROUTE=some url

```

Further Settings are found in `config\saml2\rlp_idp_settings.php` and `config\saml2_settings.php`

You also have to set up your IDP. The following routes will help you:
```
http://{curriculumUrl}/saml2/{idpName}/acs
http://{curriculumUrl}/saml2/{idpName}/login
http://{curriculumUrl}/saml2/{idpName}/logout
http://{curriculumUrl}/saml2/{idpName}/metadata
http://{curriculumUrl}/saml2/{idpName}/sls
```
More information at [aacotroneo/laravel-saml2](https://github.com/aacotroneo/laravel-saml2)


## Enabling guest login
By default the guest user is seeded with ID 8.
To enable login (over login page or route ```"/guest"```) add ```GUEST_USER=8``` to ```.env```


If the organization of the guest user has a navigator, he will be redirected to the first view of this navigator. 
If there is no navigator, he is redirected to the dashboard.



## Generating PDFs (Certificates,...)
Curriculum uses [barryvdh/laravel-snappy] (https://github.com/barryvdh/laravel-snappy)

Check that wkhtmltopdf binaries are present. (Further information on [barryvdh/laravel-snappy] (https://github.com/barryvdh/laravel-snappy))
Binaries for linux are included in the package, those for macs can be found under [profburial/wkhtmltopdf-binaries-osx] (https://github.com/profburial/wkhtmltopdf-binaries-osx)

Set up the `.env` to get it working. Example:
```
SNAPPY_PDF_BINARY="/absolute_path_to/vendor/bin/wkhtmltopdf-amd64-osx"
SNAPPY_IMAGE_BINARY="/absolute_path_to/vendor/bin/wkhtmltoimage-amd64-osx"
```

Now you can generate PDFs. Example: 
```
return SnappyPdf::loadFile('http://curriculumonline.de')->inline('cur.pdf');
```

## Testing

### Feature and Unit Tests
```
./vendor/bin/phpunit
```

### Browser Tests (Dusk)
Important: Start server in dusk environment.

```
php artisan config:clear
php artisan serve --env=dusk.testing
```

Run browser tests

```
php artisan dusk
```

### Browser Tests (Cypress)
add `.env.cypress` to use alternative config (eg. DB)


Run browser tests (see package.json)
```
npm run test:cypress
```

### Websockets
Add/Edit the following lines to .env

```
BROADCAST_CONNECTION=reverb
REVERB_APP_ID="${APP_NAME}"
REVERB_APP_KEY="${APP_KEY}"
REVERB_APP_SECRET=secret

REVERB_HOST="${APP_HOSTNAME}"
REVERB_PORT=6001
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"

REVERB_SERVER_HOST="${REVERB_HOST}"
REVERB_SERVER_PORT="${REVERB_PORT}"

WEBSOCKET_APP_ACTIVE=true
VITE_WEBSOCKET_APP_ACTIVE="${WEBSOCKET_APP_ACTIVE}"
```

Start Reverb-Server with `sudo php artisan reverb:start`.

If you use another queue then **sync** you have to start a listener.

You can do that with `php artisan queue:listen` to listen to the **default** queue.

#### Know Issues
When using the **sync**-queue, model events will be broadcasted/handled differently.
For example, the **created** model event won't be either fired by **laravel** or handled by **echo**.
An additional example would be the **update**-event of an model (e.g. Kanban). It will return old information if a model
with relation (e.g. KanbanStaus) get updated.
But IF you use the **database**-queue everything works perfect.

Further information [laravel-websockets](https://laravel.com/docs/11.x/broadcasting)

### Documentation
Curriculum uses [saleem-hadad/larecipe](https://github.com/saleem-hadad/larecipe) to provide integrated project documentation. 
The documentation can be found at the following URL `localhost:[port]/documentation`

### Roles and Permissions
The initial installation has 8 Roles: 
- Administrator
- Creator
- Indexer
- Schooladmin
- Teacher
- Student
- Parent
- Guest

The [Permission-Map](permissionmap.md) gives a quick view over the permissions of those roles.

### Artisan commands

#### References between curricula

To refresh curriculum references (referencing_curriculum_id on terminal- and enablingObjectives) based on
reference_subscriptions and quote_subscriptions use the following command.

```bash
php artisan objectives:refreshReferences
```

#### Generate metadataset

Metadatasets can be created via ```/metadatasets``` in the frontend. Use API-Endpoint ```/v1/curricula/metadatasets```
to get metadataset of all curricula (with type_id == 1). If you want to secure this endpoint with a
password ```/v1/curricula/metadatasets?password={password}``` uncomment code in CurriculaApiController.php,
getAllMetadatasets()

Alternative metadataset-creation:

```bash
php artisan curriculum:metadataset 001
```

### Further information

If ```npm install``` fails with: ```Failed at the admin-lte@3.0.5 install script 'npm run plugins'.``` update node.

Clear NPM's cache:
```sudo npm cache clean -f```
Install a little helper called 'n'
```sudo npm install -g n```
Install latest stable Node.js version
```sudo n stable```

### Set status-filter for telescope
```
TELESCOPE_STATUS_FILTER="200,302"
```
Given HTTP-Codes will NOT be shown inside telescope

### json-fields in MariaDB

If migrations failing with the following message (MariaDB Version < 10.2.7): 


```
SQLSTATE[42000]: Syntax error or access violation: 1064 You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'json null' at ...
```
Then change all ```json-fields``` in migrations to ```text``` 

```php
$table->json('variants')->nullable(); // e.g. in 2022_10_01_154624_add_variant_column_to_curricula_table.php 

to 

$table->text('variants')->nullable(); 
```

### Logo for embededEvent.vue
Put ```logo.png``` in the following path: ```public/favicons/logo.png```
