<!-- Tạo component nhỏ gọn cho nút Reset -->
<button {{ $attributes->merge(['class' => 'px-3 py-2 text-xs font-medium text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 hover:text-gray-800 transition-colors flex items-center gap-1.5']) }}
        title="Xóa bộ lọc">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
    </svg>
    <span>Reset</span>
</button>
