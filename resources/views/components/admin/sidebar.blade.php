<div x-data="{
    sidebarState: 2
}" class="flex-shrink-0">
    <!-- Smooth Animation Styles -->
    <style>
        /* x-cloak - hide elements until Alpine initializes */
        [x-cloak] {
            display: none !important;
        }

        details>div {
            animation: slideDown 0.3s ease-out;
            overflow: hidden;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        details summary svg:last-child {
            transition: transform 0.3s ease;
        }

        details[open] summary svg:last-child {
            transform: rotate(180deg);
        }

        button.fixed.top-4.left-4.z-50.p-2.bg-gray-900.text-white.rounded-xl.shadow-2xl.hover\:bg-gray-800.transition-all.animate-pulse.hover\:animate-none {
            background: rgb(0, 0, 0);
        }
    </style>

    <!-- Floating Open Button (only when completely hidden) -->
    <button x-show="sidebarState === 0" x-cloak @click="sidebarState = 2" x-transition
        class="fixed top-4 left-4 z-50 p-2 bg-gray-900 text-white rounded-xl shadow-2xl hover:bg-gray-800 transition-all animate-pulse hover:animate-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <aside :class="sidebarState === 2 ? 'w-64' : (sidebarState === 1 ? 'w-20' : 'w-0')"
        class="bg-white border-r border-gray-200 h-screen sticky top-0 flex flex-col shadow-sm transition-all duration-300 overflow-hidden">

        <!-- Logo & Toggle -->
        <div class="h-16 flex items-center border-b border-gray-100 flex-shrink-0"
            :class="sidebarState === 2 ? 'justify-between px-4' : 'justify-center'">
            <!-- Logo (Full mode only) -->
            <div x-show="sidebarState === 2" x-transition class="flex items-center">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z" />
                    </svg>
                </div>
                <span class="ml-3 text-lg font-bold text-gray-800">Boong Cake</span>
            </div>

            <!-- Toggle Button (show in icon mode and full mode, hide when completely hidden) -->
            <button x-show="sidebarState > 0"
                @click="sidebarState = sidebarState === 2 ? 1 : (sidebarState === 1 ? 0 : 2)"
                class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                :title="sidebarState === 2 ? 'Thu gọn (Icon)' : (sidebarState === 1 ? 'Ẩn hẳn' : 'Mở rộng')">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3" style="scrollbar-width: thin;">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" :title="sidebarState === 1 ? 'Dashboard' : ''"
                class="flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-50 text-amber-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                :class="sidebarState === 1 && 'justify-center'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span x-show="sidebarState === 2" x-transition class="ml-3">Dashboard</span>
            </a>

            <div x-show="sidebarState === 2" class="my-2 border-t border-gray-100"></div>

            <!-- Nhân sự -->
            <details class="mb-1 group" {{ request()->routeIs('admin.users.*', 'admin.departments.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span class="ml-3">Nhân sự</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.users.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Người dùng
                    </a>
                    <a href="{{ route('admin.departments.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.departments.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Phòng ban
                    </a>
                </div>
            </details>

            <!-- Icon-only: Nhân sự -->
            <a href="{{ route('admin.users.index') }}" x-show="sidebarState === 1" x-cloak title="Nhân sự"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.users.*', 'admin.departments.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </a>

            <!-- Đại lý -->
            <details class="mb-1 group" {{ request()->routeIs('admin.agencies.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="ml-3">Đại lý</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    <a href="{{ route('admin.agencies.dashboard') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.agencies.dashboard', 'admin.agencies.detail') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.agencies.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.agencies.index', 'admin.agencies.create', 'admin.agencies.edit') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">
                        Điểm bán
                    </a>
                </div>
            </details>

            <a href="{{ route('admin.agencies.dashboard') }}" x-show="sidebarState === 1" x-cloak title="Đại lý"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.agencies.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </a>

            <!-- Sản phẩm -->
            <details class="mb-1 group"
                {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.ingredients.*', 'admin.suppliers.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span class="ml-3">Sản phẩm</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.products.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Sản
                        phẩm</a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.categories.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Size
                        bánh</a>
                    <a href="{{ route('admin.ingredients.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.ingredients.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Nguyên
                        liệu</a>
                    <a href="{{ route('admin.suppliers.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.suppliers.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Nhà
                        cung cấp</a>
                </div>
            </details>

            <a href="{{ route('admin.products.index') }}" x-show="sidebarState === 1" x-cloak title="Sản phẩm"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.products.*', 'admin.categories.*', 'admin.ingredients.*', 'admin.suppliers.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
            </a>

            <!-- Quản lý Chung -->
            <details class="mb-1 group" {{ request()->routeIs('admin.materials.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                        </svg>
                        <span class="ml-3">Quản lý Chung</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    <a href="{{ route('admin.materials.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.materials.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Vật
                        tư + Nguyên liệu</a>
                </div>
            </details>

            <a href="{{ route('admin.materials.index') }}" x-show="sidebarState === 1" x-cloak title="Quản lý Chung"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.materials.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
            </a>

            <!-- Hàng hóa -->
            <details class="mb-1 group"
                {{ request()->routeIs('admin.production-batches.*', 'admin.recipes.*', 'admin.distribution.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span class="ml-3">Hàng hóa</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    <a href="{{ route('admin.production-batches.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.production-batches.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Mẻ
                        sản xuất</a>
                    <a href="{{ route('admin.recipes.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.recipes.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Công
                        thức</a>
                    <a href="{{ route('admin.distribution.index') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.distribution.*') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Phân
                        bổ hàng</a>
                </div>
            </details>

            <a href="{{ route('admin.production-batches.index') }}" x-show="sidebarState === 1" x-cloak
                title="Hàng hóa"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.production-batches.*', 'admin.recipes.*', 'admin.distribution.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </a>

            <details class="mb-1 group" {{ request()->routeIs('admin.shift.*') ? 'open' : '' }}
                x-show="sidebarState === 2">
                <summary
                    class="flex items-center justify-between px-3 py-2.5 rounded-lg cursor-pointer transition-all text-gray-700 hover:bg-gray-50 list-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3">Ca làm việc</span>
                    </div>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </summary>
                <div class="pl-8 mt-1 space-y-0.5 border-l-2 border-amber-200 ml-6">
                    {{-- <a href="{{ route('admin.shift.new') }}" class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.shift.new') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Quản lý Ca (Mới)</a> --}}
                    <a href="{{ route('admin.shift.management') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.shift.management') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Giám
                        sát Ca</a>
                    <a href="{{ route('admin.shift.reports') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.shift.reports') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Quản
                        lý Chốt Ca</a>
                    <a href="{{ route('admin.shift.requests') }}"
                        class="flex items-center px-3 py-2 rounded-md text-sm transition-all {{ request()->routeIs('admin.shift.requests') ? 'text-amber-700 bg-amber-50 font-medium' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}">Duyệt
                        yêu cầu</a>
                </div>
            </details>

            <a href="{{ route('admin.shift.management') }}" x-show="sidebarState === 1" x-cloak title="Ca làm việc"
                class="flex items-center justify-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.shift.*') ? 'bg-amber-50 text-amber-700' : 'text-gray-700 hover:bg-gray-50' }}">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>

            <div x-show="sidebarState === 2" class="my-2 border-t border-gray-100"></div>

            <!-- Quản lý Chấm công -->
            <a href="{{ route('admin.attendance.index') }}" :title="sidebarState === 1 ? 'Quản lý Chấm công' : ''"
                class="flex items-center px-3 py-2.5 mb-1 rounded-lg transition-all {{ request()->routeIs('admin.attendance.*') ? 'bg-amber-50 text-amber-700 font-medium' : 'text-gray-700 hover:bg-gray-50' }}"
                :class="sidebarState === 1 && 'justify-center'">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span x-show="sidebarState === 2" x-transition class="ml-3">Quản lý Chấm công</span>
            </a>
        </nav>

        <!-- Logout -->
        <div class="border-t border-gray-100 p-3 flex-shrink-0">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" :title="sidebarState === 1 ? 'Đăng xuất' : ''"
                    class="flex items-center w-full px-3 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition-all"
                    :class="sidebarState === 1 && 'justify-center'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span x-show="sidebarState === 2" x-transition class="ml-3 font-medium">Đăng xuất</span>
                </button>
            </form>
        </div>

    </aside>
</div>
