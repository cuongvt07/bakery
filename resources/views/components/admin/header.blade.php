<header class="bg-white border-b border-gray-200 px-6 py-4">
    <div class="flex items-center justify-between">
        
        <!-- Page Title & Search -->
        <div class="flex items-center flex-1">
            <h1 class="text-2xl font-semibold text-gray-800">
                {{ $pageTitle ?? 'Dashboard' }}
            </h1>
        </div>
        
        <!-- Right Side: Search, Notifications, User Menu -->
        <div class="flex items-center space-x-4">
            
            <!-- Global Search -->
            <div class="relative">
                <input type="text" 
                       placeholder="Tìm kiếm..." 
                       class="w-64 px-4 py-2 pl-10 pr-4 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            
            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <!-- Badge -->
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    
                    <div class="px-4 py-3 border-b border-gray-200">
                        <h3 class="text-sm font-semibold text-gray-700">Thông báo</h3>
                    </div>
                    
                    <div class="max-h-96 overflow-y-auto">
                        <!-- Notification Item -->
                        <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm font-medium text-gray-900">Phiếu chốt ca mới</p>
                                    <p class="text-xs text-gray-500 mt-1">Điểm bán Quận 1 đã gửi phiếu chốt ca</p>
                                    <p class="text-xs text-gray-400 mt-1">5 phút trước</p>
                                </div>
                            </div>
                        </a>
                        
                        <!-- More notifications... -->
                    </div>
                    
                    <div class="px-4 py-3 border-t border-gray-200 text-center">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Xem tất cả</a>
                    </div>
                </div>
            </div>
            
            <!-- User Menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-3 p-2 hover:bg-gray-100 rounded-lg">
                    <img src="https://ui-avatars.com/api/?name={{ auth()->user()->ho_ten ?? 'Admin' }}&background=4F46E5&color=fff" 
                         alt="Avatar" 
                         class="w-8 h-8 rounded-full">
                    <div class="text-left hidden md:block">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->ho_ten ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-500">{{ auth()->user()->vai_tro ?? 'Quản trị viên' }}</p>
                    </div>
                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                
                <!-- Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Hồ sơ cá nhân
                        </div>
                    </a>
                    
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Cài đặt
                        </div>
                    </a>
                    
                    <div class="border-t border-gray-200"></div>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Đăng xuất
                            </div>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</header>
