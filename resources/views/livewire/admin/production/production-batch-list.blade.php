<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Quản lý Mẻ Sản xuất</h2>
        <a href="{{ route('admin.production-batches.create') }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tạo Mẻ mới
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sản xuất</label>
                <input type="date" wire:model.live="dateFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buổi</label>
                <select wire:model.live="buoiFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="sang">Sáng</option>
                    <option value="trua">Trưa</option>
                    <option value="chieu">Chiều</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                <select wire:model.live="trangThaiFilter" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Tất cả</option>
                    <option value="ke_hoach">Kế hoạch</option>
                    <option value="dang_san_xuat">Đang SX</option>
                    <option value="qc">QC</option>
                    <option value="hoan_thanh">Hoàn thành</option>
                    <option value="huy">Hủy</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button wire:click="resetFilters" class="w-full px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                    Xóa bộ lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Status Flow Legend -->
    <div class="mb-6 flex items-center gap-4 text-sm text-gray-500 overflow-x-auto pb-2">
        <span class="font-semibold whitespace-nowrap">Quy trình:</span>
        <div class="flex items-center gap-2 whitespace-nowrap">
            <span class="px-2 py-1 rounded bg-gray-100 text-gray-800 border">Kế hoạch</span>
            <span class="text-gray-400">→</span>
            <span class="px-2 py-1 rounded bg-blue-100 text-blue-800 border-blue-200">Đang SX</span>
            <span class="text-gray-400">→</span>
            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 border-yellow-200">QC (Kiểm tra)</span>
            <span class="text-gray-400">→</span>
            <span class="px-2 py-1 rounded bg-green-100 text-green-800 border-green-200">Hoàn thành</span>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if (session('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4">{{ session('message') }}</div>
        @endif
        @if (session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">{{ session('error') }}</div>
        @endif
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mã Mẻ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày/Buổi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dự kiến</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thực tế</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lỗi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($batches as $batch)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $batch->ma_me }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                @if($batch->details->isNotEmpty())
                                    @foreach($batch->details as $detail)
                                        <div class="py-0.5">{{ $detail->product->ten_san_pham }} ({{ $detail->so_luong_du_kien }})</div>
                                    @endforeach
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($batch->ngay_san_xuat)->format('d/m/Y') }}
                                <span class="ml-1 px-2 py-0.5 bg-blue-100 text-blue-700 rounded text-xs">{{ ucfirst($batch->buoi) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $batch->total_expected_quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $batch->total_actual_quantity > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $batch->total_actual_quantity > 0 ? $batch->total_actual_quantity : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $batch->total_failed_quantity > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                {{ $batch->total_failed_quantity > 0 ? $batch->total_failed_quantity . ' (' . number_format($batch->average_defect_rate, 1) . '%)' : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'ke_hoach' => 'bg-gray-100 text-gray-800',
                                        'dang_san_xuat' => 'bg-blue-100 text-blue-800',
                                        'qc' => 'bg-yellow-100 text-yellow-800',
                                        'hoan_thanh' => 'bg-green-100 text-green-800',
                                        'huy' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'ke_hoach' => 'Kế hoạch',
                                        'dang_san_xuat' => 'Đang SX',
                                        'qc' => 'QC',
                                        'hoan_thanh' => '✓ Hoàn thành',
                                        'huy' => 'Hủy',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$batch->trang_thai] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$batch->trang_thai] ?? $batch->trang_thai }}
                                </span>
                                
                                @if(in_array($batch->trang_thai, ['ke_hoach', 'dang_san_xuat', 'qc']))
                                    <button wire:click="nextStatus({{ $batch->id }})" 
                                            class="ml-2 text-indigo-600 hover:text-indigo-900 focus:outline-none"
                                            title="{{ $batch->trang_thai === 'ke_hoach' ? 'Chuyển: Đang SX' : ($batch->trang_thai === 'dang_san_xuat' ? 'Chuyển: QC' : 'Vào QC/Chi tiết') }}">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.production-batches.edit', $batch->id) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    {{ $batch->trang_thai === 'qc' || $batch->trang_thai === 'dang_san_xuat' ? 'QC/Detail' : 'Sửa' }}
                                </a>
                                @if($batch->distributions_count == 0)
                                    <button wire:click="delete({{ $batch->id }})" wire:confirm="Bạn có chắc muốn xóa?" class="text-red-600 hover:text-red-900">Xóa</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                Chưa có mẻ sản xuất nào
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="bg-white px-4 py-3 border-t border-gray-200">
            {{ $batches->links() }}
        </div>
    </div>
</div>
