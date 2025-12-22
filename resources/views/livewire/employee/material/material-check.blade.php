<div class="p-4 space-y-4 pb-20">
    <div class="flex items-center justify-between mb-2">
        <h1 class="text-xl font-bold text-gray-900">📦 Tra cứu Vật dụng</h1>
        
        {{-- Agency Selector if multiple --}}
        @if($agencies->count() > 1)
            <select wire:model.live="selectedAgencyId" class="text-sm border-none bg-white shadow-sm rounded-lg px-3 py-1.5 focus:ring-1 focus:ring-indigo-500">
                @foreach($agencies as $agency)
                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                @endforeach
            </select>
        @else
            <span class="text-sm text-gray-500">{{ $agencies->first()->ten_diem_ban ?? '' }}</span>
        @endif
    </div>

    {{-- Search Bar --}}
    <div class="relative">
        <input type="text" wire:model.live.debounce.300ms="search" 
               class="w-full bg-white border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block pl-10 p-3 shadow-sm" 
               placeholder="Tìm tên hoặc mã vật dụng...">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    {{-- Material List --}}
    <div class="space-y-3">
        @forelse($materials as $item)
            <div class="bg-white rounded-xl p-4 shadow-sm border border-gray-100 flex items-start gap-3 active:bg-gray-50 transition-colors">
                {{-- Icon / Image Placeholder --}}
                <div class="w-12 h-12 rounded-lg bg-indigo-50 flex items-center justify-center flex-shrink-0 text-indigo-500">
                    @if(!empty($item->hinh_anh))
                        {{-- If image exists, could show it here --}}
                        <img src="{{ asset(is_array($item->hinh_anh) ? $item->hinh_anh[0] : $item->hinh_anh) }}" class="w-full h-full object-cover rounded-lg" onerror="this.style.display='none'">
                    @else
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <div class="flex justify-between items-start">
                        <h3 class="font-bold text-gray-900 truncate">{{ $item->tieu_de }}</h3>
                        {{-- Optional Status Badge --}}
                    </div>
                    
                    @if($item->location)
                        <div class="mb-1">
                            <p class="text-sm font-bold text-indigo-700">{{ $item->location->ten_vi_tri }}</p>
                            @if($item->location->mo_ta)
                                <p class="text-xs text-gray-500">{{ $item->location->mo_ta }}</p>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-gray-400 italic mb-1">Chưa có vị trí</p>
                    @endif

                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-1">
                        @if(isset($item->metadata['ma_vat_dung']))
                            <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $item->metadata['ma_vat_dung'] }}</span>
                        @endif
                        <span>-</span>
                        <span>{{ $item->agency->ten_diem_ban ?? '' }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900">Không tìm thấy vật dụng</h3>
                <p class="text-gray-500 mt-1">Thử tìm kiếm với từ khóa khác</p>
            </div>
        @endforelse
    </div>
    
    {{-- Pagination --}}
    @if($materials->hasPages())
        <div class="pt-4">
            {{ $materials->links() }}
        </div>
    @endif
</div>
