<div>
    <!-- Header -->
    <div class="flex justify-between items-start mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">{{ $agency->ten_diem_ban }}</h2>
            <p class="text-sm text-gray-500">{{ $agency->ma_diem_ban }} • {{ $agency->dia_chi }}</p>
        </div>
        <div class="flex gap-3 items-center">
            <!-- Quick Settings Buttons (Inline Modals) -->
            <button wire:click="$set('showTypeModal', true)" 
               class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm flex items-center gap-1" 
               title="Quản lý loại ghi chú">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                </svg>
                <span class="hidden md:inline">Loại ghi chú</span>
            </button>
            <button wire:click="$set('showLocationModal', true)" 
               class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm flex items-center gap-1" 
               title="Quản lý vị trí vật dụng">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span class="hidden md:inline">Vị trí</span>
            </button>
            <a href="{{ route('admin.agencies.dashboard') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                ← Dashboard
            </a>
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
        <a href="{{ route('admin.agencies.notes.create', $agency->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 inline-block">
            + Thêm ghi chú mới
        </a>
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
                                <a href="{{ route('admin.agencies.notes.edit', [$agency->id, $note->id]) }}" class="text-blue-600 hover:text-blue-800 mr-2">Sửa</a>
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

    <!-- Inline Modal: Note Type Management -->
    @if($showTypeModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showTypeModal', false)">
            <div class="relative top-10 mx-auto p-0 border w-full max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <livewire:admin.agency.note-type-list :agencyId="$agency->id" :key="'types-'.$agency->id" />
            </div>
        </div>
    @endif

    <!-- Inline Modal: Location Management -->
    @if($showLocationModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" wire:click="$set('showLocationModal', false)">
            <div class="relative top-10 mx-auto p-0 border w-full max-w-4xl shadow-lg rounded-md bg-white" wire:click.stop>
                <livewire:admin.agency.location-list :agencyId="$agency->id" :key="'locs-'.$agency->id" />
            </div>
        </div>
    @endif
</div>
