# [OOT HANANOHI]
API for mobile APP
## Required

- PHP >= 7.3
- MySQL 5.7.x

## Architecture Overview

### Packaging Rules

List components in source code and explain it.

Example:

```
.
├──.ebextensions - ElasticBeanstalk Extension
├──.gitlab - Gitlab Merge request template
├──.platform - Config for ElasticBeanstalk deploy
├── app - contains the core code
│             ├── Console
│             ├── Enums - 
│             ├── Exceptions - contains the exception handling code
│             ├── Helpers - contains the helper functions
│             ├── Http
│             │             ├── Controllers
│             │             ├── Middleware - contains middleware mechanism, comprising the filter mechanism and communication between response and request
│             │             └── Requests - contains validate request logic
│             ├── Interfaces
│             ├── Mail - contains validate request logic
│             ├── Models - contains the email handling code
│             ├── Providers - contains all the service providers required to register events for core servers and to configure application
│             ├── Repositories - contains database logic function
│             ├── Services - contains database logic function
│             ├── Traits - contains all of the route definitions for application
│             └── Transformers - contains all of the route definitions for application
├── bootstrap - contains all the application bootstrap scripts
├── config - contains various configurations and associated parameters required for the application
├── database - contains database migrates, seeder
├── designs - contains all of documents explain about system or source code
├── resources - contains the files which enhances application
│             ├── lang - 
│             ├── views - contains view file which interact with end users
│             └── web-view - revisions of html webview
├── routes - contains all of the route definitions for application
├── storage
│             └── logs - Logging
├── tests
└── vendor

```

### Create packages

Guide to generate packages for projects

Example:

- Generate models

```bash
php artisan make:model User
```
## Rules
Define rules for steps of process as naming convention, filename convention...
Can split separate file, link to it
### Designs

### Database
[Database Design](./designs/database/erd.md)
### API
[API Design](./designs/api/openapi.yaml)
### Logging
[Logging Design](./docs/logging.md)

### Auth

### Cache Design

### Coding
※ Convnetion url, route, controller, function  name
| METHOD |URL                  |  CONTROLLER & FUNCTION NAME  |
|--------|---------------------|----------------------------|
| POST   | posts               | PostController@createPost  |
| GET    | posts/{postId}      | PostController@getDetail   |
| DELETE | posts/{postId}      | PostController@create      |
| POST   | posts/{postId}      | PostController@store       |
| GET    | posts/edit/{postId} | PostController@edit        |

-----Xử lý khác phương thức get post tùy chọn cho hợp lý----

### Git flow
- One branch - one feature - one backlog task
- Branch name format:
```console
task/{Backlog_ID}/{task-name}
```
- Trường hợp các task có liên quan đến nhau thì tạo 1 nhánh nhưng commit khác nhau, commit message lúc này bổ sung thêm backlog id
```console
feat: list user
feat: edit user
feat: delete user
```

## Get started

### Set up `app`

- Clone repository from git

```console
git clone git@git.hblab.vn:hb2/oot_hananohi/hana_api.git
```

- Run docker

```console
docker-compose up -d
```

- Install dependencies library

```console
docker-compose exec hana_app composer install
```

- Create environment file

```console
cp .env.example .env
```

- Generate application key

```console
docker-compose exec hana_app php artisan key:generate
```

- Update database connection info in environment file

```console
DB_HOST_READ=mysql
DB_HOST_WRITE=mysql
DB_DATABASE=oot_hana
DB_user=root
DB_PASSWORD=123456
```

- Migrate database

```console
docker-compose exec hana_app php artisan migrate
docker-compose exec hana_app php artisan db:seed
```

- Compile js & css

```console
docker-compose exec app npm run dev
```

- Finish open http://127.0.0.1/
### Run up app

Go back to `app` directory.

- Run up app:

```console
docker-compose up -d
```

- Stop app

```console
docker-compose down
```

## DB

Open http://localhost:8080/

## [Third-party service]
- l5-repository : https://github.com/andersao/l5-repository
- Laravel Sanctum : https://laravel.com/docs/8.x/sanctum

## Unit test(optional)

## Document page

- Backlog : https://hblab.backlogtool.com/projects/OOT_HANA
- Sonar : https://sonar.dev.hblab.vn/dashboard?id=HB2_OOT_HANANOHI_HANA_API
