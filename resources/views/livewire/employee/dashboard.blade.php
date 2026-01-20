<div class="p-4 space-y-4 pb-20">
    {{-- Header / Welcome --}}
    <div class="flex items-center justify-between mb-2">
        <div>
            <h1 class="text-xl font-bold text-gray-900">Xin chào, {{ auth()->user()->ho_ten }} 👋</h1>
            <p class="text-sm text-gray-500">{{ auth()->user()->ma_nhan_vien }}</p>
        </div>
        <a href="{{ route('employee.support.ticket') }}"
            class="p-2 bg-white rounded-full shadow-sm border border-gray-100 text-indigo-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </a>
    </div>

    {{-- Today's Shift Card - Hero Section --}}
    @if ($todayShift)
        <div class="bg-indigo-600 rounded-xl shadow-lg p-5 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mt-2 -mr-2 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="absolute bottom-0 left-0 -mb-2 -ml-2 w-20 h-20 bg-white opacity-10 rounded-full blur-xl"></div>

            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-lg">Ca hôm nay</h3>
                        <p class="text-indigo-100 text-sm">
                            {{ \Carbon\Carbon::parse($todayShift->ngay_lam)->format('d/m/Y') }}</p>
                    </div>
                    <span class="px-2 py-1 bg-white/20 rounded-lg text-xs font-medium backdrop-blur-sm">
                        {{ match ($todayShift->trang_thai) {
                            'dang_lam' => 'Đang làm',
                            'chua_bat_dau' => 'Chưa bắt đầu',
                            'da_ket_thuc' => 'Đã kết thúc',
                            default => $todayShift->trang_thai,
                        } }}
                    </span>
                </div>

                <div class="flex items-center gap-4 text-3xl font-bold mb-4">
                    {{ \Carbon\Carbon::parse($todayShift->gio_bat_dau)->format('H:i') }}
                    <span class="text-indigo-300 text-xl font-normal">-</span>
                    {{ \Carbon\Carbon::parse($todayShift->gio_ket_thuc)->format('H:i') }}
                </div>

                @if ($todayAttendance && $todayAttendance->thoi_gian_checkin)
                    <div class="mt-4 space-y-2">
                        <div class="flex justify-between items-center text-sm bg-white/10 p-2 rounded-lg">
                            <span class="text-indigo-200">Giờ vào thực tế:</span>
                            <span
                                class="font-bold">{{ \Carbon\Carbon::parse($todayAttendance->thoi_gian_checkin)->format('H:i') }}</span>
                        </div>

                        @if ($todayAttendance->trang_thai === 'dang_lam')
                            @php
                                $expectedCheckout = $todayAttendance->expected_checkout_time;
                                $targetTime = $expectedCheckout->toIso8601String();
                            @endphp

                            <div class="flex justify-between items-center text-sm bg-white/10 p-2 rounded-lg">
                                <span class="text-indigo-200">Dự kiến ra:</span>
                                <span class="font-bold">{{ $expectedCheckout->format('H:i') }}</span>
                            </div>

                            <div x-data="{
                                target: new Date('{{ $targetTime }}'),
                                now: new Date(),
                                timeDisplay: '',
                                isOvertime: false,
                                updateTime() {
                                    this.now = new Date();
                                    let diff = this.target - this.now;
                                    this.isOvertime = diff < 0;
                            
                                    diff = Math.abs(diff);
                            
                                    const hours = Math.floor(diff / (1000 * 60 * 60));
                                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                            
                                    let text = '';
                                    if (hours > 0) text += hours + ' giờ ';
                                    text += minutes + ' phút ';
                            
                                    // Show seconds if less than 1 hour remains or is overtime
                                    if (hours === 0 || this.isOvertime) {
                                        text += seconds + ' giây';
                                    }
                            
                                    this.timeDisplay = text;
                                }
                            }" x-init="updateTime();
                            setInterval(() => updateTime(), 1000)"
                                class="flex justify-between items-center text-sm p-2 rounded-lg border transition-colors duration-300"
                                :class="isOvertime ? 'bg-red-500/20 text-red-100 border-red-500/30' :
                                    'bg-green-500/20 text-green-100 border-green-500/30'">
                                <span x-text="isOvertime ? 'Quá giờ:' : 'Còn lại:'"></span>
                                <span class="font-bold font-mono" x-text="timeDisplay"></span>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-4 grid grid-cols-2 gap-3">
                    @if (!$todayAttendance)
                        {{-- No ca_lam_viec record yet - Show Check-in button --}}
                        <button wire:click="checkIn"
                            class="col-span-2 py-3 bg-white text-indigo-600 rounded-lg font-bold hover:bg-indigo-50 transition-colors shadow-sm">
                            Check-in
                        </button>
                    @elseif($todayAttendance->trang_thai === 'dang_lam')
                        {{-- Ca_lam_viec exists and status is dang_lam - Show Checkout button --}}
                        <button wire:click="checkOut"
                            class="col-span-2 py-3 bg-orange-600 text-white rounded-lg font-bold hover:bg-orange-700 transition-colors shadow-sm">
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
            <a href="{{ route('employee.shifts.register') }}"
                class="mt-3 inline-block px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-sm font-medium">
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

    {{-- Quick Actions --}}
    <h3 class="font-bold text-gray-800 text-lg mt-6">Truy cập nhanh</h3>
    <div class="space-y-3">
        <a href="{{ route('employee.shifts.system-schedule') }}"
            class="block bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg p-4 text-white hover:shadow-xl transition-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-base">Lịch chung hệ thống</div>
                        <div class="text-xs text-indigo-100">Xem lịch làm việc tất cả các điểm</div>
                    </div>
                </div>
                <svg class="w-5 h-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </a>
    </div>

    {{-- Quick Shortcuts (If needed, but nav handles most) --}}
    {{-- User asked for buttons to be distinct. --}}


</div>
