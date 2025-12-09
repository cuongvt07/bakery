<div class="min-h-screen bg-gray-50 pb-32">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white shadow-lg">
        <div class="px-3 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h1 class="text-lg font-bold">Đơn hàng</h1>
                        <p class="text-xs text-green-100">{{ count($sales) }} đơn</p>
                    </div>
                </div>
                
                <a href="/admin/pos" class="bg-white text-green-600 px-3 py-1.5 rounded-lg font-semibold text-sm hover:bg-green-50 transition-colors">
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

    <div class="p-3 space-y-3">
        @if(empty($sales))
            <div class="text-center py-20">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 font-medium">Chưa có đơn hàng nào</p>
            </div>
        @else
            {{-- Sales List --}}
            @foreach($sales as $sale)
                @php
                    $isConfirmed = $sale['is_confirmed'];
                    // For confirmed: chi_tiet_don is array of sales, each with chi_tiet
                    // For pending: chi_tiet is direct array
                    if ($isConfirmed) {
                        // Flatten all chi_tiet from all sales in the batch
                        $chiTiet = [];
                        foreach ($sale['chi_tiet_don'] as $saleInBatch) {
                            foreach ($saleInBatch['chi_tiet'] as $item) {
                                $chiTiet[] = $item;
                            }
                        }
                    } else {
                        $chiTiet = $sale['chi_tiet'];
                    }
                @endphp
                <div class="bg-white rounded-lg shadow-sm overflow-hidden {{ $isConfirmed ? 'border-2 border-green-200' : '' }}">
                    <div class="p-3">
                        <div class="flex items-start gap-2 mb-2">
                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center gap-2">
                                        <div class="text-xs text-gray-500 flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($sale['created_at'])->format('H:i') }}
                                        </div>
                                        
                                        @if($isConfirmed)
                                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded text-xs font-semibold">
                                                ✓ Đã chốt
                                            </span>
                                        @else
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded text-xs font-semibold">
                                                ⏳ Chờ chốt
                                            </span>
                                        @endif
                                    </div>
                                    
                                    @if($isConfirmed)
                                        <button wire:click="openEdit({{ $sale['id'] }}, true)" class="text-blue-500 hover:text-blue-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                
                                {{-- Items --}}
                                <div class="space-y-1 text-sm">
                                    @foreach($chiTiet as $item)
                                        <div class="flex justify-between text-gray-700">
                                            <span>{{ $item['ten_sp'] }} × {{ $item['so_luong'] }}</span>
                                            <span class="font-semibold">{{ number_format($item['thanh_tien']) }}đ</span>
                                        </div>
                                    @endforeach
                                </div>
                                
                                {{-- Total & Payment Method --}}
                                <div class="mt-2 pt-2 border-t flex justify-between items-center">
                                    @php
                                        // For confirmed batch, check first sale's payment method
                                        $paymentMethod = 'tien_mat';
                                        if ($isConfirmed && !empty($sale['chi_tiet_don'])) {
                                            // Get first sale's details from PendingSale to find payment method
                                            $firstSaleId = $sale['chi_tiet_don'][0]['id'] ?? null;
                                            if ($firstSaleId) {
                                                $pendingSale = \App\Models\PendingSale::find($firstSaleId);
                                                $paymentMethod = $pendingSale->phuong_thuc_thanh_toan ?? 'tien_mat';
                                            }
                                        } else {
                                            $paymentMethod = $sale['phuong_thuc_thanh_toan'] ?? 'tien_mat';
                                        }
                                    @endphp
                                    <span class="text-xs font-medium px-2 py-1 rounded {{ $paymentMethod === 'tien_mat' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $paymentMethod === 'tien_mat' ? '💵 TM' : '💳 CK' }}
                                    </span>
                                    <span class="text-base font-bold text-green-600">{{ number_format($sale['tong_tien']) }}đ</span>
                                </div>
                                
                                {{-- Show note if exists --}}
                                @if($isConfirmed && !empty($sale['ghi_chu']))
                                    <div class="mt-2 pt-2 border-t">
                                        <p class="text-xs text-gray-600 font-semibold mb-1">📝 Ghi chú điều chỉnh:</p>
                                        <p class="text-xs text-gray-700 whitespace-pre-wrap bg-yellow-50 p-2 rounded">{{ $sale['ghi_chu'] }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{-- Edit Modal --}}
    @if($showEditModal && $editingBatch)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="closeEdit">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden" wire:click.stop>
            <div class="bg-green-600 text-white px-6 py-4 flex items-center justify-between rounded-t-lg">
                <h3 class="text-lg font-bold">Chỉnh sửa đơn đã chốt</h3>
                <button wire:click="closeEdit" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                <div class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded-r">
                    <p class="text-sm text-yellow-800 font-semibold">⚠️ Lưu ý:</p>
                    <p class="text-xs text-yellow-700 mt-1">Đơn này đã được chốt. Mọi thay đổi sẽ được ghi lại để admin theo dõi.</p>
                </div>

                {{-- Product Quantities --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm & Số lượng</label>
                    <div class="space-y-2">
                        @foreach($editingBatchItems as $key => $item)
                            <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-lg">
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-900">{{ $item['ten_sp'] }}</p>
                                    <p class="text-xs text-gray-600">{{ number_format($item['gia']) }}đ/sp</p>
                                </div>
                                <input 
                                    type="number" 
                                    wire:model.live="editingBatchItems.{{ $key }}.so_luong"
                                    min="0"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center"
                                >
                                <div class="w-24 text-right">
                                    <p class="font-bold text-green-600">{{ number_format($item['gia'] * $item['so_luong']) }}đ</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Total --}}
                    @php
                        $modalTotal = array_sum(array_map(fn($i) => $i['gia'] * $i['so_luong'], $editingBatchItems));
                    @endphp
                    <div class="mt-3 pt-3 border-t flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Tổng cộng:</span>
                        <span class="text-xl font-bold text-green-600">{{ number_format($modalTotal) }}đ</span>
                    </div>
                </div>

                {{-- Payment Method --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phương thức thanh toán</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button 
                            type="button"
                            wire:click="$set('editPaymentMethod', 'tien_mat')"
                            @class([
                                'flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-semibold transition-all',
                                'bg-green-100 text-green-700 border-2 border-green-500' => $editPaymentMethod === 'tien_mat',
                                'bg-gray-100 text-gray-600 border-2 border-transparent hover:border-gray-300' => $editPaymentMethod !== 'tien_mat',
                            ])
                        >
                            <span class="text-2xl">💵</span>
                            <span>Tiền mặt</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="$set('editPaymentMethod', 'chuyen_khoan')"
                            @class([
                                'flex items-center justify-center gap-2 px-4 py-3 rounded-lg font-semibold transition-all',
                                'bg-blue-100 text-blue-700 border-2 border-blue-500' => $editPaymentMethod === 'chuyen_khoan',
                                'bg-gray-100 text-gray-600 border-2 border-transparent hover:border-gray-300' => $editPaymentMethod !== 'chuyen_khoan',
                            ])
                        >
                            <span class="text-2xl">💳</span>
                            <span>Chuyển khoản</span>
                        </button>
                    </div>
                </div>

                {{-- Note (Required) --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Lý do điều chỉnh *</label>
                    <textarea 
                        wire:model="editNote" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500"
                        placeholder="VD: Khách hàng trả lại 2 bánh mì vì không tươi, chuyển sang thanh toán chuyển khoản..."
                    ></textarea>
                    @error('editNote')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 bg-gray-50 border-t flex gap-3">
                <button wire:click="closeEdit" class="flex-1 px-4 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-semibold">
                    Hủy
                </button>
                <button wire:click="updateBatch" class="flex-1 px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                    Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
