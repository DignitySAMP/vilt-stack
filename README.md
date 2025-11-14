# Vue, Inertia, Tailwind and Laravel (VILT) template

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.^-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.^-4FC08D?style=flat&logo=vue.js&logoColor=white" alt="Vue">
  <img src="https://img.shields.io/badge/Inertia.js-2.^-9553E9?style=flat&logo=inertia&logoColor=white" alt="Inertia">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.^-06B6D4?style=flat&logo=tailwind-css&logoColor=white" alt="Tailwind">
  <img src="https://img.shields.io/badge/Ziggy-2.^-fad710?style=flat&logo=reactrouter&logoColor=white" alt="Ziggy.js">
</p>

> Are you looking for a VILT stack template that has is nothing more but a boilerplate? Look no further. This is essentially the Laravel Breeze stack but completely up to date and without bloat.

## What is it?

This template is a fresh Laravel installation with Vue 3, and Tailwind set up out of the box. Inertia.js is configured to tie it all together with a clean ziggy.js configuration for routes. 
> If you would like a version with TypeScript preinstalled and preconfigured, [click here](https://github.com/DignitySAMP/vilt-stack-ts).

This template serves as the entry point for all of my web projects, and as such, gets maintained per personal use case. 

> If you're looking for this template with authentication provided out of the box (using Laravel Fortify), [click here.](https://github.com/DignitySAMP/vilt-stack-fortify)

## Why another VILT stack?

There are already a few VILT stacks released but almost all of them implement some sort of bloat that might not be preferred. 

This one is pretty much empty, except for the following libraries:
- **Vue.js** version 3.^ (Composition API)
- **Inertia.js** version 2.^ pre-configured
- **Laravel** version (12.^) _(with PHPUnit and SQLite configured out of the box)_
- **Tailwind CSS**  version 4.^ (vite)
- **Ziggy.js** version 2.^

## Project Structure

Vite is configured to inherit from `resources/js/`. Naturally you will want to create your `Layouts`, `Components`, `Stores` or `Composables` folders here depending on use case. 

The template only ships a single page, `@/pages/Welcome.vue`, to give you a clean starting point. This is the default Laravel 12 Welcome page with Vue, Inertia and Tailwind cards added.

Tailwind entry .css can be found at `@/css/app.css`. Inertia's middleware is configured according to it's documentation: `app/Http/Middleware/HandleInertiaRequests.php`.

## Installation

```bash
laravel new --using=dignitysamp/viltstack
```

## Usage

> Named routes `->name('')` are automatically compatible in Vue by using `route('name')` and `route('name', property)` through **ziggy.js**.

### Creating Pages

Controllers return Inertia responses:

```php
use Inertia\Inertia;

return Inertia::render('Dashboard', [
    'user' => $user
]);
```

Vue components receive props automatically:

```vue
<script setup>
defineProps({
  user: Object
})
</script>
```

### Routing with Ziggy

Use Laravel route names in Vue:

```vue
<script setup>
import { router } from '@inertiajs/vue3'

const navigate = () => {
  router.visit(route('dashboard'))
}
</script>
```

### Styling

Tailwind 4 is configured with the Vite plugin. Use utility classes directly in Vue's template:

```vue
<template>
  <div class="flex items-center justify-center min-h-screen">
    <h1 class="text-4xl font-bold">Welcome</h1>
  </div>
</template>
```

But it also works in blade or html files:
```html
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
  </head>
  <body class="w-full h-full bg-neutral-500">
    @inertia
  </body>
</html>
```

### Shared props from backend to frontend
Share data across all pages using Inertia::share() in HandleInertiaRequests middleware at `app/Http/Middleware/HandleInertiaRequests.php`:
```php
public function share(Request $request): array
{
    return [
        ...parent::share($request),
        'auth' => [
            'user' => $request->user(),
        ],
        'flash' => [
            'success' => $request->session()->get('success'),
            'error' => $request->session()->get('error'),
        ],
    ];
}
```
