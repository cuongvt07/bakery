<div>
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $agency->ten_diem_ban }}</h2>
            <p class="text-sm text-gray-500">{{ $agency->ma_diem_ban }} • {{ $agency->dia_chi }}</p>
        </div>
        <div class="flex gap-3 items-center">
            <div class="flex flex-wrap gap-3 items-center">
            <!-- Note Type Management Button - BIG -->
            <button wire:click="$toggle('showTypeModal')" 
                    class="bg-white border-2 border-indigo-600 text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-lg flex items-center gap-2 shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                <span class="font-semibold">Thêm Tab Ghi chú</span>
            </button>
            <a href="{{ route('admin.agencies.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                ← Dashboard
            </a>
        </div>
        </div>
    </div>

    <!-- Dynamic Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto">
                <!-- All Tab -->
                <button wire:click="$set('activeTab', 'all')"
                        class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Tất cả
                </button>
                
                <!-- Dynamic Type Tabs -->
                @foreach($noteTypes as $type)
                    <button wire:click="$set('activeTab', '{{ $type->ma_loai }}')"
                            class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === $type->ma_loai ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $type->display_label }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Add Note Button -->
    <div class="mb-4">
        <button wire:click="openAddNoteModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-block">
            + Thêm ghi chú mới
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <!-- Notes List -->
    @if($notes->count() > 0)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Loại</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tiêu đề</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-28">Ngày nhắc</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Mức độ</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase w-16">✓</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notes as $note)
                        <tr class="hover:bg-gray-50 {{ $note->da_xu_ly ? 'opacity-60 bg-gray-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap text-xs">
                                {{ $note->type_label }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                <div class="max-w-lg overflow-hidden text-ellipsis {{ $note->da_xu_ly ? 'line-through' : '' }}" title="{{ $note->tieu_de }}">
                                    {{ $note->tieu_de }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-500">
                                @if($note->ngay_nhac_nho)
                                    {{ \Carbon\Carbon::parse($note->ngay_nhac_nho)->format('d/m/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs">
                                <span class="px-2 py-0.5 rounded-full text-xs
                                    {{ $note->muc_do_quan_trong === 'khan_cap' ? 'bg-red-100 text-red-800' : 
                                       ($note->muc_do_quan_trong === 'cao' ? 'bg-orange-100 text-orange-800' : 
                                      ($note->muc_do_quan_trong === 'trung_binh' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ substr(ucfirst(str_replace('_', ' ', $note->muc_do_quan_trong)), 0, 1) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                @if($note->da_xu_ly)
                                    <span class="text-green-600 text-lg">✓</span>
                                @else
                                    <span class="text-gray-300 text-lg">○</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                <button wire:click="openEditNoteModal({{ $note->id }})" class="text-blue-600 hover:text-blue-800 mr-2">Sửa</button>
                                <button wire:click="deleteNote({{ $note->id }})" wire:confirm="Xóa ghi chú này?" class="text-red-600 hover:text-red-800 mr-2">Xóa</button>
                                <button wire:click="toggleComplete({{ $note->id }})" class="text-{{ $note->da_xu_ly ? 'yellow' : 'green' }}-600 hover:text-{{ $note->da_xu_ly ? 'yellow' : 'green' }}-800 text-sm">
                                    {{ $note->da_xu_ly ? '↻' : '✓' }}
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-500">
            Chưa có ghi chú nào
        </div>
    @endif

    <!-- Note Form Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showNoteModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto" wire:click.stop>
                <!-- Header -->
                <div class="bg-indigo-600 rounded-t-xl px-6 py-4 flex justify-between items-center sticky top-0">
                    <h3 class="text-lg font-bold text-white">{{ $editingNoteId ? '✏️ Sửa ghi chú' : '➕ Thêm ghi chú' }}</h3>
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
                            {{ $editingNoteId ? 'Cập nhật' : 'Thêm ghi chú' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Inline Modal: Note Type Management -->
    @if($showTypeModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showTypeModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" wire:click.stop>
                <livewire:admin.agency.note-type-list :agencyId="$agency->id" :key="'types-'.$agency->id" />
            </div>
        </div>
    @endif

    <!-- Inline Modal: Location Management -->
    @if($showLocationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showLocationModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                <livewire:admin.agency.location-list :agencyId="$agency->id" :key="'locs-'.$agency->id" />
            </div>
        </div>
    @endif
</div>
