<div class="p-4 space-y-4">
    {{-- Agency Card --}}
    @if($agency)
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
            <h2 class="text-white font-bold text-lg">🏪 Đại lý của tôi</h2>
        </div>
        <div class="p-6">
            <h3 class="font-bold text-xl text-gray-900 mb-2">{{ $agency->ten_diem_ban }}</h3>
            <div class="space-y-2 text-gray-600">
                <p class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    {{ $agency->dia_chi }}
                </p>
                <p class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    {{ $agency->so_dien_thoai ?? 'Chưa cập nhật' }}
                </p>
            </div>
        </div>
    </div>
    @endif

    {{-- Support Ticket Button --}}
    <a href="{{ route('employee.support.ticket') }}" class="block">
        <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl shadow-lg p-6 text-white active:scale-95 transition-transform">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-2xl font-bold mb-1">🎫 Cần hỗ trợ?</div>
                    <div class="text-amber-100">Gửi ticket ngay</div>
                </div>
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </div>
    </a>

    {{-- Today's Shift Card --}}
    @if($todayShift)
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <span class="text-2xl">📅</span>
            Ca làm việc hôm nay
        </h3>
        
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Thời gian:</span>
                <span class="font-semibold text-gray-900">
                    {{ \Carbon\Carbon::parse($todayShift->gio_bat_dau)->format('H:i') }} - 
                    {{ \Carbon\Carbon::parse($todayShift->gio_ket_thuc)->format('H:i') }}
                </span>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-gray-600">Trạng thái:</span>
                <span class="px-3 py-1 rounded-full text-sm font-medium
                    {{ $todayShift->trang_thai === 'dang_lam' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $todayShift->trang_thai === 'chua_bat_dau' ? 'bg-blue-100 text-blue-800' : '' }}
                    {{ $todayShift->trang_thai === 'da_ket_thuc' ? 'bg-gray-100 text-gray-800' : '' }}">
                    {{ match($todayShift->trang_thai) {
                        'dang_lam' => '🟢 Đang làm',
                        'chua_bat_dau' => '🔵 Chưa bắt đầu',
                        'da_ket_thuc' => '⚪ Đã kết thúc',
                        default => $todayShift->trang_thai
                    } }}
                </span>
            </div>

            @if($todayAttendance)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg space-y-2">
                @if($todayAttendance->gio_vao)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">✅ Check-in:</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($todayAttendance->gio_vao)->format('H:i') }}</span>
                </div>
                @endif
                
                @if($todayAttendance->gio_ra)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">🏁 Check-out:</span>
                    <span class="font-medium">{{ \Carbon\Carbon::parse($todayAttendance->gio_ra)->format('H:i') }}</span>
                </div>
                @endif
            </div>
            @endif
            
            @if(!$todayAttendance || !$todayAttendance->gio_vao)
            <button wire:click="checkIn" class="w-full btn-mobile bg-green-600 text-white hover:bg-green-700">
                ✅ Check-in
            </button>
            @elseif(!$todayAttendance->gio_ra)
            <button wire:click="checkOut" class="w-full btn-mobile bg-red-600 text-white hover:bg-red-700">
                🏁 Check-out
            </button>
            @endif
        </div>
    </div>
    @else
    <div class="bg-gray-50 rounded-2xl p-8 text-center">
        <div class="text-4xl mb-2">📅</div>
        <p class="text-gray-600 mb-4">Hôm nay bạn không có ca làm việc</p>
        <a href="{{ route('employee.shifts.register') }}" class="inline-block btn-mobile bg-indigo-600 text-white hover:bg-indigo-700">
            Đăng ký ca
        </a>
    </div>
    @endif

    {{-- Monthly Stats --}}
    <div class="bg-white rounded-2xl shadow-sm p-6">
        <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
            <span class="text-2xl">📊</span>
            Thống kê tháng này
        </h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-xl p-4">
                <div class="text-3xl font-bold text-blue-600">{{ $monthlyStats['total_shifts'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Tổng ca</div>
            </div>
            
            <div class="bg-green-50 rounded-xl p-4">
                <div class="text-3xl font-bold text-green-600">{{ $monthlyStats['completed'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Đã hoàn thành</div>
            </div>
            
            <div class="bg-purple-50 rounded-xl p-4">
                <div class="text-3xl font-bold text-purple-600">{{ $monthlyStats['upcoming'] }}</div>
                <div class="text-sm text-gray-600 mt-1">Sắp tới</div>
            </div>
            
            <div class="bg-amber-50 rounded-xl p-4">
                <div class="text-3xl font-bold text-amber-600">{{ number_format($monthlyStats['total_hours'], 1) }}</div>
                <div class="text-sm text-gray-600 mt-1">Giờ làm</div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="grid grid-cols-2 gap-3">
        <a href="{{ route('employee.shifts.schedule') }}" class="bg-white rounded-xl shadow-sm p-4 active:scale-95 transition-transform">
            <div class="text-3xl mb-2">📅</div>
            <div class="font-semibold text-gray-900">Lịch làm</div>
            <div class="text-xs text-gray-500 mt-1">Xem lịch cá nhân</div>
        </a>
        
        <a href="{{ route('employee.shifts.register') }}" class="bg-white rounded-xl shadow-sm p-4 active:scale-95 transition-transform">
            <div class="text-3xl mb-2">➕</div>
            <div class="font-semibold text-gray-900">Đăng ký ca</div>
            <div class="text-xs text-gray-500 mt-1">Đăng ký ca mới</div>
        </a>
    </div>
</div>
