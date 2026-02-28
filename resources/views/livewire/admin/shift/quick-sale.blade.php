<div class="min-h-screen bg-gray-50 pb-80" x-data="{ wakeLock: null }" x-init="(() => {
    // Request Wake Lock to keep screen on
    if ('wakeLock' in navigator) {
        navigator.wakeLock.request('screen').then(lock => {
            wakeLock = lock;
            console.log('Screen wake lock activated');
        }).catch(err => {
            console.log('Wake lock error:', err);
        });
    }
})()"
x-on:copy-to-clipboard.window="
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText($event.detail.text).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Lỗi copy clipboard: ' + err);
        });
    } else {
        const textArea = document.createElement('textarea');
        textArea.value = $event.detail.text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-9999px';
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
        } catch (err) {
            console.error('Fallback copy failed', err);
            alert('Lỗi copy clipboard (fallback)');
        }
        document.body.removeChild(textArea);
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
                    <div class="flex items-center">
                        <div>
                            <h1 class="text-xl font-bold">POS</h1>
                            <p class="text-[10px] text-blue-100">{{ Str::limit($shift->diemBan->ten_diem_ban ?? 'Điểm bán', 15) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-1.5 justify-end">
                    <button 
                        wire:click="openUpdateStockModal"
                        class="px-2 py-1.5 rounded bg-indigo-500 hover:bg-indigo-600 text-white font-semibold flex items-center shadow-md transition-colors"
                    >
                        <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span class="text-[10px] hidden sm:inline">Nhập SL</span>
                    </button>

                    <button 
                        wire:click="copyStockList"
                        class="px-2 py-1.5 rounded bg-white/20 hover:bg-white/30 text-white font-semibold flex items-center shadow-md transition-colors"
                    >
                        <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        <span class="text-[10px] hidden sm:inline">Copy tồn</span>
                    </button>
                    
                    <a href="{{ auth()->user()->vai_tro === 'nhan_vien' ? route('employee.pos.pending') : route('admin.pos.pending') }}">
                        <div class="{{ $pendingCount > 0 ? 'bg-yellow-400 text-yellow-900' : 'bg-white/20 text-white' }} px-2 py-1.5 rounded font-semibold flex items-center shadow-md hover:bg-yellow-300 transition-colors">
                            <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-[10px]">{{ $pendingCount > 0 ? $pendingCount : '' }}<span class="hidden sm:inline"> Chờ</span></span>
                        </div>
                    </a>
                    
                    <a 
                        href="{{ auth()->user()->vai_tro === 'nhan_vien' ? route('employee.shifts.closing', ['confirm_closing' => 1]) : route('admin.shift.closing', ['confirm_closing' => 1]) }}" 
                        @if($pendingCount > 0) 
                            onclick="event.preventDefault(); alert('Vui lòng chốt hết {{ $pendingCount }} đơn chờ trước khi chốt ca!'); return false;"
                        @endif
                        @class([
                            'px-2 py-1.5 rounded font-semibold flex items-center shadow-md transition-colors',
                            'bg-red-500 hover:bg-red-600 text-white' => $pendingCount == 0,
                            'bg-gray-400 text-gray-200 cursor-not-allowed' => $pendingCount > 0
                        ])
                    >
                        <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span class="text-[10px] hidden sm:inline">Chốt ca</span>
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

    {{-- Update Stock Modal --}}
    @if($showUpdateStockModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md overflow-hidden max-h-[90vh] flex flex-col">
                <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center shrink-0">
                    <h3 class="text-white font-bold text-lg">Nhập số lượng bổ sung</h3>
                    <button wire:click="$set('showUpdateStockModal', false)" class="text-indigo-100 hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Xác nhận số lượng bánh nhận <span class="text-red-500">*</span></label>
                        <div class="bg-gray-50 rounded-lg border border-gray-200 divide-y divide-gray-200">
                            @forelse($updateProducts as $p)
                                <div class="p-3 flex items-center justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <span class="font-medium text-gray-900 block truncate">{{ $p['ten_san_pham'] ?? 'Sản phẩm' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="relative w-20">
                                            <input type="number" inputmode="numeric" 
                                                wire:model="updateReceivedStock.{{ $p['id'] }}" 
                                                class="block w-full text-center border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 font-bold px-1"
                                                placeholder="0">
                                        </div>
                                        <button wire:click="fillUpdateMaxStock({{ $p['id'] }})"
                                            class="px-2 py-2 bg-indigo-100 text-indigo-700 rounded-lg text-[10px] font-bold hover:bg-indigo-200 active:scale-95 transition-transform whitespace-nowrap">
                                            Max ({{ $updateMaxStock[$p['id']] ?? 0 }})
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center text-gray-500 text-sm">
                                    Không có hàng phân bổ mới đang chờ.
                                </div>
                            @endforelse
                        </div>
                        @error('updateReceivedStock.*')
                            <span class="text-red-500 text-xs mt-1 block">Vui lòng nhập số lượng hợp lệ</span>
                        @enderror
                    </div>
                </div>

                <div class="p-4 bg-gray-50 border-t border-gray-200 shrink-0 flex gap-3">
                    <button wire:click="$set('showUpdateStockModal', false)"
                        class="flex-1 bg-white border border-gray-300 text-gray-700 font-medium py-2.5 rounded-lg hover:bg-gray-50">
                        Hủy
                    </button>
                    <button wire:click="confirmUpdateStock"
                        @if(empty($updateProducts)) disabled @endif
                        class="flex-1 bg-indigo-600 text-white font-bold py-2.5 rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
