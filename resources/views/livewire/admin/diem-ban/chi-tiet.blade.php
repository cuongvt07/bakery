<div class="max-w-6xl mx-auto">
    <!-- Agency Header -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
        <div class="p-6 flex items-center">
            <div class="w-20 h-20 rounded-lg bg-indigo-100 flex items-center justify-center text-indigo-600 text-2xl font-bold">
                {{ substr($agency->ten_diem_ban, 0, 1) }}
            </div>
            <div class="ml-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ $agency->ten_diem_ban }}</h1>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ $agency->ma_diem_ban }}
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $agency->trang_thai === 'hoat_dong' ? 'bg-green-100 text-green-800' : ($agency->trang_thai === 'tam_ngung' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $agency->trang_thai === 'hoat_dong' ? 'Hoạt động' : ($agency->trang_thai === 'tam_ngung' ? 'Tạm ngưng' : 'Đóng cửa') }}
                    </span>
                    <span class="text-sm text-gray-500 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $agency->dia_chi }}
                    </span>
                </div>
            </div>
            <div class="ml-auto flex space-x-3">
                <a href="{{ route('admin.diemban.assign', $agency->id) }}" 
                   class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Gán nhân viên
                </a>
                <a href="{{ route('admin.diemban.edit', $agency->id) }}" 
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
                    Thông tin chung
                </button>
                <button wire:click="setTab('nhan_vien')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'nhan_vien' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Nhân viên ({{ $agency->nhanVien->count() }})
                </button>
                <button wire:click="setTab('ca_lam')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'ca_lam' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Ca làm sắp tới
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden p-6">
        
        <!-- Thông tin chung -->
        @if ($activeTab === 'thong_tin')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Thông tin liên hệ</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Số điện thoại</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->so_dien_thoai ?? 'Chưa cập nhật' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tọa độ GPS</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                @if($agency->vi_do && $agency->kinh_do)
                                    <a href="https://www.google.com/maps?q={{ $agency->vi_do }},{{ $agency->kinh_do }}" target="_blank" class="text-indigo-600 hover:underline">
                                        {{ $agency->vi_do }}, {{ $agency->kinh_do }}
                                    </a>
                                @else
                                    Chưa cập nhật
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Ghi chú</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $agency->ghi_chu ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Danh sách vật dụng</h3>
                    @if(!empty($agency->thong_tin_vat_dung))
                        <ul class="divide-y divide-gray-200 border border-gray-200 rounded-lg">
                            @foreach($agency->thong_tin_vat_dung as $item)
                                <li class="p-3 flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $item['ten'] ?? '' }}</span>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-500">SL: {{ $item['so_luong'] ?? 0 }}</span>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ ($item['tinh_trang'] ?? '') === 'tot' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ($item['tinh_trang'] ?? '') === 'tot' ? 'Tốt' : 'Hỏng/Cần sửa' }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">Chưa có thông tin vật dụng</p>
                    @endif
                </div>
            </div>
        @endif
        
        <!-- Nhân viên -->
        @if ($activeTab === 'nhan_vien')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày bắt đầu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($agency->nhanVien as $nv)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($nv->ho_ten) }}&background=4F46E5&color=fff" 
                                             class="w-8 h-8 rounded-full mr-3">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $nv->ho_ten }}</div>
                                            <div class="text-sm text-gray-500">{{ $nv->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $nv->vai_tro === 'admin' ? 'Quản lý' : 'Nhân viên' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $nv->pivot->ngay_bat_dau ? \Carbon\Carbon::parse($nv->pivot->ngay_bat_dau)->format('d/m/Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $nv->pivot->trang_thai === 'dang_lam_viec' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $nv->pivot->trang_thai === 'dang_lam_viec' ? 'Đang làm việc' : 'Đã nghỉ' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Chưa có nhân viên nào được gán vào điểm bán này
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
        <!-- Ca làm sắp tới -->
        @if ($activeTab === 'ca_lam')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Giờ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nhân viên</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($caLamViecs as $ca)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ca->ngay_lam->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($ca->gio_bat_dau)->format('H:i') }} - {{ \Carbon\Carbon::parse($ca->gio_ket_thuc)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $ca->nguoiDung->ho_ten ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $ca->trang_thai }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Không có ca làm việc sắp tới
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
        
    </div>
</div>
