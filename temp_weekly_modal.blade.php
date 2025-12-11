    <!-- Registration Modal - Weekly Calendar -->
    @if($showRegistrationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showRegistrationModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl" wire:click.stop>
                <!-- Modal Header -->
                <div class="bg-amber-500 rounded-t-xl px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">Đăng ký ca làm việc - Tuần {{  \Carbon\Carbon::parse($weekStartDate)->format('d/m') }} - {{ \Carbon\Carbon::parse($weekStartDate)->addDays(6)->format('d/m/Y') }}</h3>
                    <button wire:click="$set('showRegistrationModal', false)" class="text-white hover:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Week Navigation -->
                <div class="px-6 py-3 bg-gray-50 border-b flex justify-between items-center">
                    <button wire:click="previousWeek" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100">
                        ← Tuần trước
                    </button>
                    <span class="text-sm font-medium text-gray-700">
                        Tuần {{ \Carbon\Carbon::parse($weekStartDate)->weekOfYear }}
                    </span>
                    <button wire:click="nextWeek" class="px-3 py-1.5 text-sm border border-gray-300 rounded hover:bg-gray-100">
                        Tuần sau →
                    </button>
                </div>

                <!-- Modal Content - Calendar Grid -->
                <form wire:submit.prevent="registerShift" class="p-6">
                    <div class="grid grid-cols-7 gap-3 mb-6">
                        @foreach($weekDays as $index => $day)
                            <div class="border rounded-lg p-3 {{ in_array($index, $selectedDays) ? 'bg-amber-50 border-amber-500' : 'bg-white border-gray-200' }}">
                                <!-- Day Header + Checkbox -->
                                <label class="flex items-center justify-between cursor-pointer mb-2">
                                    <div>
                                        <div class="text-xs font-semibold text-gray-600">{{ $day['dayShort'] }}</div>
                                        <div class="text-sm font-bold">{{ \Carbon\Carbon::parse($day['date'])->format('d/m') }}</div>
                                    </div>
                                    <input type="checkbox" 
                                           wire:click="toggleDay({{ $index }})"
                                           {{ in_array($index, $selectedDays) ? 'checked' : '' }}
                                           class="w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                                </label>

                                <!-- Agency Selection -->
                                <div class="mt-2">
                                    <select wire:model="weekDays.{{ $index }}.agencyId" 
                                            class="block w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-amber-500 focus:border-amber-500"
                                            {{ !in_array($index, $selectedDays) ? 'disabled' : '' }}>
                                        <option value="">Điểm bán...</option>
                                        @foreach($availableLocations as $location)
                                            <option value="{{ $location->id }}">{{ $location->ten_diem_ban }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Shift Template Selection -->
                                <div class="mt-2">
                                    <select wire:model="weekDays.{{ $index }}.templateId" 
                                            class="block w-full px-2 py-1 text-xs border border-gray-300 rounded focus:ring-amber-500 focus:border-amber-500"
                                            {{ !in_array($index, $selectedDays) ? 'disabled' : '' }}>
                                        <option value="">Chọn ca...</option>
                                        @if(!empty($weekDays[$index]['agencyId']))
                                            @php
                                                $templates = \App\Models\ShiftTemplate::where('diem_ban_id', $weekDays[$index]['agencyId'])
                                                    ->where('is_active', true)
                                                    ->get();
                                            @endphp
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}">
                                                    {{ $template->name }} ({{ $template->start_time->format('H:i') }}-{{ $template->end_time->format('H:i') }})
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" wire:click="$set('showRegistrationModal', false)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Hủy
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600">
                            Đăng ký {{ count($selectedDays) }} ca
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
