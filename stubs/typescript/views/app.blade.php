<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/ts/app.ts'])
    @routes
    @inertiaHead
</head>

<body class="antialiased">
    @inertia
</body>

</html>