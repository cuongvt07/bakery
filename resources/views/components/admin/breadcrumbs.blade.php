<nav class="flex items-center space-x-2 text-sm text-gray-600 mb-4">
    <a href="{{ route('admin.dashboard') }}" class="hover:text-indigo-600 transition-colors">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
        </svg>
    </a>
    
    @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
        @foreach($breadcrumbs as $index => $breadcrumb)
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            
            @if($loop->last)
                <span class="font-medium text-gray-900">{{ $breadcrumb['label'] }}</span>
            @else
                <a href="{{ $breadcrumb['url'] ?? '#' }}" class="hover:text-indigo-600 transition-colors">
                    {{ $breadcrumb['label'] }}
                </a>
            @endif
        @endforeach
    @endif
</nav>
