# VILT Stack Starter

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12+-FF2D20?style=flat&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Vue.js-3.5-4FC08D?style=flat&logo=vue.js&logoColor=white" alt="Vue">
  <img src="https://img.shields.io/badge/Inertia.js-2.2-9553E9?style=flat&logo=inertia&logoColor=white" alt="Inertia">
  <img src="https://img.shields.io/badge/Tailwind_CSS-4.0-06B6D4?style=flat&logo=tailwind-css&logoColor=white" alt="Tailwind">
</p>

A starter template combining Laravel, Vue 3, Inertia.js, and Tailwind CSS. Using vite for building and tooling. PHPUnit and SQLite configured out of the box. Intended for developers who want to skip the boilerplate and start building. 

This repository gets maintained per personal use case. Depending on the scale of my projects, it may take a few weeks or months for this to get updated. When in doubt, use `npm outdated` and `composer outdated` respectively to check. `npm update` and `composer update` to update. PR's for this are welcome.

## Stack

- **Laravel** - Backend framework
- **Vue.js** - Frontend framework with Composition API
- **Inertia.js** - Modern monolith architecture
- **Tailwind CSS** - Utility-first styling
- **Ziggy.js** - Laravel route helpers for Vue

## Installation

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate

# Start development servers
npm run dev          # In terminal 1
php artisan serve    # In terminal 2
```

## Project Structure

This template has minimal structure set up. Naturally you will want to create your `Layouts`, `Components`, `Stores` or `Composables` folders depending on use case.
```
app/
├── Http/Controllers/   # Inertia controllers
resources/
├── js/
│   ├── Pages/         # Inertia views
│   └── app.js         # Vue initialization
├── views/
│   └── app.blade.php  # Root template
routes/
└── web.php            # Routing
```

> Named routes `->name('')` are automatically compatible with ziggy.js in Vue by using `route('name')` and `route('name', property)`.

## Usage

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

But it also works in blade.php or html files:
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
