# laravel-wp-api
This is a fork of [https://github.com/Cyber-Duck/laravel-wp-api](https://github.com/Cyber-Duck/laravel-wp-api).
All credits go to [Cyber-Duck](https://github.com/Cyber-Duck).
Laravel 5 package for the [Wordpress JSON REST API](https://github.com/WP-API/WP-API) 

## Install

Simply add the following line to your `composer.json` and run install/update:

    "compareasiagroup/laravel-wp-api": "dev-master"

## Configuration

Publish the package config files to configure the location of your Wordpress install:

    php artisan vendor:publish

You will also need to add the service provider and optionally the facade alias to your `app/config/app.php`:

```php
'providers' => array(
  'CompareAsiaGroup\LaravelWpApi\LaravelWpApiServiceProvider'
)

'aliases' => array(
  'WpApi' => 'CompareAsiaGroup\LaravelWpApi\Facades\WpApi'
),
```

### Usage

The package provides a simplified interface to some of the existing api methods documented [here](http://wp-api.org/).
You can either use the Facade provided or inject the WpApi class.

#### Posts
```php
WpApi::posts($page);

```

#### Pages
```php
WpApi::pages($page);

```

#### Post
```php
WpApi::post($slug);

```

#### Categories
```php
WpApi::categories();

```

#### Tags
```php
WpApi::tags();

```

#### Category posts
```php
WpApi::category_posts($slug, $page);

```

#### Search
```php
WpApi::search($query, $page);

```

#### Archive
```php
WpApi::archive($year, $month, $page);

```

#### Credits

[Cyber-Duck](https://github.com/Cyber-Duck/laravel-wp-api)
[Laravel](http://laravel.com/)