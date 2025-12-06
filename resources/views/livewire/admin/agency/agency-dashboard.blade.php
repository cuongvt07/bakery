<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">Dashboard Đại lý</h2>
        <a href="{{ route('admin.agencies.index') }}" class="text-indigo-600 hover:text-indigo-800">
            Quản lý danh sách →
        </a>
    </div>

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
            <a href="{{ route('admin.agencies.detail', $agency->id) }}" 
               class="block bg-white rounded-lg shadow hover:shadow-lg transition-shadow border-l-4 
                      {{ $agency->statusColor === 'red' ? 'border-red-500' : ($agency->statusColor === 'yellow' ? 'border-yellow-500' : 'border-green-500') }}">
                <div class="p-4">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $agency->ten_diem_ban }}</h3>
                            <p class="text-xs text-gray-500">{{ $agency->ma_diem_ban }}</p>
                        </div>
                        @if($agency->loai_dai_ly === 'rieng_tu')
                            <span class="text-lg">🏠</span>
                        @else
                            <span class="text-lg">📍</span>
                        @endif
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
                        @if($agency->pendingCount > 0)
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
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                Không có đại lý nào
            </div>
        @endforelse
    </div>
</div>
