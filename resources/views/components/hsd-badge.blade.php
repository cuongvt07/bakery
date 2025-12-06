@props(['detail', 'size' => 'sm'])

@php
    if (!$detail || !$detail->han_su_dung) {
        return;
    }
    
    $daysLeft = $detail->daysUntilExpiry();
    $isExpired = $detail->isExpired();
    $isNearExpiry = $detail->isNearExpiry();
    
    // Size variants
    $sizeClasses = [
        'xs' => 'px-1 py-0.5 text-[9px]',
        'sm' => 'px-1.5 py-0.5 text-[10px]',
        'md' => 'px-2 py-1 text-xs',
        'lg' => 'px-2.5 py-1 text-sm',
    ];
    
    $textSize = $sizeClasses[$size] ?? $sizeClasses['sm'];
@endphp

@if($isExpired)
    <span class="{{ $textSize }} bg-red-600 text-white rounded font-bold inline-flex items-center gap-0.5" title="Đã hết hạn sử dụng">
        <span>❌</span>
        <span>HẾT HẠN</span>
    </span>
@elseif($isNearExpiry)
    <span class="{{ $textSize }} bg-orange-500 text-white rounded font-bold inline-flex items-center gap-0.5" title="Sắp hết hạn (còn {{ $daysLeft }} ngày)">
        <span>⚠️</span>
        <span>{{ $daysLeft }}d</span>
    </span>
@else
    <span class="{{ $textSize }} text-green-600 inline-flex items-center gap-0.5" title="Còn {{ $daysLeft }} ngày">
        <span>✓</span>
        <span>{{ $daysLeft }}d</span>
    </span>
@endif
