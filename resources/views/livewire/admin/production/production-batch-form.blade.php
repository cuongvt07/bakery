<div>
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-semibold text-gray-800">
            {{ $batch ? 'Sửa Mẻ sản xuất' : 'Tạo Mẻ mới' }}
        </h2>
    </div>

    <!-- Show form or QC based on state -->
    @if($batch && $batch->trang_thai == 'qc')
        <!-- QC Form -->
        <form wire:submit="confirmQC" class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">QC Mẻ: {{ $batch->ma_me }}</h3>
            
            <div class="space-y-4">
                @foreach($batch->details as $detail)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-gray-700">{{ $detail->product->ten_san_pham }}</h4>
                            <span class="text-sm text-gray-500">Công thức: {{ $detail->recipe->ten_cong_thuc }}</span>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Dự kiến:</span>
                                <div class="font-semibold text-blue-600">{{ $detail->so_luong_du_kien }} {{ $detail->recipe->don_vi_san_xuat }}</div>
                            </div>
                            <div>
                                <label class="text-gray-600 block mb-1">Số lượng lỗi:</label>
                                <input type="number" wire:model="qcData.{{ $detail->id }}" min="0" max="{{ $detail->so_luong_du_kien }}" 
                                       class="w-full px-3 py-1 border border-gray-300 rounded">
                            </div>
                            <div>
                                <span class="text-gray-600">Thực tế OK:</span>
                                <div class="font-semibold text-green-600">
                                    {{ $detail->so_luong_du_kien - ($qcData[$detail->id] ?? 0) }} {{ $detail->recipe->don_vi_san_xuat }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- QC Images & Notes -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload ảnh lỗi</label>
                <input type="file" wire:model="qcImages" multiple accept="image/*" class="block w-full text-sm">
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Ghi chú QC</label>
                <textarea wire:model="ghi_chu_qc" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
            </div>

            @if (session('message'))
                <div class="mt-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded">{{ session('message') }}</div>
            @endif

            <div class="mt-6 flex justify-end gap-3">
                <a href="{{ route('admin.production-batches.index') }}" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50">Hủy</a>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700">
                    ✓ Xác nhận QC hoàn thành
                </button>
            </div>
        </form>
    @else
        <!-- Create/Edit Form -->
        <form wire:submit="save" class="bg-white rounded-lg shadow-sm p-6">
            <!-- Batch Info -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Mã mẻ *</label>
                    <input type="text" wire:model="ma_me" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ngày SX / Buổi *</label>
                    <div class="flex gap-2">
                        <input type="date" wire:model.live="ngay_san_xuat" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <select wire:model.live="buoi" class="w-28 px-2 py-2 border border-gray-300 rounded-lg">
                            <option value="sang">Sáng</option>
                            <option value="trua">Trưa</option>
                            <option value="chieu">Chiều</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hạn sử dụng (Bảo quản)</label>
                    <input type="date" wire:model="han_su_dung" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
                    <select wire:model="trang_thai" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="ke_hoach">Kế hoạch</option>
                        <option value="dang_san_xuat">Đang sản xuất</option>
                        <option value="qc">QC</option>
                        <option value="hoan_thanh">Hoàn thành</option>
                        <option value="huy">Hủy</option>
                    </select>
                </div>
            </div>

            <!-- Products & Ingredients Section - 2 Column Layout -->
            <div class="mb-6">
                <h3 class="font-semibold text-gray-800 mb-3">Thông tin sản xuất</h3>
                
                <div class="grid grid-cols-10 gap-6">
                    <!-- Left: Products List (60% - 6 cols) -->
                    <div class="col-span-6 border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-medium text-gray-700">Danh sách sản phẩm</h4>
                            <button type="button" wire:click="addProduct" class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
                                + Thêm
                            </button>
                        </div>

                        <div class="space-y-3 max-h-96 overflow-y-auto">
                            @foreach($products as $index => $product)
                                <div class="bg-white border border-gray-200 rounded p-3 relative">
                                    @if(count($products) > 1)
                                        <button type="button" wire:click="removeProduct({{ $index }})" class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    <div class="grid grid-cols-10 gap-2">
                                        <div class="col-span-4">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Công thức *</label>
                                            <select wire:model.live="products.{{ $index }}.cong_thuc_id" class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                                                <option value="">-- Chọn công thức --</option>
                                                @foreach($recipes as $recipe)
                                                    <option value="{{ $recipe->id }}">{{ $recipe->ten_cong_thuc }}</option>
                                                @endforeach
                                            </select>
                                            @error("products.{$index}.cong_thuc_id") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-span-4">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Sản phẩm *</label>
                                            <select wire:model.live="products.{{ $index }}.san_pham_id" class="w-full px-2 py-2 text-sm border border-gray-300 rounded">
                                                <option value="">-- Chọn sản phẩm --</option>
                                                @foreach($allProducts as $p)
                                                    <option value="{{ $p->id }}">{{ $p->ten_san_pham }}</option>
                                                @endforeach
                                            </select>
                                            @error("products.{$index}.san_pham_id") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">SL dự kiến</label>
                                            <input type="number" wire:model.live="products.{{ $index }}.so_luong_du_kien" min="1" class="w-full px-2 py-2 text-sm border border-gray-300 rounded" placeholder="100">
                                            @error("products.{$index}.so_luong_du_kien") <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                    
                                    @if($trang_thai === 'hoan_thanh' && $batch)
                                        <div class="grid grid-cols-2 gap-3 mt-3 pt-3 border-t border-gray-200">
                                            <div>
                                                <label class="block text-xs font-medium text-red-600 mb-1">SL lỗi</label>
                                                <input type="number" wire:model="products.{{ $index }}.so_luong_that_bai" min="0" max="{{ $product['so_luong_du_kien'] ?? 0 }}" 
                                                       class="w-full px-3 py-2 text-sm border border-red-300 rounded bg-red-50" placeholder="0">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-green-600 mb-1">SL thực tế OK</label>
                                                <input type="number" readonly 
                                                       value="{{ ($product['so_luong_du_kien'] ?? 0) - ($product['so_luong_that_bai'] ?? 0) }}"
                                                       class="w-full px-3 py-2 text-sm border border-green-300 rounded bg-green-50 font-semibold">
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right: Ingredients Summary (40% - 4 cols) -->
                    <div class="col-span-4 border border-amber-200 rounded-lg p-4 bg-amber-50">
                        <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Định lượng nguyên liệu
                        </h4>
                        
                        @php
                            $allIngredients = [];
                            foreach($products as $prod) {
                                if(!empty($prod['cong_thuc_id']) && !empty($prod['so_luong_du_kien'])) {
                                    $recipe = $recipes->find($prod['cong_thuc_id']);
                                    if($recipe && $recipe->details) {
                                        // Calculate ratio: (expected qty / recipe base qty)
                                        $ratio = $prod['so_luong_du_kien'] / $recipe->so_luong_san_xuat;
                                        
                                        foreach($recipe->details as $detail) {
                                            $key = $detail->nguyen_lieu_id;
                                            if(!isset($allIngredients[$key])) {
                                                $allIngredients[$key] = [
                                                    'ten' => $detail->ingredient->ten_nguyen_lieu,
                                                    'so_luong' => 0,
                                                    'don_vi' => $detail->don_vi,
                                                    'ton_kho' => $detail->ingredient->ton_kho_hien_tai,
                                                ];
                                            }
                                            // Accumulate quantity with ratio
                                            $allIngredients[$key]['so_luong'] += $detail->so_luong * $ratio;
                                        }
                                    }
                                }
                            }
                        @endphp
                        
                        @if(empty($allIngredients))
                            <div class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-sm">Chọn công thức để xem định lượng</p>
                            </div>
                        @else
                            <div class="space-y-2 max-h-96 overflow-y-auto">
                                @foreach($allIngredients as $ing)
                                    @php 
                                        $sufficient = $ing['ton_kho'] >= $ing['so_luong'];
                                        $usageRatio = $ing['ton_kho'] > 0 ? ($ing['so_luong'] / $ing['ton_kho']) * 100 : 0;
                                        $isHighUsage = $usageRatio >= 70; // Cảnh báo khi dùng >= 70% tồn kho
                                        
                                        // Determine colors based on conditions
                                        if (!$sufficient) {
                                            $borderColor = 'border-red-300 bg-red-50';
                                            $iconColor = 'text-red-600';
                                            $textColor = 'text-red-700';
                                        } elseif ($isHighUsage) {
                                            $borderColor = 'border-orange-300 bg-orange-50';
                                            $iconColor = 'text-orange-600';
                                            $textColor = 'text-orange-700';
                                        } else {
                                            $borderColor = 'border-green-200';
                                            $iconColor = 'text-green-600';
                                            $textColor = 'text-green-700';
                                        }
                                    @endphp
                                    <div class="bg-white p-2 rounded border {{ $borderColor }}">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if(!$sufficient)
                                                <svg class="w-4 h-4 {{ $iconColor }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @elseif($isHighUsage)
                                                <svg class="w-4 h-4 {{ $iconColor }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 {{ $iconColor }} flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @endif
                                            <span class="text-sm font-medium text-gray-800">{{ $ing['ten'] }}</span>
                                        </div>
                                        <div class="text-xs pl-6">
                                            <div class="flex justify-between">
                                                <span class="{{ $textColor }} font-semibold">
                                                    Cần: {{ number_format($ing['so_luong'], 2) }}
                                                </span>
                                                <span class="text-gray-600">
                                                    Tồn: {{ number_format($ing['ton_kho'], 2) }}
                                                </span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500">{{ $ing['don_vi'] }}</span>
                                                <span class="text-xs {{ $textColor }} font-medium">
                                                    {{ number_format($usageRatio, 0) }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            @php
                                $hasInsufficientIngredients = collect($allIngredients)->contains(fn($ing) => $ing['ton_kho'] < $ing['so_luong']);
                                $hasHighUsageIngredients = collect($allIngredients)->contains(function($ing) {
                                    $ratio = $ing['ton_kho'] > 0 ? ($ing['so_luong'] / $ing['ton_kho']) * 100 : 0;
                                    return $ratio >= 70 && $ing['ton_kho'] >= $ing['so_luong'];
                                });
                            @endphp
                            
                            @if($hasInsufficientIngredients)
                                <div class="mt-3 p-2 bg-red-100 border-l-4 border-red-600 rounded">
                                    <p class="text-xs font-semibold text-red-800">
                                        ⚠️ Thiếu nguyên liệu!
                                    </p>
                                </div>
                            @elseif($hasHighUsageIngredients)
                                <div class="mt-3 p-2 bg-orange-100 border-l-4 border-orange-600 rounded">
                                    <p class="text-xs font-semibold text-orange-800">
                                        ⚠️ Dùng nhiều (≥70% tồn kho)
                                    </p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            @if (session('message'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-3 rounded">{{ session('message') }}</div>
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.production-batches.index') }}" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50">Hủy</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                    Lưu mẻ
                </button>
            </div>
        </form>
    @endif
</div>
