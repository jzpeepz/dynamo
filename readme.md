## Installation

Install via Composer:

`composer require jzpeepz/dynamo`

Include the service provider in your `config/app.php`:

`Jzpeepz\Dynamo\DynamoServiceProvider::class`

Publish the Dynamo config file:

`php artisan vendor:publish --tag=dynamo`

NOTE: If using a local disk for uploading, be sure to symlink it to your public directory and provide the proper path in the config file.

## Usage

### Generating your first admin

The following command will create a controller, model, migration, and route for your admin:

`php artisan make:dynamo Employee`

