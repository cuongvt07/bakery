<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Báo cáo Hạn sử dụng</h2>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
            <div class="text-sm text-gray-600">Tổng sản phẩm</div>
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
            <div class="text-sm text-gray-600">Hết hạn</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['expired'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
            <div class="text-sm text-gray-600">Sắp hết (≤1 ngày)</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['near_expiry'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
            <div class="text-sm text-gray-600">Còn tốt</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['ok'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                <input type="text" wire:model.live="search" placeholder="Tìm theo tên sản phẩm..." class="w-full px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lọc theo trạng thái</label>
                <select wire:model.live="filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="all">Tất cả</option>
                    <option value="expired">🔴 Hết hạn</option>
                    <option value="near_expiry">🟠 Sắp hết hạn</option>
                    <option value="ok">🟢 Còn tốt</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sản phẩm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Số lượng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">HSD</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Khả dụng</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($details as $detail)
                    @php
                        $rowClass = '';
                        if ($detail->isExpired()) {
                            $rowClass = 'bg-red-50';
                        } elseif ($detail->isNearExpiry()) {
                            $rowClass = 'bg-orange-50';
                        }
                    @endphp
                    <tr class="{{ $rowClass }} hover:bg-opacity-75">
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ $detail->product->ten_san_pham }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            {{ $detail->so_luong_thuc_te ?? 0 }} cái
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold">
                            {{ \Carbon\Carbon::parse($detail->han_su_dung)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <x-hsd-badge :detail="$detail" size="md" />
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="font-semibold {{ ($detail->available_quantity ?? 0) > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                {{ $detail->available_quantity ?? 0 }} cái
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Không tìm thấy sản phẩm nào
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t">
            {{ $details->links() }}
        </div>
    </div>
</div>
