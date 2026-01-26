<div class="px-6 py-4">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Quản lý Thông báo</h2>
            <p class="text-gray-500">Tạo thông báo và quản lý lịch sử gửi</p>
        </div>
        <button wire:click="openCreateModal" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tạo thông báo mới
        </button>
    </div>

    <!-- Stats/Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6 border-b border-gray-200">
        <div class="flex">
            @php
                $tabs = [
                    'all' => ['label' => 'Tất cả', 'icon' => 'M4 6h16M4 12h16M4 18h16', 'color' => 'indigo'],
                    'he_thong' => ['label' => 'Thông báo toàn hệ thống', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color' => 'red'],
                    'canh_bao' => ['label' => 'Thông báo yêu cầu', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'color' => 'yellow'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
                <button wire:click="setTab('{{ $key }}')" 
                    class="px-6 py-4 flex items-center gap-2 border-b-2 font-medium text-sm transition-colors duration-150 {{ $activeTab === $key ? 'border-'.$tab['color'].'-500 text-'.$tab['color'].'-600 bg-'.$tab['color'].'-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/></svg>
                    {{ $tab['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- List -->
    <div class="bg-white rounded-lg shadow-sm w-full">
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-100">
                @foreach($notifications as $notif)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start gap-4">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                @if($notif->loai_thong_bao === 'canh_bao')
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-yellow-100 text-yellow-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                    </span>
                                @elseif($notif->loai_thong_bao === 'he_thong')
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-100 text-red-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /></svg>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </span>
                                @endif
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between">
                                    <h4 class="text-sm font-bold text-gray-900">{{ $notif->tieu_de }}</h4>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ $notif->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $notif->noi_dung }}</p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>
                                        {{ $notif->gui_toi_tat_ca ? 'Tất cả nhân viên' : ($notif->diemBan ? 'Điểm bán: ' . $notif->diemBan->ten_diem_ban : 'Cá nhân: ' . ($notif->nguoiNhan->ho_ten ?? 'N/A')) }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                        Người gửi: {{ $notif->nguoiGui->ho_ten ?? 'Hệ thống' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="p-12 text-center text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                <p>Chưa có thông báo nào trong mục này</p>
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg" @click.away="closeCreateModal">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 class="text-lg font-bold text-gray-800">Tạo thông báo mới</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                        <input type="text" wire:model="tieu_de" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nhập tiêu đề thông báo...">
                        @error('tieu_de') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loại thông báo</label>
                        <select wire:model="loai_thong_bao" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="he_thong">Thông báo toàn hệ thống (Đỏ)</option>
                            <option value="canh_bao">Thông báo yêu cầu (Vàng)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Gửi tới</label>
                        <select wire:model.live="target_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="all">Tất cả nhân viên</option>
                            <option value="agency">Theo điểm bán</option>
                            <option value="user">Nhân viên cụ thể</option>
                        </select>
                    </div>

                    @if($target_type === 'agency')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn điểm bán</label>
                            <select wire:model="target_agency_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Chọn điểm bán --</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                @endforeach
                            </select>
                            @error('target_agency_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    @if($target_type === 'user')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Chọn nhân viên</label>
                            <select wire:model="target_user_id" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->ho_ten }} ({{ $user->ma_nhan_vien }})</option>
                                @endforeach
                            </select>
                            @error('target_user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nội dung</label>
                        <textarea wire:model="noi_dung" rows="4" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nhập nội dung thông báo..."></textarea>
                        @error('noi_dung') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-xl">
                    <button wire:click="closeCreateModal" class="px-4 py-2 text-gray-600 font-medium hover:bg-gray-100 rounded-lg">Hủy</button>
                    <button wire:click="sendNotification" class="px-4 py-2 bg-indigo-600 text-white font-medium hover:bg-indigo-700 rounded-lg shadow-md">Gửi thông báo</button>
                </div>
            </div>
        </div>
    @endif
</div>
