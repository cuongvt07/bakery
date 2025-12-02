<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $supplier ? 'Cập nhật Nhà cung cấp' : 'Thêm mới Nhà cung cấp' }}</h1>
        <a href="{{ route('admin.suppliers.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã nhà cung cấp</label>
                    <input type="text" wire:model="ma_ncc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: NCC001">
                    @error('ma_ncc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên nhà cung cấp</label>
                    <input type="text" wire:model="ten_ncc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_ncc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số điện thoại</label>
                    <input type="text" wire:model="so_dien_thoai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('so_dien_thoai') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" wire:model="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('email') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Địa chỉ</label>
                    <input type="text" wire:model="dia_chi" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('dia_chi') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
                    <textarea wire:model="ghi_chu" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" rows="3"></textarea>
                    @error('ghi_chu') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $supplier ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
