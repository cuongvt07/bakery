<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Phân bổ hàng - {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
        <input type="date" wire:model.live="date" class="px-4 py-2 border border-gray-300 rounded-lg">
    </div>

    @if($productionBatches->isEmpty())
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <p class="text-yellow-700">
                <strong>Chưa có mẻ sản xuất nào hoàn thành!</strong><br>
                Vui lòng đến <a href="{{ route('admin.production-batches.index') }}" class="underline">Module Mẻ sản xuất</a> để tạo và QC mẻ.
            </p>
        </div>
    @else
        <!-- Select Production Batch -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4">1. Chọn Mẻ sản xuất</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($productionBatches as $batch)
                    <button 
                        wire:click="selectProductionBatch({{ $batch->id }})"
                        class="p-4 border-2 rounded-lg transition-all {{ $selectedProductionBatchId == $batch->id ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                        <div class="flex justify-between items-start mb-2">
                            <span class="font-bold text-indigo-600">{{ $batch->ma_me }}</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs">{{ ucfirst($batch->buoi) }}</span>
                        </div>
                        <div class="text-sm text-gray-700 font-medium mb-1">{{ $batch->recipe->product->ten_san_pham }}</div>
                        <div class="text-xs text-gray-500">
                            Thực tế: {{ $batch->so_luong_thuc_te }} {{ $batch->recipe->don_vi_san_xuat }}<br>
                            Đã phân bổ: {{ $batch->distributed_quantity }}<br>
                            <strong class="text-green-600">Còn lại: {{ $batch->available_quantity }}</strong>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        @if($currentBatch)
            <!-- Distribution Form -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">2. Phân bổ cho điểm bán</h3>
                
                <!-- Batch Info -->
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">Sản phẩm:</span>
                            <div class="font-semibold">{{ $currentBatch->recipe->product->ten_san_pham }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Tổng (sau QC):</span>
                            <div class="font-semibold">{{ $currentBatch->so_luong_thuc_te }} {{ $currentBatch->recipe->don_vi_san_xuat }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Đã phân bổ:</span>
                            <div class="font-semibold text-blue-600">{{ $currentBatch->distributed_quantity }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600">Còn lại:</span>
                            <div class="font-semibold text-green-600">{{ $currentBatch->available_quantity }}</div>
                        </div>
                    </div>
                </div>

                <!-- Distribution List -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-700 mb-3">Đã phân bổ:</h4>
                    @if($currentBatch->distributions->isEmpty())
                        <p class="text-gray-500 text-sm">Chưa phân bổ cho điểm nào</p>
                    @else
                        <div class="space-y-2">
                            @foreach($currentBatch->distributions->groupBy('diem_ban_id') as $diemBanId => $dists)
                                @php
                                    $agency = $agencies->find($diemBanId);
                                    $total = $dists->sum('so_luong');
                                @endphp
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded border">
                                    <div>
                                        <span class="font-medium">{{ $agency->ten_diem_ban }}</span>
                                        <span class="text-xs text-gray-500 ml-2">
                                            @foreach($dists as $d)
                                                {{ ucfirst($d->buoi) }}: {{ $d->so_luong }}
                                            @endforeach
                                        </span>
                                    </div>
                                    <span class="font-semibold text-indigo-600">{{ $total }} {{ $currentBatch->recipe->don_vi_san_xuat }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Add/Edit Distribution -->
                <form wire:submit="saveAgencyDistribution" class="border-t pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Điểm bán</label>
                            <select wire:model.live="selectedAgencyId" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="">-- Chọn điểm --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Buổi</label>
                            <select wire:model.live="selectedSession" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                <option value="sang">Sáng</option>
                                <option value="chieu">Chiều</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng</label>
                            <input type="number" wire:model="distributionQuantity" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="VD: 20">
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 mb-4 rounded">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 rounded">{{ session('error') }}</div>
                    @endif

                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700" {{ !$selectedAgencyId ? 'disabled' : '' }}>
                        Lưu phân bổ
                    </button>
                </form>
            </div>
        @endif
    @endif
</div>
