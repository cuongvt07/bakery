<div class="space-y-3">
    <!-- Preset Buttons -->
    <div class="flex flex-wrap gap-2">
        <button 
            wire:click="setDateRange('today')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            Hôm nay
        </button>
        <button 
            wire:click="setDateRange('yesterday')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            Hôm qua
        </button>
        <button 
            wire:click="setDateRange('7days')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            7 ngày
        </button>
        <button 
            wire:click="setDateRange('30days')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            30 ngày
        </button>
        <button 
            wire:click="setDateRange('this_month')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            Tháng này
        </button>
        <button 
            wire:click="setDateRange('last_month')"
            type="button"
            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            Tháng trước
        </button>
    </div>

    <!-- Custom Date Range -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Từ ngày</label>
            <input 
                type="date" 
                wire:model.live="dateFrom"
                class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Đến ngày</label>
            <input 
                type="date" 
                wire:model.live="dateTo"
                class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
            >
        </div>
    </div>

    <!-- Reset Button -->
    @if($dateFrom || $dateTo)
        <div class="flex justify-end">
            <button 
                wire:click="$set('dateFrom', ''); $set('dateTo', '')"
                type="button"
                class="text-xs text-blue-600 hover:text-blue-800 font-medium"
            >
                Xóa lọc ngày
            </button>
        </div>
    @endif
</div>
