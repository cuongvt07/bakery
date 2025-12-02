<div class="max-w-7xl mx-auto p-4">
    <!-- Header -->
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Phân Bổ Hàng Hàng Ngày</h1>
            <p class="text-sm text-gray-500">Quản lý nhập hàng từ xưởng và chia về các điểm bán</p>
        </div>
        <div>
            <input type="date" wire:model.live="date" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Total Ticket (Step 1) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="font-semibold text-gray-700">1. Tổng Hàng Từ Xưởng</h2>
                    @if($step == 2)
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Đã tạo</span>
                    @endif
                </div>
                
                <div class="p-4">
                    @if($step == 1)
                        <div class="space-y-4">
                            @foreach($products as $p)
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-700 w-1/2">{{ $p->ten_san_pham }}</label>
                                    <input type="number" wire:model="totalQuantities.{{ $p->id }}" class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right" min="0">
                                </div>
                            @endforeach
                            
                            <div class="pt-4 border-t">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ghi chú</label>
                                <textarea wire:model="ghiChuTong" class="w-full border-gray-300 rounded-md shadow-sm text-sm" rows="2"></textarea>
                            </div>
                            
                            <button wire:click="createTotalTicket" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                                Lưu Phiếu Tổng
                            </button>
                        </div>
                    @else
                        <!-- Read-only view for Step 2 -->
                        <div class="space-y-3">
                            @foreach($products as $p)
                                @if(($totalQuantities[$p->id] ?? 0) > 0)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $p->ten_san_pham }}</span>
                                        <span class="font-bold">{{ $totalQuantities[$p->id] }}</span>
                                    </div>
                                @endif
                            @endforeach
                            <div class="pt-3 border-t text-xs text-gray-500">
                                Mã phiếu: {{ $phieuTong->ma_phieu }}<br>
                                Tạo lúc: {{ $phieuTong->created_at->format('H:i') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Distribution (Step 2) -->
        <div class="lg:col-span-2">
            @if($step == 2)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden h-full">
                    <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                        <h2 class="font-semibold text-gray-700">2. Phân Bổ Cho Điểm Bán</h2>
                    </div>
                    
                    <div class="p-4">
                        <!-- Agency Selector -->
                        <div class="mb-6 flex space-x-2 overflow-x-auto pb-2">
                            @foreach($agencies as $agency)
                                <button 
                                    wire:click="selectAgency({{ $agency->id }})"
                                    class="px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-colors {{ $selectedAgencyId == $agency->id ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                >
                                    {{ $agency->ten_diem_ban }}
                                </button>
                            @endforeach
                        </div>

                        @if($selectedAgencyId)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Input Form -->
                                <div>
                                    <h3 class="text-sm font-bold text-gray-900 uppercase mb-3">Nhập số lượng</h3>
                                    <div class="space-y-3">
                                        @foreach($products as $p)
                                            @php
                                                $total = $totalQuantities[$p->id] ?? 0;
                                                if ($total == 0) continue;
                                                
                                                $distributed = $distributedState[$p->id] ?? 0;
                                                // Exclude current agency from distributed count for calculation
                                                // (Simplified for UI: just show remaining global)
                                                $remaining = $total - $distributed + ($agencyQuantities[$p->id] ?? 0); 
                                            @endphp
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <div class="text-sm font-medium text-gray-700">{{ $p->ten_san_pham }}</div>
                                                    <div class="text-xs text-gray-500">Còn lại: {{ $remaining }} / {{ $total }}</div>
                                                </div>
                                                <input type="number" wire:model="agencyQuantities.{{ $p->id }}" class="w-24 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-right" min="0" max="{{ $remaining }}">
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-6">
                                        <button wire:click="saveAgencyDistribution" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded transition">
                                            Lưu Phân Bổ Điểm Này
                                        </button>
                                    </div>
                                </div>

                                <!-- Progress/Status -->
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h3 class="text-sm font-bold text-gray-900 uppercase mb-3">Tiến độ phân bổ toàn hệ thống</h3>
                                    <div class="space-y-4">
                                        @foreach($products as $p)
                                            @php
                                                $total = $totalQuantities[$p->id] ?? 0;
                                                if ($total == 0) continue;
                                                $distributed = $distributedState[$p->id] ?? 0;
                                                $percent = ($distributed / $total) * 100;
                                            @endphp
                                            <div>
                                                <div class="flex justify-between text-xs mb-1">
                                                    <span>{{ $p->ten_san_pham }}</span>
                                                    <span class="{{ $distributed > $total ? 'text-red-600' : 'text-gray-600' }}">
                                                        {{ $distributed }}/{{ $total }}
                                                    </span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($percent, 100) }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p class="mt-2">Vui lòng chọn một điểm bán để bắt đầu phân bổ.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-full bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <div class="text-center p-6">
                        <p class="text-gray-500">Hoàn thành Bước 1 để tiếp tục phân bổ.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
