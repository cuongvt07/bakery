{{-- UNCLOSED SHIFTS WARNING - Add this after "Ca hôm nay" section in dashboard.blade.php --}}
@if (count($unclosedShifts) > 0)
    <div class="bg-orange-50 border-l-4 border-orange-400 rounded-r-xl p-4 shadow-sm">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-orange-600 mr-3 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div class="flex-1">
                <h3 class="font-bold text-orange-800 mb-2">
                    ⚠️ Bạn có {{ count($unclosedShifts) }} ca chưa chốt
                </h3>
                <div class="space-y-2">
                    @foreach ($unclosedShifts as $shift)
                        @php
                            $expectedCheckout = $shift->expected_checkout_time;
                            $isLate = now()->gt($expectedCheckout->copy()->addMinutes(15));
                            $isVeryLate = now()->gt($expectedCheckout->copy()->addHours(2));
                        @endphp
                        <div
                            class="bg-white rounded-lg p-3 border {{ $isVeryLate ? 'border-red-300' : ($isLate ? 'border-orange-300' : 'border-gray-200') }}">
                            <div class="flex justify-between items-center">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">
                                        Ca {{ \Carbon\Carbon::parse($shift->gio_bat_dau)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($shift->gio_ket_thuc)->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-600 mt-1">
                                        Check-in: {{ $shift->thoi_gian_checkin->format('H:i') }}
                                    </div>
                                    @if ($isLate)
                                        <div
                                            class="text-xs {{ $isVeryLate ? 'text-red-600' : 'text-orange-600' }} font-medium mt-1">
                                            {{ $isVeryLate ? '🔴' : '⚠️' }} Quá giờ
                                            {{ $expectedCheckout->diffForHumans(null, true) }}
                                        </div>
                                    @endif
                                </div>
                                @php
                                    $checkinType = auth()->user()->getCheckinType();
                                    $closeUrl =
                                        $checkinType === 'sales'
                                            ? route('admin.shift.closing', ['confirm_closing' => 1])
                                            : route('employee.shifts.check-in');
                                @endphp
                                <a href="{{ $closeUrl }}"
                                    class="ml-3 px-4 py-2 {{ $isVeryLate ? 'bg-red-600 hover:bg-red-700' : 'bg-orange-600 hover:bg-orange-700' }} text-white text-sm font-bold rounded-lg whitespace-nowrap">
                                    Chốt ca
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
