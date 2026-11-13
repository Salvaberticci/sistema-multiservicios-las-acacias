<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Multiservicios Las Acacias')</title>
    <meta name="description" content="Sistema de gestión para Multiservicios Las Acacias">
    @include('layouts.partials.head')
    @stack('styles')
</head>
<body class="h-full font-sans antialiased bg-night-950">
    @yield('content')

    @yield('scripts')

    @include('layouts.partials.scripts')
</body>
</html>
