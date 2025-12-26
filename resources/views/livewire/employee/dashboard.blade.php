<div class="p-4 space-y-4 pb-20">
    {{-- Header / Welcome --}}
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Xin chào, {{ auth()->user()->ho_ten }} 👋</h1>
            <p class="text-sm text-gray-500">{{ auth()->user()->ma_nhan_vien }}</p>
        </div>
        <a href="{{ route('employee.support.ticket') }}" class="p-2 bg-white rounded-full shadow-sm border border-gray-100 text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </a>
    </div>

    {{-- Today's Shift Card - Hero Section --}}
    @if($todayShift)
    <div class="bg-indigo-600 rounded-xl shadow-lg p-5 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
        <div class="absolute bottom-0 left-0 -mb-2 -ml-2 w-20 h-20 bg-white opacity-10 rounded-full blur-xl"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="font-bold text-lg">Ca hôm nay</h3>
                    <p class="text-indigo-100 text-sm">{{ \Carbon\Carbon::parse($todayShift->ngay_lam)->format('d/m/Y') }}</p>
                </div>
                <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium backdrop-blur-sm">
                    {{ match($todayShift->trang_thai) {
                        'dang_lam' => 'Đang làm',
                        'chua_bat_dau' => 'Chưa bắt đầu',
                        'da_ket_thuc' => 'Đã kết thúc',
                        default => $todayShift->trang_thai
                    } }}
                </span>
            </div>
            
            <div class="flex items-center gap-4 text-3xl font-bold mb-4">
                {{ \Carbon\Carbon::parse($todayShift->gio_bat_dau)->format('H:i') }}
                <span class="text-indigo-300 text-xl font-normal">-</span>
                {{ \Carbon\Carbon::parse($todayShift->gio_ket_thuc)->format('H:i') }}
            </div>

            @if($todayAttendance)
                <div class="flex gap-2 text-sm bg-white/10 p-2 rounded-lg">
                    @if($todayAttendance->gio_vao)
                        <span>Vào: {{ \Carbon\Carbon::parse($todayAttendance->gio_vao)->format('H:i') }}</span>
                    @endif
                    @if($todayAttendance->gio_ra)
                        <span class="border-l border-white/20 pl-2">Ra: {{ \Carbon\Carbon::parse($todayAttendance->gio_ra)->format('H:i') }}</span>
                    @endif
                </div>
            @endif
            
            <div class="mt-4 grid grid-cols-2 gap-3">
                @if(!$todayAttendance)
                    {{-- No ca_lam_viec record yet - Show Check-in button --}}
                    <button wire:click="checkIn" class="col-span-2 py-3 bg-white text-indigo-600 rounded-lg font-bold hover:bg-indigo-50 transition-colors shadow-sm">
                        Check-in
                    </button>
                @elseif($todayAttendance->trang_thai === 'dang_lam')
                    {{-- Ca_lam_viec exists and status is dang_lam - Show Checkout button --}}
                    <button wire:click="checkOut" class="col-span-2 py-3 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition-colors shadow-sm">
                        Chốt ca
                    </button>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-100">
        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
            <span class="text-2xl">💤</span>
        </div>
        <p class="text-gray-900 font-medium">Hôm nay nghỉ ngơi</p>
        <p class="text-sm text-gray-500 mt-1">Không có ca làm việc nào được xếp</p>
        <a href="{{ route('employee.shifts.register') }}" class="mt-3 inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium">
            Đăng ký ca
        </a>
    </div>
    @endif

    {{-- Stats Grid (Simplified) --}}
    <h3 class="font-bold text-gray-800 text-lg mt-6">Tổng quan tháng</h3>
    <div class="grid grid-cols-2 gap-3">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-900">{{ $monthlyStats['total_shifts'] }}</div>
            <div class="text-xs text-gray-500">Ca làm việc</div>
        </div>
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100">
            <div class="text-2xl font-bold text-gray-900">{{ number_format($monthlyStats['total_hours'], 1) }}</div>
            <div class="text-xs text-gray-500">Giờ công</div>
        </div>
    </div>

    {{-- Quick Shortcuts (If needed, but nav handles most) --}}
    {{-- User asked for buttons to be distinct. --}}
    
</div>
