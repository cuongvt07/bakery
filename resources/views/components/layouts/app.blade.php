<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Bakery System Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 text-white flex flex-col">
            <div class="p-4 text-center font-bold text-xl border-b border-gray-700">
                Bakery Admin
            </div>
            <nav class="flex-1 p-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.users.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-700' : '' }}">
                    Quản lý Người dùng
                </a>
                <a href="{{ route('admin.agencies.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.agencies.*') ? 'bg-gray-700' : '' }}">
                    Quản lý Điểm bán
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-700' : '' }}">
                    Danh mục sản phẩm
                </a>
                <a href="{{ route('admin.products.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-700' : '' }}">
                    Quản lý Sản phẩm
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.suppliers.*') ? 'bg-gray-700' : '' }}">
                    Quản lý Nhà cung cấp
                </a>
                <a href="{{ route('admin.ingredients.index') }}" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 {{ request()->routeIs('admin.ingredients.*') ? 'bg-gray-700' : '' }}">
                    Quản lý Nguyên liệu
                </a>
            </nav>
            <div class="p-4 border-t border-gray-700">
                <div class="flex items-center mb-4">
                    <div class="w-8 h-8 rounded-full bg-gray-500 flex items-center justify-center mr-2">
                        {{ substr(Auth::user()->ho_ten ?? 'A', 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-medium">{{ Auth::user()->ho_ten ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400">{{ Auth::user()->vai_tro ?? 'Staff' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 rounded text-sm">
                        Đăng Xuất
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
