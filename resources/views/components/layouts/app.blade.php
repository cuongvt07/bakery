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
        @if(auth()->check() && auth()->user()->vai_tro !== 'nhan_vien')
            <x-admin.sidebar />
        @endif
        
        <!-- Main Content -->
        <main class="flex-1 p-2">
            <!-- Header with Notification -->
            <div class="flex justify-end mb-4 px-4 sticky top-0 z-40">
                <livewire:notification-component />
            </div>

            {{ $slot }}
        </main>
    </div>
</body>
</html>

