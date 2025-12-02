<div class="max-w-6xl mx-auto">
    <!-- User Header -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-6 flex items-center">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->ho_ten) }}&background=4F46E5&color=fff" 
                 alt="{{ $user->ho_ten }}" 
                 class="w-20 h-20 rounded-full object-cover border-4 border-indigo-50">
            <div class="ml-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->ho_ten }}</h1>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->vai_tro === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ $user->vai_tro === 'admin' ? 'Quản trị viên' : 'Nhân viên' }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $user->trang_thai === 'hoat_dong' ? 'Đang hoạt động' : 'Đã khóa' }}
                    </span>
                    <span class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $user->email }}
                    </span>
                </div>
            </div>
            <div class="ml-auto flex space-x-3">
                <a href="{{ route('admin.nguoidung.edit', $user->id) }}" 
                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Chỉnh sửa
                </a>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="border-t border-gray-200 bg-gray-50 px-6">
            <nav class="flex space-x-8">
                <button wire:click="setTab('thong_tin')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'thong_tin' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Thông tin cá nhân
                </button>
                <button wire:click="setTab('cham_cong')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'cham_cong' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Lịch sử chấm công
                </button>
                <button wire:click="setTab('luong')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'luong' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Lịch sử lương
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
        
        <!-- Thông tin cá nhân -->
        @if ($activeTab === 'thong_tin')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin liên hệ</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->so_dien_thoai ?? 'Chưa cập nhật' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Địa chỉ</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->dia_chi ?? 'Chưa cập nhật' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ngày tham gia</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y') }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin công việc</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ngày vào làm</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $user->ngay_vao_lam ? $user->ngay_vao_lam->format('d/m/Y') : 'Chưa cập nhật' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Loại lương</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $user->loai_luong === 'theo_ngay' ? 'Theo ngày công' : 'Theo giờ làm' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Lương cơ bản</dt>
                            <dd class="mt-1 text-sm font-semibold text-indigo-600">
                                {{ number_format($user->luong_co_ban) }} VNĐ
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        @endif
        
        <!-- Lịch sử chấm công -->
        @if ($activeTab === 'cham_cong')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ vào</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ ra</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng giờ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Địa điểm</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($chamCongs as $cc)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cc->ngay_cham->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cc->gio_vao ? \Carbon\Carbon::parse($cc->gio_vao)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $cc->gio_ra ? \Carbon\Carbon::parse($cc->gio_ra)->format('H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-indigo-600">
                                    {{ $cc->tong_gio_lam }}h
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $cc->diemBan->ten_diem_ban ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Chưa có dữ liệu chấm công
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
        <!-- Lịch sử lương -->
        @if ($activeTab === 'luong')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tháng/Năm</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lương cơ bản</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số công</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tổng lương</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($bangLuongs as $bl)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $bl->thang }}/{{ $bl->nam }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($bl->luong_co_ban) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $bl->so_ngay_cong }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">
                                    {{ number_format($bl->tong_luong) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $bl->trang_thai === 'da_thanh_toan' ? 'bg-green-100 text-green-800' : ($bl->trang_thai === 'da_chot' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $bl->trang_thai === 'da_thanh_toan' ? 'Đã thanh toán' : ($bl->trang_thai === 'da_chot' ? 'Đã chốt' : 'Chưa chốt') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Chưa có dữ liệu lương
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
    </div>
</div>
