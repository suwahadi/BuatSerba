<div>
    <h1 class="text-xl font-bold text-gray-900 mb-4">Membership Saya</h1>

    <!-- Active Membership Banner -->
    @if($activeMembership)
        <div class="bg-gradient-to-r from-green-50 to-green-100 border border-green-300 rounded-lg p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-lg font-bold text-green-900">🌟 MEMBERSHIP AKTIF</span>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mt-3">
                        <div>
                            <p class="text-xs text-green-700 font-medium">Status</p>
                            <p class="text-sm font-bold text-green-900 mt-1">Aktif</p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">Dimulai</p>
                            <p class="text-sm font-bold text-green-900 mt-1">
                                {{ $activeMembership->started_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">Berakhir</p>
                            <p class="text-sm font-bold text-green-900 mt-1">
                                {{ $activeMembership->expires_at?->format('d M Y') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700 font-medium">Sisa Waktu</p>
                            <p class="text-sm font-bold text-green-900 mt-1">
                                {{ $activeMembership->daysRemaining() ?? 0 }} hari
                            </p>
                        </div>
                        <div class="text-right md:text-left">
                            <button onclick="@this.set('selectedMembership', {{ $activeMembership->id }}); @this.set('showDetailModal', true)"
                                    class="text-green-600 hover:text-green-700 font-bold text-sm">
                                Lihat Detail →
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 sm:text-sm transition-colors" 
                       placeholder="Cari membership..." 
                       type="text"/>
            </div>
        </div>

        <!-- Status Filter -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
            <span class="text-sm font-bold text-gray-900 whitespace-nowrap">Status</span>
            <div class="flex flex-wrap gap-2 items-center flex-grow">
                <button wire:click="$set('statusFilter', 'all')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'all' ? 'bg-purple-50 border-purple-600 text-purple-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Semua
                </button>
                <button wire:click="$set('statusFilter', 'pending')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'pending' ? 'bg-yellow-50 border-yellow-600 text-yellow-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Pending
                </button>
                <button wire:click="$set('statusFilter', 'active')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'active' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Aktif
                </button>
                <button wire:click="$set('statusFilter', 'expired')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'expired' ? 'bg-red-50 border-red-600 text-red-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Kedaluwarsa
                </button>
            </div>
            <button wire:click="resetFilters"
                    class="text-sm font-bold text-purple-600 hover:text-purple-700 transition-colors whitespace-nowrap">
                Reset
            </button>
        </div>
    </div>

    <!-- Memberships List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        @if($memberships->count() > 0)
            <div class="space-y-4 mb-6">
                @foreach($memberships as $membership)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <!-- Left Side: Info -->
                            <div class="flex-grow">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusBadgeColor($membership->status) }}">
                                        {{ $this->getStatusLabel($membership->status) }}
                                    </span>
                                    <span class="font-medium text-gray-900">
                                        Rp {{ number_format($membership->price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm mt-3">
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Dibuat</p>
                                        <p class="text-gray-900 font-medium">{{ $membership->created_at->format('d M Y') }}</p>
                                    </div>
                                    @if($membership->started_at)
                                        <div>
                                            <p class="text-xs text-gray-500 font-medium">Dimulai</p>
                                            <p class="text-gray-900 font-medium">{{ $membership->started_at->format('d M Y') }}</p>
                                        </div>
                                    @endif
                                    @if($membership->expires_at)
                                        <div>
                                            <p class="text-xs text-gray-500 font-medium">Berakhir</p>
                                            <p class="text-gray-900 font-medium">{{ $membership->expires_at->format('d M Y') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right Side: Actions -->
                            <div class="flex gap-2 justify-end">
                                <button wire:click="showDetail({{ $membership->id }})"
                                        class="px-4 py-2 bg-purple-50 hover:bg-purple-100 text-purple-600 font-bold text-sm rounded-lg transition-colors">
                                    Detail
                                </button>
                                @if(in_array($membership->status, ['pending', 'expired', 'cancelled']))
                                    <button wire:click="deleteMembership({{ $membership->id }})"
                                            onclick="confirm('Yakin ingin menghapus membership ini?') || event.stopImmediatePropagation()"
                                            class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 font-bold text-sm rounded-lg transition-colors">
                                        Hapus
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="border-t border-gray-200 pt-6">
                {{ $memberships->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center text-center py-16">
                <div class="mb-4 text-gray-300">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">
                    Belum Ada Membership
                </h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto text-xs leading-relaxed">
                    Belum ada riwayat pembelian premium membership.
                </p>
                <a href="{{ route('premium.purchase') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
                    Beli Premium Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedMembership)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto p-6">
                <div class="flex justify-between items-center mb-6 border-b border-gray-200 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Detail Membership</h2>
                    <button wire:click="closeDetail"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Status Section -->
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-2">Status</p>
                        <div class="inline-block">
                            <span class="px-4 py-2 text-sm font-bold rounded-full {{ $this->getStatusBadgeColor($selectedMembership->status) }}">
                                {{ $this->getStatusLabel($selectedMembership->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- User Info Section -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-2">Nama</p>
                            <p class="text-gray-900 font-medium">{{ $selectedMembership->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-2">Email</p>
                            <p class="text-gray-900 font-medium">{{ $selectedMembership->user->email }}</p>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase mb-2">Harga</p>
                        <p class="text-2xl font-bold text-purple-600">Rp {{ number_format($selectedMembership->price, 0, ',', '.') }}</p>
                    </div>

                    <!-- Dates Section -->
                    <div class="grid grid-cols-3 gap-4 p-4 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-xs text-gray-500 font-bold uppercase mb-2">Dibuat</p>
                            <p class="text-gray-900 font-medium">{{ $selectedMembership->created_at->format('d M Y H:i') }}</p>
                        </div>
                        @if($selectedMembership->started_at)
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase mb-2">Dimulai</p>
                                <p class="text-gray-900 font-medium">{{ $selectedMembership->started_at->format('d M Y') }}</p>
                            </div>
                        @endif
                        @if($selectedMembership->expires_at)
                            <div>
                                <p class="text-xs text-gray-500 font-bold uppercase mb-2">Berakhir</p>
                                <p class="text-gray-900 font-medium">{{ $selectedMembership->expires_at->format('d M Y') }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Payment Proof Section -->
                    @if($selectedMembership->payment_proof_path)
                        <div class="border-t border-gray-200 pt-6">
                            <p class="text-xs text-gray-500 font-bold uppercase mb-3">Bukti Transfer</p>
                            <a href="{{ Storage::disk('public')->url($selectedMembership->payment_proof_path) }}"
                               target="_blank"
                               class="inline-block px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 font-bold text-sm rounded-lg transition-colors">
                                📎 Lihat Bukti Transfer
                            </a>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6">
                    <button wire:click="closeDetail"
                            class="w-full bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-2.5 rounded-lg transition-colors">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
