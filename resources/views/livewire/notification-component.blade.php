<div x-data="{ 
    dropdownOpen: @entangle('showDropdown'),
    toastOpen: @entangle('showToast'),
    initToast() {
        this.$watch('toastOpen', value => {
            if (value) {
                setTimeout(() => {
                    this.toastOpen = false;
                    @this.closeToast(); // Sync back to PHP
                }, 5000);
            }
        })
    }
}" x-init="initToast()" wire:poll.5s="poll" class="relative">

    <!-- Bell Icon -->
    <button @click="dropdownOpen = !dropdownOpen" class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none">
        <span class="sr-only">View notifications</span>
        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        @if($unreadCount > 0)
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-1.5 py-0.5 text-[10px] font-bold leading-none text-white bg-red-600 rounded-full border-2 border-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
        @endif
    </button>

    <!-- Dropdown -->
    <div x-show="dropdownOpen" 
         @click.away="dropdownOpen = false"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden" 
         style="display: none;">
         
        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-sm font-medium text-gray-900">Thông báo</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-800">Đọc tất cả</button>
            @endif
        </div>

        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $status)
                <div wire:click="markAsRead({{ $status->id }})" class="px-4 py-3 hover:bg-gray-50 cursor-pointer flex items-start {{ !$status->da_doc ? 'bg-indigo-50' : '' }}">
                    <!-- Icon based on type -->
                    <div class="flex-shrink-0 mr-3 mt-1">
                        @if($status->thongBao->loai_thong_bao == 'canh_bao')
                            <span class="flex items-center justify-center h-8 w-8 rounded-full bg-yellow-100 ring-4 ring-white">
                                <svg class="h-4 w-4 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                        @elseif($status->thongBao->loai_thong_bao == 'he_thong')
                             <span class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 ring-4 ring-white">
                                <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        @else
                             <span class="flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 ring-4 ring-white">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            {{ $status->thongBao->tieu_de }}
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">
                            {{ $status->thongBao->noi_dung }}
                        </p>
                        <p class="text-[10px] text-gray-400 mt-1">
                            {{ $status->created_at->diffForHumans() }}
                        </p>
                    </div>
                    
                    @if(!$status->da_doc)
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600 ml-2 mt-2"></span>
                    @endif
                </div>
            @empty
                <div class="px-4 py-8 text-center text-gray-500 text-sm">
                    Không có thông báo nào
                </div>
            @endforelse
        </div>
    </div>

    <!-- Toast Notification (Global Top Overlay) -->
    <div x-show="toastOpen" 
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="-translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="-translate-y-full opacity-0"
         class="fixed top-0 left-0 right-0 z-[9999] pointer-events-auto shadow-xl"
         style="display: none;"
         x-data="{ 
             startY: 0, 
             currentY: 0,
             handleTouchStart(e) {
                 this.startY = e.touches[0].clientY;
             },
             handleTouchMove(e) {
                 this.currentY = e.touches[0].clientY;
                 if (this.startY - this.currentY > 30) { // Swipe Up detected
                     this.toastOpen = false;
                 }
             }
         }"
         @touchstart="handleTouchStart($event)"
         @touchmove="handleTouchMove($event)"
         >
        
        <div class="bg-white border-b-2 border-indigo-500 w-full flex items-center p-4 shadow-sm"
             :class="{
                'border-green-500': '{{ $toastType }}' == 'success',
                'border-yellow-500': '{{ $toastType }}' == 'warning',
                'border-red-500': '{{ $toastType }}' == 'error',
                'border-blue-500': '{{ $toastType }}' == 'info'
             }">
            
            <div class="flex-shrink-0 mr-3">
                @if($toastType == 'success')
                    <svg class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @elseif($toastType == 'warning')
                        <svg class="h-8 w-8 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                @elseif($toastType == 'error')
                    <svg class="h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @else
                    <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 line-clamp-2">
                    {{ $toastMessage }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Vuốt lên để ẩn</p>
            </div>

            <div class="ml-3 flex-shrink-0">
                <button @click="toastOpen = false" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
