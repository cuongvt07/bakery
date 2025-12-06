<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Phân bổ hàng - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
        <input type="date" wire:model.live="date" class="px-4 py-2 border border-gray-300 rounded-lg">
    </div>

    @if($productionBatches->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
            <p class="text-yellow-700">
                <strong>Chưa có mẻ sản xuất nào hoàn thành!</strong><br>
                Vui lòng đến <a href="{{ route('admin.production-batches.index') }}" class="underline">Module Mẻ sản xuất</a> để tạo và QC mẻ.
            </p>
        </div>
    @else
        <!-- Main Content -->
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
            <!-- Left: Distribution Form (75%) -->
            <div class="xl:col-span-3">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form wire:submit="saveAgencyDistribution">
                        <!-- Selection Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Mẻ sản xuất *</label>
                                <select wire:model.live="selectedProductionBatchId" class="w-full px-3 py-2 border-2 border-indigo-300 rounded-lg">
                                    @foreach($productionBatches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->ma_me }} ({{ ucfirst($batch->buoi) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Điểm bán *</label>
                                <select wire:model.live="selectedAgencyId" class="w-full px-3 py-2 border-2 border-green-300 rounded-lg">
                                    <option value="">-- Chọn điểm --</option>
                                    @foreach($agencies as $agency)
                                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Buổi *</label>
                                <select wire:model.live="selectedSession" class="w-full px-3 py-2 border-2 border-amber-300 rounded-lg">
                                    <option value="sang">Sáng</option>
                                    <option value="chieu">Chiều</option>
                                </select>
                            </div>
                        </div>

                        @if($currentBatch && $selectedAgencyId)
                            <!-- Products Table -->
                            <div class="border-2 border-gray-200 rounded-lg overflow-hidden mb-4">
                                <table class="min-w-full">
                                    <thead class="bg-gray-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Sản phẩm</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-20">Tổng</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-20">PB</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-20">Còn</th>
                                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase w-32">Số lượng</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($currentBatch->details as $detail)
                                            @php
                                                $distributed = $currentBatch->distributions()
                                                    ->where('san_pham_id', $detail->san_pham_id)
                                                    ->sum('so_luong');
                                                $available = $detail->so_luong_thuc_te - $distributed;
                                            @endphp
                                            <tr class="{{ $available > 0 ? 'hover:bg-green-50' : 'bg-gray-50' }}">
                                                <td class="px-4 py-3">
                                                    <span class="font-medium text-gray-900">{{ $detail->product->ten_san_pham }}</span>
                                                </td>
                                                <td class="px-4 py-3 text-center text-sm">{{ $detail->so_luong_thuc_te }}</td>
                                                <td class="px-4 py-3 text-center text-sm text-blue-600 font-medium">{{ $distributed }}</td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="px-2 py-1 rounded text-sm font-bold {{ $available > 0 ? 'bg-green-200 text-green-900' : 'bg-gray-200 text-gray-600' }}">
                                                        {{ $available }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="number" 
                                                           wire:model="distributionData.{{ $detail->san_pham_id }}" 
                                                           min="0" 
                                                           max="{{ $available }}"
                                                           class="w-full px-3 py-2 text-center text-lg font-bold border-2 rounded-lg {{ $available > 0 ? 'border-green-400 bg-green-50' : 'border-gray-300 bg-gray-100' }}"
                                                           placeholder="0"
                                                           {{ $available <= 0 ? 'disabled' : '' }}>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Messages -->
                            @if (session('success'))
                                <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-3 mb-4 rounded font-medium">
                                    ✅ {{ session('success') }}
                                </div>
                            @endif
                            @if (session('error'))
                                <div class="bg-red-100 border-l-4 border-red-500 text-red-800 p-3 mb-4 rounded font-medium">
                                    ❌ {{ session('error') }}
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-3">
                                <button type="button" wire:click="$set('distributionData', [])" class="px-5 py-2 border-2 border-gray-300 rounded-lg hover:bg-gray-100 font-medium">
                                    🗑️ Xóa
                                </button>
                                <button type="submit" class="bg-indigo-600 text-white px-8 py-3 rounded-lg hover:bg-indigo-700 font-bold text-lg shadow-lg">
                                    💾 Lưu phân bổ
                                </button>
                            </div>
                        @elseif($currentBatch)
                            <div class="text-center py-16 text-gray-400">
                                <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <p class="text-lg font-medium">👆 Chọn điểm bán để bắt đầu</p>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Right: History Sidebar (25%) -->
            <div class="xl:col-span-1">
                @if($currentBatch && $currentBatch->distributions->isNotEmpty())
                    <div class="bg-white rounded-lg shadow-sm p-4 sticky top-6">
                        <h4 class="text-sm font-bold text-gray-700 mb-3 pb-2 border-b">📋 Đã phân bổ</h4>
                        <div class="space-y-3 max-h-[600px] overflow-y-auto">
                            @foreach($currentBatch->distributions->groupBy('diem_ban_id') as $diemBanId => $dists)
                                @php
                                    $agency = $agencies->find($diemBanId);
                                @endphp
                                <div class="bg-gray-50 rounded p-3 border border-gray-200">
                                    <div class="font-semibold text-sm text-gray-800 mb-2">{{ $agency->ten_diem_ban }}</div>
                                    <div class="space-y-1">
                                        @foreach($dists->groupBy('san_pham_id') as $productId => $productDists)
                                            @php
                                                $product = $productDists->first()->product;
                                                $total = $productDists->sum('so_luong');
                                            @endphp
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-600">{{ $product->ten_san_pham }}</span>
                                                <span class="font-bold text-indigo-600">{{ $total }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
