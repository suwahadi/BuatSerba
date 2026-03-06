<div class="flex flex-col gap-0.5">
    <span>{{ $userName }}<br></span>
    @if ($roleName !== '-')
        <x-filament::badge color="gray" size="xs">
            {{ ucfirst($roleName) }}
        </x-filament::badge>
    @else
        <span class="text-gray-400">-</span>
    @endif
</div>
