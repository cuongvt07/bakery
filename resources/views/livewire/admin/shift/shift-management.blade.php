<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Quản lý Ca Làm Việc</h2>
            <p class="text-sm text-gray-500 mt-1">Giám sát ca làm việc của nhân viên tại các điểm bán</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-green-50 px-4 py-2 rounded-lg border border-green-200">
                <p class="text-xs text-green-600 font-medium">Đang hoạt động</p>
                <p class="text-2xl font-bold text-green-700">{{ $activeShifts }}</p>
            </div>
            <div class="bg-yellow-50 px-4 py-2 rounded-lg border border-yellow-200">
                <p class="text-xs text-yellow-600 font-medium">Chưa check-in</p>
                <p class="text-2xl font-bold text-yellow-700">{{ $notCheckedIn }}</p>
            </div>
            <div class="bg-red-50 px-4 py-2 rounded-lg border border-red-200">
                <p class="text-xs text-red-600 font-medium">Chưa chốt</p>
                <p class="text-2xl font-bold text-red-700">{{ $notClosed }}</p>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="space-y-3">
            <!-- Row 1: Search + Dates -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tìm kiếm</label>
                    <input type="text" wire:model.live.debounce.300ms="search" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Nhân viên, điểm bán...">
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
                    <input type="date" wire:model.live="dateFrom" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
                    <input type="date" wire:model.live="dateTo" 
                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>

            <!-- Row 2: Agency + Status -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Agency Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Điểm bán</label>
                    <select wire:model.live="agencyFilter" 
                            class="block w-full min-w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả</option>
                        @foreach($agencies as $agency)
                            <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Trạng thái</label>
                    <select wire:model.live="statusFilter" 
                            class="block w-full min-w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option value="">Tất cả</option>
                        <option value="active">✅ Đang làm</option>
                        <option value="closed">🔒 Đã kết thúc</option>
                        <option value="not_checked_in">⚠️ Chưa check-in</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    @if($shifts->count() > 0)
        <div class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Điểm bán</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Giờ bắt đầu</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Check-in</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Tiền đầu ca</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Trạng thái ca</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Chốt ca</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($shifts as $shift)
                        @php
                            $isActive = $shift->trang_thai === 'dang_lam';
                            $isClosed = $shift->trang_thai === 'da_ket_thuc';
                            $hasCheckedIn = $shift->trang_thai_checkin;
                            $hasClosureRecord = $shift->phieuChotCa !== null;
                            $isOverdue = $isActive && !$hasClosureRecord && \Carbon\Carbon::now()->diffInHours($shift->gio_bat_dau) > 12;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isOverdue || (!$hasCheckedIn && $isActive) ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                                {{ $shift->diemBan->ten_diem_ban ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $shift->nguoiDung->ho_ten ?? '-' }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($hasCheckedIn)
                                    <span class="text-green-600 text-lg" title="Đã check-in">✓</span>
                                @else
                                    <span class="text-red-600 text-lg" title="Chưa check-in">✗</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900">
                                {{ number_format($shift->tien_mat_dau_ca ?? 0) }}đ
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($isClosed)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        🔒 Đã kết thúc
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        ✅ Đang làm
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($hasClosureRecord)
                                    <span class="text-green-600 text-lg" title="Đã chốt ca">✓</span>
                                @else
                                    <span class="text-red-600 text-lg font-bold" title="Chưa chốt ca">✗</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <button wire:click="openDetail({{ $shift->id }})" 
                                        class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    Chi tiết
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            @if($shifts->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $shifts->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm p-12 text-center text-gray-500 border border-gray-200">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Không tìm thấy ca làm việc nào</p>
        </div>
    @endif

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedShift)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-indigo-600 rounded-t-xl px-6 py-4 flex justify-between items-center sticky top-0">
                    <h3 class="text-lg font-bold text-white">Chi tiết ca làm việc</h3>
                    <button wire:click="closeDetail" class="text-white hover:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Điểm bán</p>
                            <p class="font-semibold text-lg">{{ $selectedShift->diemBan->ten_diem_ban ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Nhân viên</p>
                            <p class="font-semibold text-lg">{{ $selectedShift->nguoiDung->ho_ten ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Giờ bắt đầu</p>
                            <p class="font-semibold">{{ \Carbon\Carbon::parse($selectedShift->gio_bat_dau)->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Trạng thái</p>
                            <p class="font-semibold">
                                @if($selectedShift->trang_thai === 'dang_lam')
                                    <span class="text-green-600">✅ Đang làm</span>
                                @else
                                    <span class="text-gray-600">🔒 Đã kết thúc</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Check-in Status -->
                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <h4 class="font-bold text-blue-900 mb-3">👤 Check-in</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-sm text-blue-600">Trạng thái</p>
                                <p class="font-bold">
                                    @if($selectedShift->trang_thai_checkin)
                                        <span class="text-green-600">✓ Đã check-in</span>
                                    @else
                                        <span class="text-red-600">✗ Chưa check-in</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600">Tiền mặt đầu ca</p>
                                <p class="font-bold text-blue-900">{{ number_format($selectedShift->tien_mat_dau_ca ?? 0) }}đ</p>
                            </div>
                        </div>
                    </div>

                    <!-- Product Inventory -->
                    @if($selectedShift->chiTietCaLam && $selectedShift->chiTietCaLam->count() > 0)
                        <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <h4 class="font-bold text-gray-900 mb-3">📦 Hàng hóa nhận ca</h4>
                            <div class="space-y-2">
                                @foreach($selectedShift->chiTietCaLam as $detail)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">{{ $detail->sanPham->ten_san_pham ?? 'N/A' }}</span>
                                        <div class="flex gap-4">
                                            <span class="font-semibold">Đầu ca: {{ $detail->so_luong_nhan_ca }}</span>
                                            @if($detail->so_luong_giao_ca)
                                                <span class="text-green-600">Cuối ca: {{ $detail->so_luong_giao_ca }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Closure Status -->
                    @if($selectedShift->phieuChotCa)
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <h4 class="font-bold text-green-900 mb-3">✅ Chốt ca</h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-green-600">Ngày chốt</p>
                                    <p class="font-bold">{{ $selectedShift->phieuChotCa->ngay_chot->format('d/m/Y H:i') }}</p>
                                </div>
                                <div>
                                    <p class="text-green-600">Tổng tiền thực tế</p>
                                    <p class="font-bold">{{ number_format($selectedShift->phieuChotCa->tong_tien_thuc_te) }}đ</p>
                                </div>
                                <div>
                                    <p class="text-green-600">Chênh lệch</p>
                                    <p class="font-bold {{ $selectedShift->phieuChotCa->tien_lech > 0 ? 'text-green-600' : ($selectedShift->phieuChotCa->tien_lech < 0 ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ number_format($selectedShift->phieuChotCa->tien_lech) }}đ
                                    </p>
                                </div>
                                <div>
                                    <p class="text-green-600">Trạng thái</p>
                                    <p class="font-bold">
                                        @if($selectedShift->phieuChotCa->trang_thai === 'cho_duyet')
                                            <span class="text-yellow-600">⏳ Chờ duyệt</span>
                                        @elseif($selectedShift->phieuChotCa->trang_thai === 'da_duyet')
                                            <span class="text-green-600">✅ Đã duyệt</span>
                                        @else
                                            <span class="text-red-600">❌ Từ chối</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <p class="text-red-700 font-semibold">⚠️ Ca làm việc này chưa được chốt</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
