<div>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $recipe ? 'Cập nhật Công thức' : 'Thêm Công thức mới' }}</h1>
        <a href="{{ route('admin.recipes.index') }}" class="text-gray-600 hover:text-gray-900">
            &larr; Quay lại
        </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã công thức</label>
                    <input type="text" wire:model="ma_cong_thuc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="VD: CT-001">
                    @error('ma_cong_thuc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tên công thức</label>
                    <input type="text" wire:model="ten_cong_thuc" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    @error('ten_cong_thuc') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sản phẩm đầu ra</label>
                    <select wire:model="san_pham_id" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Chọn sản phẩm --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->ten_san_pham }}</option>
                        @endforeach
                    </select>
                    @error('san_pham_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Số lượng/mẻ</label>
                    <div class="flex gap-2">
                        <input type="number" wire:model.live="so_luong_san_xuat" class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" min="1" placeholder="0">
                        <input type="text" wire:model="don_vi_san_xuat" class="w-24 px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500" placeholder="cái">
                    </div>
                    @error('so_luong_san_xuat') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        <option value="hoat_dong">Hoạt động</option>
                        <option value="ngung_su_dung">Ngừng sử dụng</option>
                    </select>
                </div>
            </div>

            <!-- Nguyên liệu Section -->
            <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-800">Nguyên liệu cần thiết</h3>
                    <button type="button" wire:click="addIngredient" class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                        + Thêm nguyên liệu
                    </button>
                </div>

                <!-- Header Row -->
                <div class="grid grid-cols-12 gap-2 mb-2 px-3">
                    <div class="col-span-5 text-xs font-semibold text-gray-600 uppercase">Nguyên liệu</div>
                    <div class="col-span-2 text-xs font-semib

old text-gray-600 uppercase">Số lượng</div>
                    <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase">Đơn vị</div>
                    <div class="col-span-2 text-xs font-semibold text-gray-600 uppercase">Đơn giá</div>
                    <div class="col-span-1"></div>
                </div>

                <div class="space-y-3">
                    @foreach($ingredients as $index => $ingredient)
                        <div class="grid grid-cols-12 gap-2 bg-white p-3 rounded border border-gray-200">
                            <div class="col-span-5">
                                <select wire:model.live="ingredients.{{ $index }}.nguyen_lieu_id" class="w-full px-2 py-1 text-sm border border-gray-300 rounded">
                                    <option value="">-- Chọn nguyên liệu --</option>
                                    @foreach($allIngredients as $ing)
                                        <option value="{{ $ing->id }}">{{ $ing->ten_nguyen_lieu }}</option>
                                    @endforeach
                                </select>
                                @error("ingredients.{$index}.nguyen_lieu_id") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-span-2">
                                <input type="number" wire:model.live="ingredients.{{ $index }}.so_luong" step="0.01" class="w-full px-2 py-1 text-sm border border-gray-300 rounded" placeholder="0">
                                @error("ingredients.{$index}.so_luong") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="col-span-2">
                                <input type="text" wire:model="ingredients.{{ $index }}.don_vi" readonly class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" placeholder="--">
                            </div>
                            
                            <div class="col-span-2">
                                <input type="number" wire:model="ingredients.{{ $index }}.don_gia" readonly class="w-full px-2 py-1 text-sm border border-gray-300 rounded bg-gray-100" placeholder="0">
                            </div>
                            
                            <div class="col-span-1 flex items-center justify-center">
                                <button type="button" wire:click="removeIngredient({{ $index }})" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(count($ingredients) > 0)
                    <div class="mt-4 p-3 bg-white border-l-4 border-blue-600 rounded">
                        <div class="text-sm font-semibold text-gray-800">TỔNG CHI PHÍ: <span class="text-blue-600">{{ number_format($this->totalCost, 0, ',', '.') }} đ</span></div>
                        <div class="text-xs text-gray-600 mt-1">Chi phí/{{ $don_vi_san_xuat }}: {{ number_format($this->costPerUnit, 0, ',', '.') }} đ</div>
                    </div>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.recipes.index') }}" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50">Hủy</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    {{ $recipe ? 'Cập nhật' : 'Thêm mới' }}
                </button>
            </div>
        </form>
    </div>
</div>
