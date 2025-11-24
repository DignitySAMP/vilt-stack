# VILT (Vue, Inertia, Tailwind and Laravel) stack

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.^-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.^-4FC08D?style=flat&logo=vue.js&logoColor=white" alt="Vue">
  <img src="https://img.shields.io/badge/Inertia.js-2.^-9553E9?style=flat&logo=inertia&logoColor=white" alt="Inertia">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.^-06B6D4?style=flat&logo=tailwind-css&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/Ziggy-2.^-fad710?style=flat&logo=reactrouter&logoColor=white" alt="Ziggy.js">
</p>

> Are you looking for a VILT template that has nothing more but empty, preconfigured boilerplate? Look no further. This is essentially the Laravel Breeze stack but completely up to date and without bloat.

## What is it?

This template is a fresh Laravel installation with Vue 3, and Tailwind set up out of the box. Inertia.js is configured to tie it all together with a clean ziggy.js or Wayfinder configuration for routes, depending on your stack choice. 

This template serves as the entry point for all of my web projects, and as such, gets maintained per personal use case. 

> If you're looking for this template with authentication provided out of the box, consider [using the default Laravel Vue starter kit](https://laravel.com/docs/12.x/starter-kits).

## Why another VILT stack?

There are already a few VILT stacks released but almost all of them implement some sort of bloat that might not be preferred. 

This one is pretty much empty, except for the following libraries:
- **Vue.js** version 3.^ (Composition API)
- **Inertia.js** version 2.^ pre-configured
- **Laravel** version (12.^) _(with PHPUnit and SQLite configured out of the box)_
- **Tailwind CSS**  version 4.^ (vite)
- **Ziggy.js** version 2.^ OR **Wayfinder** version 0.1.^.

## Project Structure

Vite is configured to inherit from `resources/js/` or `resources/ts/` depending on your stack choice (js/ts). Naturally you will want to create your `Layouts`, `Components`, `Stores` or `Composables` folders here depending on use case. 

The template only ships a single page, `@/pages/Welcome.vue`, to give you a clean starting point. This is the default Laravel 12 Welcome page with Vue, Inertia and Tailwind cards added.

Tailwind entry .css can be found at `@/css/app.css`. Inertia's middleware is configured according to it's documentation: `app/Http/Middleware/HandleInertiaRequests.php`.

If you use JavaScript, you can find all configuration and files inside the `/js/` folder. If you use TypeScript, it will default to `/ts/`.

## Installation (using installer)

This is meant to be installed on a fresh Laravel installation. As such:

```bash
laravel new [your-app-name]
composer require dignitysamp/vilt-stack
php artisan install:vilt
```

Important: currently only pnpm, npm and yarn are supported as package managers. If you choose TypeScript, **Wayfinder** will automatically be installed. If you choose JavaScript, **Ziggy.js** will be installed. 

### Note about Wayfinder:

You might want to update your .gitignore with Wayfinder types if you installed the TypeScript version:
```md
.... other .gitignore content ....

... at the end of the file:

/resources/ts/wayfinder
```

There is no reason to commit these files as they get auto generated every time `npm run x` is called.

## Manual installation

If you wish to install JavaScript, refer to the `./stubs/default/` folder. If you wish to install TypeScript, refer to the `./stubs/typescript/` folder.

Copy `./stubs/{stack}/app/Http/Middleware/HandleInertiaRequests.php` into your Laravel application's `app/Http/Middleware` folder.

Open your Laravel application's `bootstrap/app.php` file and add the Inertia middleware to the call chain:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        HandleInertiaRequests::class,
    ]);
})
```

Copy `./stubs/{stack}/resources` into your Laravel application root.
Delete `./resources/js/bootstrap.js` from your Laravel application.

Copy `./stubs/{stack}/views/app.blade.php` into your Laravel application's `./views/` folder.
Delete `./views/welcome.blade.php` from your Laravel application.

Laravel should install Tailwind by default, but to double check your Laravel application:

`./resources/css/app.css`:
```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';
@source '../**/*.blade.php';
@source '../**/*.ts';
@source '../**/*.vue';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';
}
```

If it looks like this, or at the very least includes `@import 'tailwindcss';` you should be good to go.