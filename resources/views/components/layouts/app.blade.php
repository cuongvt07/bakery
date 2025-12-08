<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Boong Cake Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar (always visible, can be toggled) -->
        @if(auth()->check())
            <x-admin.sidebar />
        @endif
        
        <!-- Main Content -->
        <main class="flex-1 p-2">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
