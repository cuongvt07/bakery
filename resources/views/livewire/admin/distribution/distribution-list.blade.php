<div>
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-bold text-gray-900">📦 Quản lý Phân bổ hàng</h1>
            <a href="{{ route('admin.distribution.daily') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-semibold">
                ➕ Tạo phân bổ mới
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif
    @if(session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 text-sm">{{ session('error') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
        <div class="grid grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Từ ngày</label>
                <input type="date" wire:model.live="dateFrom" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Đến ngày</label>
                <input type="date" wire:model.live="dateTo" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Điểm bán</label>
                <select wire:model.live="selectedAgency" class="w-full px-2 py-1.5 text-sm border border-gray-300 rounded-lg">
                    <option value="">Tất cả</option>
                    @foreach($agencies as $agency)
                        <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button wire:click="clearFilters" class="px-3 py-1.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                    🔄 Xóa bộ lọc
                </button>
            </div>
        </div>
    </div>

    <!-- Grouped Data by Date - Table Format -->
    @forelse($groupedData as $date => $agenciesData)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-4 overflow-hidden">
            <!-- Date Header -->
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 px-4 py-2">
                <h3 class="font-bold text-gray-900 text-sm">
                    📅 {{ \Carbon\Carbon::parse($date)->format('d/m/Y - l') }}
                </h3>
            </div>

            <!-- Agencies Table -->
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Điểm bán</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Tổng SL</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Số mục</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Đã nhận</th>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Chưa nhận</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($agenciesData as $agencyId => $data)
                        <tr class="hover:bg-gray-50">
                            <td class="px-3 py-2 text-sm font-semibold text-gray-900">{{ $data['agency']->ten_diem_ban }}</td>
                            <td class="px-3 py-2 text-sm font-bold text-blue-600">{{ number_format($data['total_quantity']) }}</td>
                            <td class="px-3 py-2 text-sm text-gray-600">{{ $data['total_items'] }}</td>
                            <td class="px-3 py-2 text-sm">
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-semibold">{{ $data['status_counts']['da_nhan'] }}</span>
                            </td>
                            <td class="px-3 py-2 text-sm">
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-semibold">{{ $data['status_counts']['chua_nhan'] }}</span>
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button wire:click="showDetails('{{ $date }}', {{ $agencyId }})" 
                                    class="px-3 py-1 text-xs bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200">
                                    Chi tiết →
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @empty
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Không có dữ liệu</h3>
            <p class="text-gray-600">Không tìm thấy phân bổ nào trong khoảng thời gian đã chọn</p>
        </div>
    @endforelse

    <!-- Detail Modal -->
    @if($showDetailModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" wire:click="closeModal">
        <div class="bg-white rounded-lg shadow-xl max-w-5xl w-full mx-4 max-h-[90vh] overflow-hidden" wire:click.stop>
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-600 text-white px-6 py-3 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold">Chi tiết phân bổ</h3>
                    <p class="text-sm opacity-90">{{ $modalAgencyName }} - {{ \Carbon\Carbon::parse($modalDate)->format('d/m/Y') }}</p>
                </div>
                <button wire:click="closeModal" class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-4 overflow-y-auto max-h-[calc(90vh-80px)]">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Mẻ SX</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Buổi</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Sản phẩm</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Số lượng</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Trạng thái</th>
                            <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($modalDistributions as $item)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-semibold text-gray-900">#{{ $item->productionBatch->ma_me ?? 'N/A' }}</td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-0.5 rounded text-xs {{ $item->buoi === 'sang' ? 'bg-yellow-100 text-yellow-800' : 'bg-orange-100 text-orange-800' }}">
                                        {{ ucfirst($item->buoi) }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-gray-900">{{ $item->product->ten_san_pham }}</td>
                                <td class="px-3 py-2 font-bold text-blue-600">{{ number_format($item->so_luong) }}</td>
                                <td class="px-3 py-2">
                                    @if($item->trang_thai === 'da_nhan')
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">✓ Đã nhận</span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">⏳ Chưa nhận</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-center">
                                    @if($item->trang_thai !== 'da_nhan')
                                        <button wire:click="deleteDistribution({{ $item->id }})" 
                                            class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs hover:bg-red-200">
                                            Xóa
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @if($modalDistributions->isEmpty())
                    <div class="text-center text-gray-500 py-8">Không có dữ liệu</div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>
