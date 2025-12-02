<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $category ? 'Cập nhật Danh mục' : 'Thêm mới Danh mục' }}</h1>
        <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên danh mục</label>
                    <input type="text" wire:model="ten_danh_muc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_danh_muc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Thứ tự</label>
                    <input type="number" wire:model="thu_tu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('thu_tu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
                    <textarea wire:model="mo_ta" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                    @error('mo_ta') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $category ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
