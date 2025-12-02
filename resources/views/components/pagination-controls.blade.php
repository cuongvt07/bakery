@props(['paginator'])

<div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-3">
    <!-- Per Page Selection -->
    <div class="flex items-center gap-2">
        <label class="text-sm text-gray-700">Hiển thị</label>
        <select 
            wire:model.live="perPage"
            class="px-3 py-1.5 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
        >
            <option value="15">15</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
        </select>
        <span class="text-sm text-gray-700">mục/trang</span>
    </div>

    <!-- Pagination Info -->
    <div class="text-sm text-gray-700">
        Hiển thị 
        <span class="font-medium">{{ $paginator->firstItem() ?? 0 }}</span>
        -
        <span class="font-medium">{{ $paginator->lastItem() ?? 0 }}</span>
        trên tổng
        <span class="font-medium">{{ $paginator->total() }}</span>
        kết quả
    </div>

    <!-- Pagination Links -->
    <div>
        {{ $paginator->links() }}
    </div>
</div>

<!-- Loading Overlay -->
<div wire:loading class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-lg">
    <div class="flex items-center gap-2">
        <svg class="w-5 h-5 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="text-sm text-gray-600">Đang tải...</span>
    </div>
</div>
