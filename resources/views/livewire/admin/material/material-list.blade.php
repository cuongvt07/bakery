<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-800">Quản lý vật tư + nguyên liệu</h2>
            <p class="text-sm text-gray-500 mt-1">Quản lý tập trung vật tư & vị trí của các đại lý</p>
        </div>
        @if($activeTab === 'materials')
            <a href="{{ route('admin.materials.create') }}" 
               class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm vật tư
            </a>
        @else
            <button wire:click="openAddLocationModal" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Thêm vị trí
            </button>
        @endif
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm mb-4">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button wire:click="$set('activeTab', 'materials')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'materials' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Vật tư & Nguyên liệu
                </button>
                <button wire:click="$set('activeTab', 'locations')"
                        class="px-6 py-3 border-b-2 font-medium text-sm {{ $activeTab === 'locations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Vị trí
                </button>
            </nav>
        </div>
    </div>

    <!-- Messages -->
    @if (session('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">{{ session('message') }}</div>
    @endif
    @if (session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">{{ session('error') }}</div>
    @endif

    @if($activeTab === 'materials')
        @include('livewire.admin.material.partials.materials-tab')
    @else
        @include('livewire.admin.material.partials.locations-tab')
    @endif

    <!-- Location Modal - Clean & Simple -->
    @if($showLocationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md" wire:click.stop>
                <!-- Header -->
                <div class="bg-indigo-600 rounded-t-xl px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white">{{ $editingLocation ? 'Sửa vị trí' : 'Thêm vị trí' }}</h3>
                    <button wire:click="$set('showLocationModal', false)" class="text-white hover:bg-white/20 rounded-lg p-1.5 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Form -->
                <form wire:submit="saveLocation" class="p-6">
                    <div class="space-y-4">
                        <!-- Đại lý -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Đại lý *</label>
                            <select wire:model.live="location_diem_ban_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    {{ $editingLocation ? 'disabled' : '' }}>
                                <option value="">Chọn đại lý...</option>
                                @foreach($agencies as $agency)
                                    <option value="{{ $agency->id }}">{{ $agency->ten_diem_ban }}</option>
                                @endforeach
                            </select>
                            @error('location_diem_ban_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Tên vị trí -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tên vị trí *</label>
                            <input type="text" 
                                   wire:model="ten_vi_tri" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="VD: Kệ 1">
                            @error('ten_vi_tri') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Mã vị trí -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mã vị trí * (IN HOA)</label>
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live="ma_vi_tri" 
                                       class="w-full px-3 py-2 border rounded-lg font-mono uppercase focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 {{ $isDuplicate ? 'border-red-500 bg-red-50' : 'border-gray-300' }}" 
                                       placeholder="K1"
                                       maxlength="20">
                                <!-- Loading spinner -->
                                <div wire:loading wire:target="ma_vi_tri" class="absolute right-3 top-2.5">
                                    <svg class="animate-spin h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('ma_vi_tri') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                            @if($isDuplicate)
                                <div class="mt-1.5 flex items-center gap-1.5 text-red-600 text-sm font-medium">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>{{ $duplicateMessage }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Mô tả -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Mô tả</label>
                            <input type="text" 
                                   wire:model="mo_ta" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                                   placeholder="Mô tả ngắn...">
                        </div>

                        <!-- Địa chỉ -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Địa chỉ</label>
                            <input type="text" 
                                   wire:model="dia_chi" 
                                   readonly
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600" 
                                   placeholder="Tự động từ đại lý...">
                            <p class="text-xs text-gray-500 mt-1">Địa chỉ lấy từ đại lý đã chọn</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 flex gap-3">
                        <button type="button" 
                                wire:click="showLocationModal = false" 
                                class="flex-1 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                            Hủy
                        </button>
                        <button type="submit" 
                                {{ $isDuplicate ? 'disabled' : '' }}
                                class="flex-1 px-4 py-2.5 bg-green-600 text-white font-medium rounded-lg transition {{ $isDuplicate ? 'opacity-50 cursor-not-allowed' : 'hover:bg-green-700' }}">
                            {{ $editingLocation ? 'Cập nhật' : 'Thêm mới' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
