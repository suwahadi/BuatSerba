@props(['branches', 'activeBranch', 'lowStockAlert'])

<div class="mb-6">
    <div class="flex justify-center">
        <div class="w-full max-w-4xl px-3 py-2 bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm">
            <nav aria-label="Branches" class="flex items-center justify-center gap-4 text-base">
                @foreach($branches as $branch)
                    @php
                        $isActive = isset($activeBranch) && (string) $activeBranch->id === (string) $branch->id;
                        $activeStyle = 'background:#FDE047;border:2px solid #F59E0B;color:#111;padding:0.5rem 1rem;box-shadow:0 1px 0 rgba(0,0,0,0.03);border-radius:6px;';
                        $inactiveStyle = 'background:transparent;border:1px solid rgba(0,0,0,0.06);color:#374151;padding:0.5rem 1rem;box-shadow:0 1px 0 rgba(0,0,0,0.03);border-radius:6px;';
                    @endphp

                        <button
                            wire:click="switchBranch({{ $branch->id }})"
                            type="button"
                            aria-pressed="{{ $isActive ? 'true' : 'false' }}"
                            class="px-4 py-2 rounded-md focus:outline-none transition-colors duration-150"
                            style="{{ $isActive ? $activeStyle : $inactiveStyle }}"
                        >
                            {{ $branch->name }}
                        </button>

                @endforeach
            </nav>
        </div>
    </div>
</div>
