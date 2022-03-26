# Zephyr

This package is a Laravel starter kit with auth scaffolding using Livewire & Bootstrap.

It was created for people who prefer using Livewire & Bootstrap over Inertia/React/Vue/Alpine/Tailwind. It is an alternative to Breeze/Jetstream that is very slim. All you have to do is run the `zephyr:install` command to get complete auth scaffolding for login, register, password resets, email verification, profile updating, & more.

## Requirements

You must have NPM installed on your machine in order to install this package, as it runs `npm install && npm run dev` during installation.

## Installation

This package as designed to be used with new Laravel projects.

First, create a new Laravel project via Composer/Docker/whatever you prefer:

```console
laravel new my-app
```

Configure your `.env` file with your app, database, and mail settings:

```env
APP_*
DB_*
MAIL_*
```

Require this package via Composer:

```console
composer require legodion/zephyr
```

Now install Zephyr:

```console
php artisan zephyr:install
```

This will create the Livewire components, add necessary resources, run the NPM commands, etc.

## Usage

All of the login, registration, password reset functionality, etc. works out of the box. If you would like to add password confirmation and/or email verification, there are a couple of extra steps you need to take.

### Password Confirmation

Just add the `password.confirm` middleware to any route you want to require password confirmation:

```php
public function route()
{
    return Route::get('/home')
        ->name('home')
        ->middleware('auth', 'password.confirm');
}
```

### Email Verification

Implement the `MustVerifyEmail` contract in your `User` model class:

```php
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    // ...
```

Then add the `verified` middleware to any route that requires email verification to be complete:

```php
public function route()
{
    return Route::get('/update-profile')
        ->name('profile.update')
        ->middleware('auth', 'verified');
}
```
