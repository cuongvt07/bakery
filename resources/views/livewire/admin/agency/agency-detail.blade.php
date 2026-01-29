<div>
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $agency->ten_diem_ban }}</h2>
            <p class="text-sm text-gray-500">{{ $agency->ma_diem_ban }} • {{ $agency->dia_chi }}</p>
        </div>
        <div class="flex gap-3 items-center">
            <div class="flex flex-wrap gap-3 items-center">
                <!-- Add Note Button - Moved Here -->
                <button wire:click="openAddNoteModal" 
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg flex items-center gap-2 shadow-md hover:shadow-lg transition-all transform hover:scale-105 border-2 border-transparent">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span class="font-semibold">Thêm Ghi chú</span>
                </button>

                <!-- Note Type Management Button -->
                <button wire:click="$toggle('showTypeModal')" 
                        class="bg-white border-2 border-indigo-600 text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-lg flex items-center gap-2 shadow-md hover:shadow-lg transition-all transform hover:scale-105">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span class="font-semibold">Thêm Tab Ghi chú</span>
                </button>
                
                <a href="{{ route('admin.agencies.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-sm ml-2">
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
                            class="px-6 py-3 border-b-2 font-medium text-sm whitespace-nowrap flex items-center gap-1 {{ $activeTab === $type->ma_loai ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $type->display_label }}
                            <span class="ml-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                {{ $pendingTicketCount }}
                            </span>
                        @endif
                        @if(isset($urgentNoteCounts[$type->ma_loai]))
                             <span class="ml-1 bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[20px] text-center">
                                {{ $urgentNoteCounts[$type->ma_loai] }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Filters -->
    <div class="mb-6 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <div class="flex flex-col md:flex-row gap-4">
            <!-- 1. Search (Approx 45%) -->
            <div class="w-full md:w-5/12 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="search" 
                       class="block w-full pl-10 pr-3 py-2.5 border-2 border-gray-200 rounded-lg leading-5 bg-gray-50 placeholder-gray-500 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-0 sm:text-sm transition duration-150 ease-in-out"
                       placeholder="Tìm kiếm tiêu đề, nội dung...">
            </div>

            <!-- 2. Filters Group (Remaining width) -->
            <div class="w-full md:w-7/12 grid grid-cols-1 md:grid-cols-3 gap-2">
                <!-- Type Filter -->
                <select wire:model.live="activeTab" class="block w-full py-2.5 px-3 border-2 border-gray-200 bg-gray-50 rounded-lg text-sm focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-0">
                    <option value="all">📂 Tất cả loại</option>
                    @foreach($noteTypes as $type)
                        <option value="{{ $type->ma_loai }}">{{ $type->display_label }}</option>
                    @endforeach
                </select>

                <!-- Status Filter -->
                <select wire:model.live="statusFilter" class="block w-full py-2.5 px-3 border-2 border-gray-200 bg-gray-50 rounded-lg text-sm focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-0">
                    <option value="">⚡ Tất cả trạng thái</option>
                    <option value="pending">⏳ Chưa xử lý</option>
                    <option value="processed">✅ Đã xử lý</option>
                </select>

                <!-- Date Filter -->
                <input type="date" wire:model.live="dateFilter" class="block w-full py-2.5 px-3 border-2 border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-600 focus:outline-none focus:bg-white focus:border-indigo-500 focus:ring-0">
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <!-- Tickets List (Only in Alerts Tab) -->
    @if($activeTab === 'canh_bao' && isset($tickets) && $tickets->count() > 0)
        <div class="mb-6 bg-white rounded-lg shadow-sm overflow-hidden border border-red-100">
            <div class="px-6 py-4 border-b border-red-100 bg-red-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-red-800 flex items-center gap-2">
                    <span>🚨 Tickets Khẩn Cấp</span>
                    <span class="px-2 py-0.5 bg-red-200 text-red-800 text-xs rounded-full">{{ $tickets->count() }}</span>
                </h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày giờ</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nhân viên</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($tickets as $ticket)
                        <tr class="hover:bg-red-50/30">
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                {{ $ticket->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $ticket->nguoiDung->ho_ten ?? 'N/A' }}
                                <div class="text-xs text-gray-500">{{ $ticket->nguoiDung->ma_nhan_vien ?? '' }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                                <div class="truncate" title="{{ $ticket->reason_text }}">
                                    {{ $ticket->reason_text }}
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($ticket->trang_thai === 'cho_duyet')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Chờ xử lý
                                    </span>
                                @elseif($ticket->trang_thai === 'dang_xu_ly')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Đang xử lý
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Đã xử lý
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="viewTicketDetail({{ $ticket->id }})" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 px-3 py-1 rounded hover:bg-indigo-100 transition-colors">
                                    Chi tiết
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Notes List -->
    @if($notes->count() > 0)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-24">Loại</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase w-48">Tiêu đề</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Mô tả</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase w-28">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($notes as $note)
                        <tr class="hover:bg-gray-50 {{ $note->da_xu_ly ? 'opacity-60 bg-gray-50' : ($note->isNearReminder() ? 'bg-red-50' : '') }}">
                            <td class="px-4 py-3 whitespace-nowrap text-xs">
                                {{ $note->type_label }}
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900 font-medium">
                                <div class="max-w-xs overflow-hidden text-ellipsis" title="{{ $note->tieu_de }}">
                                    {{ $note->tieu_de }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs md:max-w-sm">
                                <div class="truncate" title="{{ $note->noi_dung }}">
                                    {{ $note->noi_dung }}
                                </div>
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
            <!-- Pagination -->
            @if($notes->hasPages())
                <div class="px-4 py-3 border-t bg-gray-50">
                    {{ $notes->links() }}
                </div>
            @endif
        </div>
        <div class="mt-2 text-sm text-gray-500 text-right">
            Hiển thị {{ $notes->count() }} kết quả
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-500">
            Chưa có ghi chú nào
        </div>
    @endif

    <!-- Note Form Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
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
                        @if($editingNoteId && $ngay_nhac_nho)
                             <button type="button" wire:click="extendReminder({{ $editingNoteId }})" class="px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg whitespace-nowrap">
                                ✅ Đã xử lý (+1 tháng)
                            </button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Inline Modal: Note Type Management -->
    @if($showTypeModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="$set('showTypeModal', false)">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="sticky top-0 bg-white border-b px-4 py-3 flex justify-between items-center z-10">
                    <h3 class="font-bold text-gray-800">Quản lý Tab Ghi chú</h3>
                    <button wire:click="$set('showTypeModal', false)" class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <livewire:admin.agency.note-type-list :agencyId="$agency->id" :key="'types-'.$agency->id" />
            </div>
        </div>
    @endif

    <!-- Inline Modal: Location Management -->
    @if($showLocationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" wire:click.stop>
                <livewire:admin.agency.location-list :agencyId="$agency->id" :key="'locs-'.$agency->id" />
            </div>
        </div>
    @endif

    <!-- Ticket Detail Modal -->
    @if($showTicketModal && $selectedTicket)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4" wire:click="closeTicketModal">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto" wire:click.stop>
                <div class="bg-red-600 rounded-t-xl px-6 py-4 flex justify-between items-center sticky top-0">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Chi tiết Ticket Khẩn Cấp
                    </h3>
                    <button wire:click="closeTicketModal" class="text-white hover:bg-white/20 rounded-lg p-1.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-3 pb-4 border-b">
                        <div class="bg-gray-100 p-2 rounded-full">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ $selectedTicket->nguoiDung->ho_ten ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $selectedTicket->nguoiDung->ma_nhan_vien ?? '' }}</div>
                        </div>
                        <div class="ml-auto text-sm text-gray-500">
                            {{ $selectedTicket->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    
                    <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                        <h4 class="font-bold text-red-800 text-sm mb-2">NỘI DUNG YÊU CẦU</h4>
                        <p class="text-gray-800 whitespace-pre-wrap">{{ $selectedTicket->reason_text }}</p>
                    </div>

                    @if($selectedTicket->trang_thai === 'cho_duyet')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú xác nhận</label>
                            <textarea wire:model="approvalNote" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Nhập ghi chú (nếu có)..."></textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button wire:click="closeTicketModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Đóng</button>
                            <button wire:click="confirmTicket({{ $selectedTicket->id }})" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-bold rounded-lg flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Xác nhận (Đang xử lý)
                            </button>
                        </div>
                    @elseif($selectedTicket->trang_thai === 'dang_xu_ly')
                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-200 mb-4">
                            <span class="text-yellow-700 font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Đang xử lý
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ghi chú hoàn thành</label>
                            <textarea wire:model="approvalNote" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Nhập ghi chú xử lý..."></textarea>
                        </div>
                        <div class="pt-4 flex justify-end gap-3">
                            <button wire:click="closeTicketModal" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg">Đóng</button>
                            <button wire:click="resolveTicket({{ $selectedTicket->id }})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Hoàn thành (Duyệt)
                            </button>
                        </div>
                    @else
                        <div class="pt-4 border-t text-center">
                            <span class="inline-flex items-center gap-1 text-green-600 font-bold bg-green-50 px-4 py-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Đã được xử lý
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
