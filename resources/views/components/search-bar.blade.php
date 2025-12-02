@props(['placeholder' => 'Tìm kiếm...'])

<div class="relative">
    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
    </div>
    
    <input 
        type="text" 
        wire:model.live.debounce.300ms="search"
        class="block w-full pl-10 pr-10 py-2.5 text-sm text-gray-900 border border-gray-300 rounded-lg bg-white focus:ring-blue-500 focus:border-blue-500"
        placeholder="{{ $placeholder }}"
    >
    
    @if($search ?? false)
        <button 
            wire:click="$set('search', '')"
            type="button"
            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    @endif
    
    <div wire:loading wire:target="search" class="absolute inset-y-0 right-0 flex items-center pr-3">
        <svg class="w-5 h-5 text-blue-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>
</div>
