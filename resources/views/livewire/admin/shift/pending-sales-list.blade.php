<div class="min-h-screen bg-gray-50 pb-32">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-purple-600 to-purple-700 text-white shadow-lg">
        <div class="px-3 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <div>
                        <h1 class="text-lg font-bold">Danh sách chưa chốt</h1>
                        <p class="text-xs text-purple-100">{{ count($pendingSales) }} đơn hàng</p>
                    </div>
                </div>
                
                <a href="/admin/pos" class="bg-white text-purple-600 px-3 py-1.5 rounded-lg font-semibold text-sm hover:bg-purple-50 transition-colors">
                    Quay lại POS
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('success'))
        <div class="mx-3 mt-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif
    @if(session()->has('error'))
        <div class="mx-3 mt-3 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
    @if(session()->has('warning'))
        <div class="mx-3 mt-3 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('warning') }}</span>
        </div>
    @endif

    <div class="p-3 space-y-3">
        @if(empty($pendingSales))
            <div class="text-center py-20">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 font-medium">Chưa có đơn hàng nào</p>
                <a href="/admin/pos" class="mt-4 inline-block bg-purple-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-purple-700">
                    Về POS
                </a>
            </div>
        @else
            {{-- Sales List --}}
            @foreach($pendingSales as $sale)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-3">
                        <div class="flex items-start gap-2 mb-2">
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="text-xs text-gray-500 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($sale['thoi_gian'])->format('H:i') }}
                                    </div>
                                    <button wire:click="delete({{ $sale['id'] }})" wire:confirm="Xác nhận xóa đơn này?" class="text-red-500 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                
                                {{-- Items --}}
                                <div class="space-y-1 text-sm">
                                    @foreach($sale['chi_tiet'] as $item)
                                        <div class="flex justify-between text-gray-700">
                                            <span>{{ $item['ten_sp'] }} × {{ $item['so_luong'] }}</span>
                                            <span class="font-semibold">{{ number_format($item['thanh_tien']) }}đ</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Total & Payment Method --}}
                                <div class="mt-2 pt-2 border-t space-y-1">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs font-medium px-2 py-1 rounded {{ $sale['phuong_thuc_thanh_toan'] === 'tien_mat' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $sale['phuong_thuc_thanh_toan'] === 'tien_mat' ? '💵 TM' : '💳 CK' }}
                                        </span>
                                        <span class="text-base font-bold text-purple-600">{{ number_format($sale['tong_tien']) }}đ</span>
                                    </div>
                                    
                                    @if($sale['phuong_thuc_thanh_toan'] === 'tien_mat')
                                        @php
                                            $expectedCash = $openingCash + $confirmedCashTotal + $sale['tong_tien'];
                                        @endphp
                                        <div class="flex justify-between items-center text-xs">
                                            <span class="text-gray-600">💰 TM lý thuyết phải có:</span>
                                            <span class="font-bold text-green-600">{{ number_format($expectedCash) }}đ</span>
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            (Đầu ca: {{ number_format($openingCash) }}đ + Đã chốt: {{ number_format($confirmedCashTotal) }}đ + Đơn này: {{ number_format($sale['tong_tien']) }}đ)
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Sticky Footer --}}
    @if(!empty($pendingSales))
    @php
        $totalCash = 0;
        $totalTransfer = 0;
        foreach($pendingSales as $sale) {
            if($sale['phuong_thuc_thanh_toan'] === 'tien_mat') {
                $totalCash += $sale['tong_tien'];
            } else {
                $totalTransfer += $sale['tong_tien'];
            }
        }
    @endphp
    <div class="fixed bottom-0 left-0 right-0 bg-white shadow-2xl border-t-4 border-purple-500">
        <div class="px-3 py-3">
            <div class="mb-2 text-center">
                <p class="text-sm text-gray-600">Tổng số đơn chờ</p>
                <p class="text-2xl font-bold text-purple-600">{{ count($pendingSales) }} đơn</p>
            </div>
            
            <div class="mb-3 grid grid-cols-2 gap-2 text-center">
                <div class="bg-green-50 rounded-lg p-2">
                    <p class="text-xs text-gray-600">💵 TM lý thuyết phải có</p>
                    <p class="text-lg font-bold text-green-600">{{ number_format($openingCash + $confirmedCashTotal + $totalCash) }}đ</p>
                    <p class="text-xs text-gray-500">({{ number_format($openingCash) }} + {{ number_format($confirmedCashTotal) }} + {{ number_format($totalCash) }})</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-2">
                    <p class="text-xs text-gray-600">💳 Chuyển khoản</p>
                    <p class="text-lg font-bold text-blue-600">{{ number_format($totalTransfer) }}đ</p>
                </div>
            </div>
            
            <div class="mb-3 text-center bg-purple-50 rounded-lg p-2">
                <p class="text-xs text-gray-600">Tổng tất cả đơn</p>
                <p class="text-xl font-bold text-purple-600">{{ number_format(array_sum(array_column($pendingSales, 'tong_tien'))) }}đ</p>
            </div>
            
            <button 
                wire:click="confirmAll" 
                wire:confirm="Xác nhận chốt TẤT CẢ {{ count($pendingSales) }} đơn?" 
                class="w-full bg-purple-600 text-white font-bold py-4 px-4 rounded-lg text-lg shadow-lg hover:bg-purple-700 active:scale-95 transition-all duration-150 flex items-center justify-center gap-2"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                CHỐT TẤT CẢ {{ count($pendingSales) }} ĐƠN
            </button>
        </div>
    </div>
    @endif
</div>
