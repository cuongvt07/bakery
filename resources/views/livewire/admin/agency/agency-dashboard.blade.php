<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard Đại lý</h2>
        <a href="{{ route('admin.agencies.index') }}" class="text-indigo-600 hover:text-indigo-800">
            Quản lý danh sách →
        </a>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6  ">
        <div class="flex gap-3">
            <button wire:click="$set('statusFilter', 'all')" 
                    class="px-4 py-2 rounded-lg {{ $statusFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Tất cả ({{ $agencies->count() }})
            </button>
            <button wire:click="$set('statusFilter', 'critical')" 
                    class="px-4 py-2 rounded-lg {{ $statusFilter === 'critical' ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🔴 Cần xử lý
            </button>
            <button wire:click="$set('statusFilter', 'warning')" 
                    class="px-4 py-2 rounded-lg {{ $statusFilter === 'warning' ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🟡 Cảnh báo
            </button>
            <button wire:click="$set('statusFilter', 'ok')" 
                    class="px-4 py-2 rounded-lg {{ $statusFilter === 'ok' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                🟢 Ổn định
            </button>
        </div>
    </div>

    <!-- Agency Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($agencies as $agency)
            <div onclick="window.location='{{ route('admin.agencies.detail', $agency->id) }}'" 
                 class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow border-l-4 cursor-pointer
                       {{ $agency->pendingTickets > 0 ? 'bg-red-50 border-red-500' : ($agency->statusColor === 'red' ? 'border-red-500' : ($agency->statusColor === 'yellow' ? 'border-yellow-500' : 'border-green-500')) }}">
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 hover:text-indigo-600">{{ $agency->ten_diem_ban }}</h3>
                            <p class="text-xs text-gray-500">{{ $agency->ma_diem_ban }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($agency->loai_dai_ly === 'rieng_tu')
                                <span class="text-lg">🏠</span>
                            @else
                                <span class="text-lg">📍</span>
                            @endif
                            <!-- Quick Add Note Button -->
                            <button wire:click="openAddNoteModal({{ $agency->id }})" 
                                    onclick="event.stopPropagation()"
                                    class="text-indigo-600 hover:text-indigo-800 p-1"
                                    title="Thêm ghi chú nhanh">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <span class="text-sm font-medium {{ $agency->statusColor === 'red' ? 'text-red-600' : ($agency->statusColor === 'yellow' ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $agency->statusLabel }}
                        </span>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-1 text-sm text-gray-600">
                        @if($agency->overdueCount > 0)
                            <div class="flex items-center gap-1">
                                <span class="text-red-600 font-bold">⚠️ {{ $agency->overdueCount }}</span>
                                <span>quá hạn</span>
                            </div>
                        @endif
                        
                        @if($agency->pendingTickets > 0)
                            <div class="flex items-center gap-1 font-bold text-red-600">
                                <span class="text-lg">🚨</span>
                                <span>{{ $agency->pendingTickets }} Tickets Khẩn Cấp</span>
                            </div>
                        @elseif($agency->pendingCount > 0)
                            <div class="flex items-center gap-1">
                                <span class="font-semibold">📋 {{ $agency->pendingCount }}</span>
                                <span>việc cần làm</span>
                            </div>
                        @else
                            <div class="text-green-600">✓ Mọi thứ ổn</div>
                        @endif
                    </div>

                    <!-- Recent notes preview -->
                    @if($agency->notes->take(2)->count() > 0)
                        <div class="mt-3 pt-3 border-t border-gray-100">
                            <div class="space-y-1">
                                @foreach($agency->notes->take(2) as $note)
                                    <div class="text-xs text-gray-500 flex items-center gap-1">
                                        <span>{{ $note->type_label }}</span>
                                        <span class="truncate">{{ $note->tieu_de }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Không có đại lý nào
            </div>
        @endforelse
    </div>

    <!-- Note Form Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md" wire:click.stop>
                <!-- Header -->
                <div class="bg-indigo-600 rounded-t-xl px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">➕ Thêm ghi chú nhanh</h3>
                    <button wire:click="$set('showNoteModal', false)" class="text-white hover:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="saveNote" class="p-6">
                    <div class="space-y-4">
                        <!-- Loại ghi chú -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Loại ghi chú *</label>
                            <select wire:model.live="loai" class="w-full px-3 py-2 border rounded-lg">
                                <option value="">-- Chọn loại --</option>
                                @foreach($noteTypes as $type)
                                    @if($type->ma_loai !== 'vat_dung')
                                        <option value="{{ $type->ma_loai }}">{{ $type->display_label }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('loai') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tiêu đề -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tiêu đề *</label>
                            <input type="text" wire:model="tieu_de" class="w-full px-3 py-2 border rounded-lg" placeholder="Nhập tiêu đề...">
                            @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Grid 2 cột -->
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Mức độ -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mức độ</label>
                                <select wire:model="muc_do_quan_trong" class="w-full px-3 py-2 border rounded-lg">
                                    <option value="thap">Thấp</option>
                                    <option value="trung_binh">Trung bình</option>
                                    <option value="cao">Cao</option>
                                    <option value="khan_cap">Khẩn cấp</option>
                                </select>
                            </div>

                            <!-- Ngày nhắc nhở -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ngày nhắc nhở</label>
                                <input type="date" wire:model="ngay_nhac_nho" class="w-full px-3 py-2 border rounded-lg">
                            </div>
                        </div>

                        <!-- Nội dung -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nội dung</label>
                            <textarea wire:model="noi_dung" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Mô tả chi tiết..."></textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex gap-3">
                        <button type="button" wire:click="$set('showNoteModal', false)" class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg">
                            Hủy
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                            Thêm ghi chú
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
