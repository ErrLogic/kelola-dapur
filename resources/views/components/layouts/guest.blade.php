<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Kelola Dapur">
    <meta name="theme-color" content="#faf7f2">
    <meta name="mobile-web-app-capable" content="yes">

    <title>{{ $title ?? 'Kelola Dapur' }}</title>

    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('icon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('icon.svg') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full overscroll-none bg-stone-50 text-stone-800 antialiased font-sans selection:bg-amber-200 selection:text-amber-900">
    <div class="min-h-full flex flex-col">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>
