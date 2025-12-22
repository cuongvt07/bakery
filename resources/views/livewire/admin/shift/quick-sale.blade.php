<div class="min-h-screen bg-gray-50 pb-80" x-data="{ wakeLock: null }" x-init="
    // Request Wake Lock to keep screen on
    if ('wakeLock' in navigator) {
        navigator.wakeLock.request('screen').then(lock => {
            wakeLock = lock;
            console.log('Screen wake lock activated');
        }).catch(err => {
            console.log('Wake lock error:', err);
        });
    }
">
    {{-- Sticky Header --}}
    <div class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 to-blue-700 text-white shadow-lg">
        <div class="px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if(auth()->check() && auth()->user()->vai_tro === 'nhan_vien')
                        <a href="{{ route('employee.dashboard') }}" class="text-white hover:text-blue-200 transition-colors">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                    @else
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    @endif
                    <div>
                        <h1 class="text-xl font-bold">POS</h1>
                        <p class="text-xs text-blue-100">{{ $shift->diemBan->ten_diem_ban ?? 'Điểm bán' }}</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2">
                    @if($pendingCount > 0)
                    <a href="/admin/pos/pending" class="relative">
                        <div class="bg-yellow-400 text-yellow-900 px-3 py-2 rounded-lg font-semibold text-sm flex items-center space-x-1 shadow-md hover:bg-yellow-300 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-xs">{{ $pendingCount }}</span>
                        </div>
                    </a>
                    @endif
                    
                    <a href="/admin/pos/confirmed" class="relative">
                        <div class="bg-white text-blue-600 px-3 py-2 rounded-lg font-semibold text-sm flex items-center space-x-1 shadow-md hover:bg-blue-50 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                            <span class="text-xs">Đã chốt</span>
                        </div>
                    </a>
                    
                    <a 
                        href="/admin/shift/closing?confirm_closing=1" 
                        @if($pendingCount > 0) 
                            onclick="event.preventDefault(); alert('Vui lòng chốt hết {{ $pendingCount }} đơn chờ trước khi chốt ca!'); return false;"
                        @endif
                        @class([
                            'px-4 py-2 rounded-lg font-semibold text-sm flex items-center space-x-2 shadow-md transition-colors',
                            'bg-red-500 hover:bg-red-600 text-white' => $pendingCount == 0,
                            'bg-gray-400 text-gray-200 cursor-not-allowed' => $pendingCount > 0
                        ])
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Chốt ca</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="mx-4 mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline font-semibold">✅ {{ session('success') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mx-4 mt-3 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline font-semibold">❌ {{ session('error') }}</span>
        </div>
    @endif

    {{-- Product List --}}
    <div class="px-4 py-6 space-y-4">
        @forelse($shiftDetails as $detail)
            @php
                $product = $detail['product'];
                $available = $detail['con_lai'];
                $cartQty = $cart[$detail['id']] ?? 0;
                
                // Color coding
                if ($available <= 0) {
                    $badgeColor = 'bg-red-100 text-red-800';
                    $stockText = 'Hết hàng';
                } elseif ($available <= 5) {
                    $badgeColor = 'bg-orange-100 text-orange-800';
                    $stockText = 'Còn ' . intval($available) . ' cái';
                } else {
                    $badgeColor = 'bg-green-100 text-green-800';
                    $stockText = 'Còn ' . intval($available) . ' cái';
                }
            @endphp
            
            <div class="bg-white rounded-2xl shadow-md p-4 {{ $available <= 0 ? 'opacity-60' : '' }}">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-800">{{ $product->ten_san_pham }}</h3>
                        <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-semibold {{ $badgeColor }}">
                            {{ $stockText }}
                        </span>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-blue-600">{{ number_format($product->gia_ban) }}đ</p>
                    </div>
                </div>

                {{-- Counter Buttons --}}
                <div class="flex items-center justify-between bg-gray-50 rounded-xl p-2">
                    <button 
                        wire:click="decrement({{ $detail['id'] }})"
                        @class([
                            'w-14 h-14 rounded-xl font-bold text-xl transition-all duration-150 active:scale-95',
                            'bg-red-500 text-white shadow-md hover:bg-red-600' => $cartQty > 0,
                            'bg-gray-300 text-gray-500 cursor-not-allowed' => $cartQty <= 0
                        ])
                        {{ $cartQty <= 0 ? 'disabled' : '' }}
                    >
                        −
                    </button>

                    <div class="flex-1 text-center">
                        <span class="text-3xl font-bold text-gray-800">{{ $cartQty }}</span>
                    </div>

                    <button 
                        wire:click="increment({{ $detail['id'] }})"
                        @class([
                            'w-14 h-14 rounded-xl font-bold text-xl transition-all duration-150 active:scale-95',
                            'bg-green-500 text-white shadow-md hover:bg-green-600' => $available > 0,
                            'bg-gray-300 text-gray-500 cursor-not-allowed' => $available <= 0
                        ])
                        {{ $available <= 0 ? 'disabled' : '' }}
                    >
                        +
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <svg class="w-20 h-20 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="mt-4 text-gray-500 font-medium">Chưa có sản phẩm nào trong ca</p>
            </div>
        @endforelse
    </div>

    {{-- Sticky Footer --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-4 border-blue-500">
        <div class="px-4 py-4">
            {{-- Payment Method --}}
            <div class="mb-4 bg-gray-50 rounded-lg p-3">
                <p class="text-xs text-gray-600 font-medium mb-2 text-center">PHƯƠNG THỨC THANH TOÁN</p>
                <div class="grid grid-cols-2 gap-2">
                    <button 
                        wire:click="$set('paymentMethod', 'tien_mat')"
                        @class([
                            'py-3 px-4 rounded-lg font-bold text-sm transition-all duration-150 active:scale-95 flex items-center justify-center gap-2',
                            'bg-green-600 text-white shadow-lg' => $paymentMethod === 'tien_mat',
                            'bg-gray-200 text-gray-700 hover:bg-gray-300' => $paymentMethod !== 'tien_mat'
                        ])
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        TIỀN MẶT
                    </button>
                    
                    <button 
                        wire:click="$set('paymentMethod', 'chuyen_khoan')"
                        @class([
                            'py-3 px-4 rounded-lg font-bold text-sm transition-all duration-150 active:scale-95 flex items-center justify-center gap-2',
                            'bg-blue-600 text-white shadow-lg' => $paymentMethod === 'chuyen_khoan',
                            'bg-gray-200 text-gray-700 hover:bg-gray-300' => $paymentMethod !== 'chuyen_khoan'
                        ])
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        CHUYỂN KHOẢN
                    </button>
                </div>
            </div>
            
            {{-- Total --}}
            <div class="mb-3 text-center">
                <p class="text-sm text-gray-600 font-medium">TỔNG CỘNG</p>
                <p class="text-4xl font-bold text-blue-600">{{ number_format($total) }}đ</p>
            </div>

            {{-- Buttons --}}
            <div class="grid grid-cols-2 gap-3">
                <button 
                    wire:click="checkout"
                    @if($total <= 0) disabled @endif
                    class="bg-gradient-to-r px-2 py-2 from-blue-600 to-blue-700 text-white font-bold rounded-xl text-lg shadow-lg hover:from-blue-700 hover:to-blue-800 active:scale-95 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    THANH TOÁN
                </button>

                <button 
                    wire:click="clearCart"
                    @if($total <= 0) disabled @endif
                    class="bg-red-500 px-2 py-2 text-white font-bold rounded-xl text-lg shadow-md hover:bg-red-600 active:scale-95 transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    XÓA
                </button>
            </div>
        </div>
    </div>

    {{-- Alert Component --}}
    <div 
        x-data="{ show: false, type: 'success', message: '' }"
        x-on:show-alert.window="
            show = true;
            type = $event.detail.type || 'success';
            message = $event.detail.message || '';
            setTimeout(() => show = false, 3000);
        "
        x-show="show"
        x-transition
        class="fixed top-20 left-4 right-4 z-50"
        style="display: none;"
    >
        <div 
            :class="{
                'bg-green-100 border-green-500 text-green-800': type === 'success',
                'bg-red-100 border-red-500 text-red-800': type === 'error',
                'bg-orange-100 border-orange-500 text-orange-800': type === 'warning'
            }"
            class="border-l-4 p-4 rounded-lg shadow-lg"
        >
            <p class="font-bold" x-text="message"></p>
        </div>
    </div>
</div>
