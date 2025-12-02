<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-800">Gán nhân viên</h2>
            <p class="text-sm text-gray-500 mt-1">Chọn nhân viên làm việc tại: {{ $agency->ten_diem_ban }}</p>
        </div>
        
        <form wire:submit="submit" class="p-6">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Ngày bắt đầu hiệu lực</label>
                <input type="date" wire:model="ngay_bat_dau" 
                       class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('ngay_bat_dau') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Danh sách nhân viên</label>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($users as $user)
                        <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:bg-gray-50 transition-colors {{ in_array($user->id, $selectedUsers) ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}">
                            <div class="min-w-0 flex-1 text-sm">
                                <div class="font-medium text-gray-700 select-none">{{ $user->ho_ten }}</div>
                                <p class="text-gray-500 select-none">{{ $user->email }}</p>
                            </div>
                            <div class="ml-3 flex items-center h-5">
                                <input type="checkbox" value="{{ $user->id }}" wire:model="selectedUsers"
                                       class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('selectedUsers') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>
            
            <div class="mt-8 flex justify-end space-x-3">
                <a href="{{ route('admin.diemban.show', $agency->id) }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
                        wire:loading.attr="disabled">
                    <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
