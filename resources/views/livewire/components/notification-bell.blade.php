<div class="relative" x-data="{ open: @entangle('isOpen') }" wire:poll.10s="loadNotifications">
    {{-- Notification Bell Button --}}
    <button @click="open = !open" 
            class="relative p-2 rounded-full transition-colors active:scale-95 duration-200
            {{ $unreadCount > 0 ? 'text-indigo-600 bg-indigo-50 hover:bg-indigo-100' : 'text-gray-600 hover:bg-gray-100 hover:text-indigo-600' }}">
        
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" 
             class="w-6 h-6 {{ $unreadCount > 0 ? 'animate-bell-shake' : '' }}">
            <path fill-rule="evenodd" d="M5.25 9a6.75 6.75 0 0113.5 0v.75c0 2.123.8 4.057 2.118 5.52a.75.75 0 01-.297 1.206c-1.544.57-3.16.99-4.831 1.243a3.75 3.75 0 11-7.48 0 24.585 24.585 0 01-4.831-1.244.75.75 0 01-.298-1.205A8.217 8.217 0 005.25 9.75V9zm4.502 8.9a2.25 2.25 0 104.496 0 25.057 25.057 0 01-4.496 0z" clip-rule="evenodd" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute top-1 right-1 flex h-4 w-4">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 border-2 border-white items-center justify-center text-[9px] font-bold text-white leading-none">
                  {{ $unreadCount > 9 ? '9+' : $unreadCount }}
              </span>
            </span>
        @endif
    </button>

    {{-- Dropdown Menu --}}
    <div x-show="open" 
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-1 scale-95"
         class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden origin-top-right">
        
        {{-- Header --}}
        <div class="px-4 py-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-sm">Thông báo</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllAsRead" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium hover:underline">
                    Đánh dấu đã đọc
                </button>
            @endif
        </div>

        {{-- List --}}
        <div class="max-h-[350px] overflow-y-auto custom-scrollbar">
            @forelse ($notifications as $notificationStatus)
                @php
                    $notification = $notificationStatus->thongBao;
                    $isUnread = !$notificationStatus->da_doc;
                @endphp
                <div wire:click="markAsRead({{ $notificationStatus->id }})" 
                     class="p-4 border-b border-gray-50 hover:bg-gray-50 cursor-pointer transition-colors relative {{ $isUnread ? 'bg-indigo-50/30' : '' }}">
                    
                    @if($isUnread)
                        <div class="absolute right-4 top-4 w-2 h-2 rounded-full bg-blue-500"></div>
                    @endif

                    <div class="flex gap-3">
                        <div class="flex-shrink-0 mt-1">
                            @switch($notification->loai_thong_bao)
                                @case('he_thong')
                                    <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    @break
                                @case('canh_bao')
                                    <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7.75 4.25a.75.75 0 10-1.5 0 .75.75 0 001.5 0zM10 5a.75.75 0 01.75.75v6a.75.75 0 01-1.5 0v-6A.75.75 0 0110 5z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                            @endswitch
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-gray-900 {{ $isUnread ? 'font-bold' : '' }}">
                                {{ $notification->tieu_de }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">
                                {{ $notification->noi_dung }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <p class="text-sm">Chưa có thông báo nào</p>
                </div>
            @endforelse
        </div>
        
        {{-- Footer --}}
        @if(count($notifications) > 0)
            <div class="p-2 bg-gray-50 border-t border-gray-100 text-center">
                <a href="#" class="text-xs text-indigo-600 font-medium hover:underline">Xem tất cả</a>
            </div>
        @endif
    </div>
    <style>
        @keyframes bell-shake {
            0% { transform: rotate(0); }
            15% { transform: rotate(5deg); }
            30% { transform: rotate(-5deg); }
            45% { transform: rotate(4deg); }
            60% { transform: rotate(-4deg); }
            75% { transform: rotate(2deg); }
            85% { transform: rotate(-2deg); }
            100% { transform: rotate(0); }
        }
        .animate-bell-shake {
            animation: bell-shake 2s infinite;
            transform-origin: top center;
        }
    </style>
</div>
