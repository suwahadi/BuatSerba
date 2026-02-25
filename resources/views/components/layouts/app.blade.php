<x-layouts.app.sidebar>
    <div class="flex flex-col min-h-screen">
        <flux:main class="flex-grow">
            {{ $slot }}
        </flux:main>
        
        <div class="mt-auto">
            <x-footer />
        </div>
    </div>
</x-layouts.app.sidebar>
