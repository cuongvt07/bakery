<div x-data="{ sidebarState: 2 }" class="flex-shrink-0 relative">
    
    <!-- Floating Open Button (Visible ONLY when Hidden) -->
    <button x-show="sidebarState === 0" 
            @click="sidebarState = 2"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-x-full"
            x-transition:enter-end="opacity-100 translate-x-0"
            class="absolute top-4 left-4 z-50 p-2 bg-indigo-800 text-white rounded-lg shadow-lg hover:bg-indigo-700 focus:outline-none"
            style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <aside :class="sidebarState === 2 ? 'w-64' : (sidebarState === 1 ? 'w-20' : 'w-0')" 
           class="bg-gradient-to-b from-indigo-800 to-indigo-900 text-white transition-all duration-300 overflow-hidden h-screen sticky top-0">
        
        <!-- Logo & Toggle -->
        <div class="flex items-center justify-between p-4 border-b border-indigo-700 h-16">
            <div x-show="sidebarState === 2" class="flex items-center whitespace-nowrap overflow-hidden transition-opacity duration-300">
                <svg class="w-8 h-8 text-yellow-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                </svg>
                <span class="ml-3 text-xl font-bold">Boong Cake</span>
            </div>
            
            <!-- Toggle Button -->
            <!-- Cycles: Expanded (2) -> Collapsed (1) -> Hidden (0) -->
            <button @click="sidebarState = (sidebarState === 2 ? 1 : 0)" 
                    class="p-1 rounded hover:bg-indigo-700 focus:outline-none ml-auto">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
        
        <!-- Navigation -->
        <nav class="mt-6 px-2 space-y-2">
            
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Dashboard">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Dashboard</span>
            </a>

            <!-- Dashboard Đại lý -->
            <a href="{{ route('admin.agencies.dashboard') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.agencies.dashboard') || request()->routeIs('admin.agencies.detail') || request()->routeIs('admin.agencies.note-types') || request()->routeIs('admin.agencies.locations') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Dashboard Đại lý">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Quản lý vật tư</span>
            </a>
            
            <!-- Quản lý Người dùng -->
            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.users.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Người dùng">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Người dùng</span>
            </a>
            
            <!-- Quản lý Điểm bán -->
            <a href="{{ route('admin.agencies.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.agencies.index') || request()->routeIs('admin.agencies.create') || request()->routeIs('admin.agencies.edit') || request()->routeIs('admin.agencies.notes.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Quản lý Điểm bán">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Điểm bán</span>
            </a>
            
            <!-- Quản lý Sản phẩm -->
            <a href="{{ route('admin.products.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.products.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Sản phẩm">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Sản phẩm</span>
            </a>

            <!-- Danh mục -->
            <a href="{{ route('admin.categories.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.categories.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Danh mục">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Danh mục</span>
            </a>
            
            <!-- Nguyên liệu -->
            <a href="{{ route('admin.ingredients.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.ingredients.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Nguyên liệu">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Nguyên liệu</span>
            </a>

            <!-- Nhà cung cấp -->
            <a href="{{ route('admin.suppliers.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.suppliers.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Nhà cung cấp">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Nhà cung cấp</span>
            </a>
            
            <!-- Divider -->
            <div x-show="sidebarState === 2" class="my-4 border-t border-indigo-700"></div>

            <!-- Công thức Sản xuất -->
            <a href="{{ route('admin.recipes.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.recipes.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Công thức Sản xuất">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Công thức</span>
            </a>

            <!-- Mẻ sản xuất -->
            <a href="{{ route('admin.production-batches.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.production-batches.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Mẻ sản xuất">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Mẻ sản xuất</span>
            </a>

            <!-- Báo cáo HSD -->
            <a href="{{ route('admin.expiry-report.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.expiry-report.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Báo cáo HSD">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Báo cáo HSD</span>
            </a>

            <!-- Phân bổ hàng -->
            <a href="{{ route('admin.distribution.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.distribution.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Phân bổ hàng">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Phân bổ hàng</span>
            </a>

            <!-- Chốt ca -->
            <a href="{{ route('admin.shift.closing') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors whitespace-nowrap overflow-hidden {{ request()->routeIs('admin.shift.*') ? 'bg-indigo-700 text-white' : 'hover:bg-indigo-700/50' }}"
               title="Chốt ca">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Chốt ca</span>
            </a>
            
            <!-- Divider -->
            <div x-show="sidebarState === 2" class="my-4 border-t border-indigo-700"></div>
            
            <!-- Ca làm việc (Coming soon) -->
            <a href="#" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors hover:bg-indigo-700/50 opacity-50 cursor-not-allowed whitespace-nowrap overflow-hidden"
               title="Ca làm việc (Sắp có)">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Ca làm việc (Sắp có)</span>
            </a>
            
            <!-- Báo cáo (Coming soon) -->
            <a href="#" 
               class="flex items-center px-4 py-3 rounded-lg transition-colors hover:bg-indigo-700/50 opacity-50 cursor-not-allowed whitespace-nowrap overflow-hidden"
               title="Báo cáo (Sắp có)">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Báo cáo (Sắp có)</span>
            </a>
            
        </nav>

        <!-- Logout -->
        <div class="absolute bottom-0 w-full border-t border-indigo-700 p-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex items-center w-full px-4 py-2 text-red-300 hover:text-white hover:bg-indigo-700 rounded-lg transition-colors whitespace-nowrap overflow-hidden" title="Đăng xuất">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span x-show="sidebarState === 2" class="ml-3 transition-opacity duration-300">Đăng xuất</span>
                </button>
            </form>
        </div>
        
    </aside>
</div>
