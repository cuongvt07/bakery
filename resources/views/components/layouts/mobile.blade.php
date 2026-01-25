<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <title>{{ $title ?? 'Employee Portal' }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Mobile-first CSS */
        * {
            -webkit-tap-highlight-color: transparent;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            -webkit-font-smoothing: antialiased;
            padding-bottom: 70px;
            /* Space for bottom nav */
        }

        /* Bottom Navigation */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            background: white;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 50;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            height: 100%;
            text-decoration: none;
            color: #6b7280;
            transition: color 0.2s;
            font-size: 11px;
        }

        .nav-item.active {
            color: #4f46e5;
        }

        .nav-item svg {
            width: 24px;
            height: 24px;
            margin-bottom: 2px;
        }

        /* Large touch targets */
        .btn-mobile {
            min-height: 48px;
            padding: 12px 24px;
            font-size: 16px;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            transition: all 0.2s ease;
        }

        .btn-mobile:active {
            transform: scale(0.95);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
        }

        /* Pull to refresh indicator */
        .ptr-element {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: translateY(-50px);
            transition: transform 0.2s;
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Mobile Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
        <div class="px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth()->user()->ho_ten, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-900">{{ auth()->user()->ho_ten }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->ma_nhan_vien }}</div>
                </div>
            </div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <livewire:notification-component />
                <button onclick="document.getElementById('logout-form').submit()" class="text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="min-h-screen">
        {{ $slot }}
    </main>

    <!-- Bottom Navigation -->
    <nav class="bottom-nav">
        <a href="{{ route('employee.dashboard') }}"
            class="nav-item {{ request()->routeIs('employee.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span>Trang chủ</span>
        </a>

        <a href="{{ route('employee.shifts.schedule') }}"
            class="nav-item {{ request()->routeIs('employee.shifts.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Lịch làm</span>
        </a>

        <a href="{{ route('employee.materials') }}"
            class="nav-item {{ request()->routeIs('employee.materials.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Vật dụng</span>
        </a>

        <a href="{{ route('employee.shifts.check-in') }}"
            class="nav-item {{ request()->routeIs('employee.shifts.check-in') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
            </svg>
            <span>Check-in</span>
        </a>

    </nav>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    @livewireScripts
</body>

</html>
