<p align="center"><a href="https://diskominfo.pemkomedan.go.id" target="_blank"><img src="https://diskominfo.pemkomedan.go.id/img/logoo.png" width="400" alt="Laravel Logo"></a>
</p>

# API Documentation Diskominfo

## Introduction

The API Documentation Diskominfo project aims to provide detailed documentation for the API managed by Diskominfo.

## Installation

To set up the API Documentation Diskominfo project, please ensure that your system meets the following requirements:

-   Laravel 9
-   PHP 8.0 or higher
-   MySQL 5.7 or higher
-   Node 18 or higher
-   Composer 2.5 or higher

After ensuring the system requirements are met, follow these steps to set up the project:

1. Run the following commands in your project directory:

```bash
composer install
```

2. Copy the `.env.example` file and rename it as `.env`. Make sure to configure the `.env` file with the necessary
   settings.
3. Generate an application key by running the following command:

```bash
php artisan key:generate
```

4. Migrate the database tables by running the following command:

```bash
php artisan migrate
```

5. Don't forget to generate JWT secret for the application.

```bash
php artisan jwt:secret
```

6. Seed the database with initial data by running the following command:

```bash
php artisan db:seed
```

## Account

Administrator

Email: **admin@gmail.com**

Password: **password**
