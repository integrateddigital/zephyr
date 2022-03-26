<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@hasSection('title') @yield('title') | @endif {{ config('app.name') }}</title>

    <link rel="icon" href="{{ mix('images/favicon.png') }}">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    <livewire:styles/>
</head>
<body>
    <livewire:layouts.navbar/>

    <main class="container my-4">
        {{ $slot }}
    </main>

    <script src="{{ mix('js/app.js') }}" defer></script>
    <livewire:scripts/>
</body>
</html>
