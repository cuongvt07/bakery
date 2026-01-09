<div>
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">🎯 Phân bổ hàng theo ngày</h1>
            <a href="{{ route('admin.distribution.index') }}"
                class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                ← Quay lại
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">
            {{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}
        </div>
    @endif

    <!-- Controls -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <div class="grid grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Ngày SX</label>
                <input type="date" wire:model.live="date"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Điểm bán <span
                        class="text-red-500">*</span></label>
                <select wire:model.live="selectedAgencyId"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Chọn --</option>
                    @foreach ($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Buổi</label>
                <select wire:model.live="selectedSession"
                    class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="sang">Sáng</option>
                    <option value="chieu">Chiều</option>
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="saveDistributions"
                    class="w-full px-4 py-1.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold"
                    @if (!$selectedAgencyId) disabled @endif>
                    💾 Lưu phân bổ
                </button>
            </div>
        </div>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Mẻ SX</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Ngày/Buổi</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Sản phẩm</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tổng SL</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Đã phân</th>
                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Còn lại</th>
                    <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Thao tác</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($batches as $batch)
                    @php
                        $totalProducts = count($batch->details);
                        $total = collect($batch->details)->sum('so_luong_thuc_te');
                        $distributed = collect($batch->details)->sum(
                            fn($d) => $availability[$batch->id][$d->san_pham_id]['distributed'] ?? 0,
                        );
                        $available = collect($batch->details)->sum(
                            fn($d) => $availability[$batch->id][$d->san_pham_id]['available'] ?? 0,
                        );
                        $isFullyDistributed = $available <= 0;
                    @endphp

                    <!-- Batch Row -->
                    <tr class="hover:bg-gray-50 {{ $isFullyDistributed ? 'opacity-50' : '' }}"
                        wire:key="batch-{{ $batch->id }}">
                        <td class="px-3 py-2 text-sm font-semibold text-gray-900">#{{ $batch->ma_me }}</td>
                        <td class="px-3 py-2 text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($batch->ngay_san_xuat)->format('d/m') }} -
                            <span
                                class="px-1.5 py-0.5 rounded text-xs {{ $batch->buoi === 'sang' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ ucfirst($batch->buoi) }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-sm text-gray-600">{{ $totalProducts }} loại</td>
                        <td class="px-3 py-2 text-sm font-semibold text-gray-900">{{ number_format($total) }}</td>
                        <td class="px-3 py-2 text-sm text-blue-600 font-semibold">{{ number_format($distributed) }}
                        </td>
                        <td
                            class="px-3 py-2 text-sm font-semibold {{ $available > 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($available) }}
                        </td>
                        <td class="px-3 py-2 text-center">
                            @if ($isFullyDistributed)
                                <span class="px-3 py-1 text-xs rounded bg-gray-200 text-gray-500">Đã hết</span>
                            @else
                                <button wire:click="toggleBatch({{ $batch->id }})"
                                    class="px-3 py-1 text-xs rounded bg-indigo-100 text-indigo-700 hover:bg-indigo-200">
                                    @if (isset($collapsedBatches[$batch->id]) && $collapsedBatches[$batch->id])
                                        ▼ Mở
                                    @else
                                        ▲ Đóng
                                    @endif
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- Expanded Products -->
                    @if (!$isFullyDistributed && (!isset($collapsedBatches[$batch->id]) || !$collapsedBatches[$batch->id]))
                        <tr wire:key="batch-expand-{{ $batch->id }}">
                            <td colspan="7" class="px-0 py-0 overflow-hidden">
                                <div class="animate-slideDown">
                                    <div class="px-6 py-3 bg-gray-50">
                                        <div class="mb-2 text-sm font-semibold text-indigo-700">
                                            📦 Sản phẩm trong mẻ #{{ $batch->ma_me }}:
                                        </div>
                                        @if (count($batch->details) > 0)
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                                @foreach ($batch->details as $detail)
                                                    @php
                                                        $avail = $availability[$batch->id][$detail->san_pham_id] ?? [
                                                            'total' => 0,
                                                            'distributed' => 0,
                                                            'available' => 0,
                                                        ];
                                                        $currentQty =
                                                            $distributionData[$batch->id][$detail->san_pham_id] ?? 0;
                                                        $productFull = $avail['available'] <= 0;
                                                    @endphp

                                                    <div class="flex items-center justify-between p-2 border border-gray-300 rounded {{ $productFull ? 'bg-gray-100' : 'bg-white' }}"
                                                        wire:key="product-{{ $batch->id }}-{{ $detail->san_pham_id }}">
                                                        <div class="flex-1 min-w-0 mr-2">
                                                            <p class="text-sm font-semibold text-gray-900 truncate">
                                                                {{ $detail->product->ten_san_pham }}</p>
                                                            <p class="text-xs text-gray-600">
                                                                Tổng: {{ $avail['total'] }} | Đã:
                                                                {{ $avail['distributed'] }} | <span
                                                                    class="{{ $avail['available'] > 0 ? 'text-green-600' : 'text-red-600' }}">Còn:
                                                                    {{ $avail['available'] }}</span>
                                                            </p>
                                                        </div>
                                                        <div class="flex-shrink-0">
                                                            <input type="number"
                                                                wire:model.live="distributionData.{{ $batch->id }}.{{ $detail->san_pham_id }}"
                                                                min="0" max="{{ $avail['available'] }}"
                                                                @if ($productFull) disabled @endif
                                                                class="w-20 px-2 py-1.5 text-center text-lg font-bold text-indigo-600 border border-gray-300 rounded focus:ring-2 focus:ring-indigo-500 disabled:bg-gray-100"
                                                                placeholder="0">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="text-center text-gray-500 py-4">
                                                Không có sản phẩm trong mẻ này
                                            </div>
                                        @endif
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p>Không có mẻ sản xuất nào</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
