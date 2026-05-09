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
<body class="h-full overscroll-none bg-stone-50 text-stone-800 antialiased font-sans selection:bg-amber-200 selection:text-amber-900"
      x-data="{ toasts: [] }"
      @toast.window="
          const id = Date.now();
          toasts.push({ id, message: $event.detail.message, type: $event.detail.type || 'success' });
          setTimeout(() => toasts = toasts.filter(t => t.id !== id), 3200);
      "
>
    {{-- Toast container --}}
    <div class="fixed top-0 left-0 right-0 z-[100] flex flex-col items-center pointer-events-none"
         style="padding-top: max(env(safe-area-inset-top, 0px), 12px);">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-y-3 scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                 x-transition:leave-end="opacity-0 -translate-y-3 scale-95"
                 class="pointer-events-auto mb-2 px-5 py-3 rounded-2xl shadow-lg backdrop-blur-xl text-sm font-medium max-w-[90vw]"
                 :class="{
                     'bg-stone-800/90 text-white': toast.type === 'success',
                     'bg-red-600/90 text-white': toast.type === 'error',
                     'bg-amber-500/90 text-white': toast.type === 'warning',
                 }">
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <div class="min-h-full flex flex-col">
        {{ $slot }}
    </div>

    @livewireScripts
</body>
</html>

