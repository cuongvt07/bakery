<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $agency->ten_diem_ban }}</h2>
            <p class="text-sm text-gray-500">{{ $agency->ma_diem_ban }} • {{ $agency->dia_chi }}</p>
        </div>
        <a href="{{ route('admin.agencies.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">
            ← Quay lại Dashboard
        </a>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button wire:click="$set('activeTab', 'all')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Tất cả
                </button>
                <button wire:click="$set('activeTab', 'hop_dong')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'hop_dong' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    📄 Hợp đồng
                </button>
                <button wire:click="$set('activeTab', 'chi_phi')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'chi_phi' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    💰 Chi phí
                </button>
                <button wire:click="$set('activeTab', 'cong_an')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'cong_an' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    👮 Công an
                </button>
                <button wire:click="$set('activeTab', 'vat_dung')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'vat_dung' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    🪑 Vật dụng
                </button>
                <button wire:click="$set('activeTab', 'bien_bao')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'bien_bao' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    🪧 Biển bảo
                </button>
            </nav>
        </div>
    </div>

    <!-- Add Note Button -->
    <div class="mb-4">
        <a href="{{ route('admin.agencies.notes.create', $agency->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-block">
            + Thêm ghi chú mới
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">{{ session('success') }}</div>
    @endif

    <!-- Notes List -->
    <div class="space-y-3">
        @php
            $pendingNotes = $notes->where('da_xu_ly', false);
            $completedNotes = $notes->where('da_xu_ly', true);
        @endphp

        @if($pendingNotes->count() > 0)
            <div class="mb-6">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Cần xử lý ({{ $pendingNotes->count() }})</h3>
                @foreach($pendingNotes as $note)
                    <div class="bg-white rounded-lg shadow-sm border-l-4 p-4 mb-3
                                {{ $note->priority_color === 'red' ? 'border-red-500' : ($note->priority_color === 'orange' ? 'border-orange-500' : ($note->priority_color === 'yellow' ? 'border-yellow-500' : 'border-green-500')) }}">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm">{{ $note->type_label }}</span>
                                    <span class="px-2 py-0.5 text-xs rounded-full
                                                 {{ $note->muc_do_quan_trong === 'khan_cap' ? 'bg-red-100 text-red-800' : ($note->muc_do_quan_trong === 'cao' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-700') }}">
                                        {{ $note->priority_label }}
                                    </span>
                                    @if($note->isOverdue())
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-red-600 text-white font-bold">
                                            QUÁ HẠN
                                        </span>
                                    @elseif($note->isDueSoon())
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-orange-500 text-white">
                                            Sắp đến hạn
                                        </span>
                                    @endif
                                </div>
                                <h4 class="font-semibold text-gray-900">{{ $note->tieu_de }}</h4>
                                @if($note->noi_dung)
                                    <p class="text-sm text-gray-600 mt-1">{{ $note->noi_dung }}</p>
                                @endif
                                @if($note->ngay_nhac_nho)
                                    <p class="text-xs text-gray-500 mt-2">📅 Hạn: {{ $note->ngay_nhac_nho->format('d/m/Y') }}</p>
                                @endif
                            </div>
                            <button wire:click="toggleComplete({{ $note->id }})"
                                    class="ml-4 px-3 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                ✓ Xong
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($completedNotes->count() > 0)
            <div>
                <h3 class="text-sm font-semibold text-gray-500 mb-3">Đã xử lý ({{ $completedNotes->count() }})</h3>
                @foreach($completedNotes as $note)
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4 mb-3 opacity-60">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm text-gray-500">{{ $note->type_label }}</span>
                                    <span class="text-green-600">✓</span>
                                </div>
                                <h4 class="font-medium text-gray-700 line-through">{{ $note->tieu_de }}</h4>
                            </div>
                            <button wire:click="toggleComplete({{ $note->id }})"
                                    class="ml-4 px-3 py-1 bg-gray-400 text-white text-xs rounded hover:bg-gray-500">
                                Hoàn tác
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($notes->count() === 0)
            <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-500">
                Chưa có ghi chú nào
            </div>
        @endif
    </div>
</div>
