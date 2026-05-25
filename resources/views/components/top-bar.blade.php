@php
    $topBar = trim((string) global_config('top_bar', ''));
@endphp
@if($topBar !== '')
<div class="top-bar text-white text-[12px] md:text-[13px] font-medium">
    <div class="container-x flex items-center justify-center h-9">
        <span class="inline-flex items-center gap-1.5 truncate">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m3 11 18-5v12L3 13v-2Z" />
                <path d="M11.6 16.8a3 3 0 1 1-5.8-1.6" />
            </svg>
            <span class="truncate">{!! $topBar !!}</span>
        </span>
    </div>
</div>
@endif
