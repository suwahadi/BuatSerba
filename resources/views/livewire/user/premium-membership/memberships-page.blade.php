<div>
    <h1 class="text-2xl font-bold text-gray-900 mb-2">📋 Membership Saya</h1>
    <p class="text-gray-600 mb-8">Lihat riwayat dan status semua membership premium Anda.</p>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Membership Aktif</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $activeMembershipCount }}</p>
                </div>
                <div class="bg-green-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Menunggu Verifikasi</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $pendingMembershipCount }}</p>
                </div>
                <div class="bg-yellow-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Kedaluwarsa</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ $expiredMembershipCount }}</p>
                </div>
                <div class="bg-red-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13.477 14.89A6 6 0 015.11 2.626a6 6 0 018.367 8.264A6 6 0 0113.477 14.89z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

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
            
            <div class="relative min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input wire:model.live="dateFilter"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-purple-600 focus:border-purple-600 sm:text-sm transition-colors cursor-pointer" 
                       type="date"/>
            </div>
        </div>

        <!-- Status Filter -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <span class="text-sm font-bold text-gray-900 whitespace-nowrap">Status</span>
            <div class="flex flex-wrap gap-2 items-center flex-grow">
                <button wire:click="$set('statusFilter', 'all')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'all' ? 'bg-purple-50 border-purple-600 text-purple-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Semua
                </button>
                <button wire:click="$set('statusFilter', 'active')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'active' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Aktif
                </button>
                <button wire:click="$set('statusFilter', 'pending')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'pending' ? 'bg-yellow-50 border-yellow-600 text-yellow-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Menunggu
                </button>
                <button wire:click="$set('statusFilter', 'expired')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'expired' ? 'bg-red-50 border-red-600 text-red-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Kedaluwarsa
                </button>
            </div>
            <button wire:click="resetFilters"
                    class="text-sm font-bold text-purple-600 hover:text-purple-700 transition-colors whitespace-nowrap">
                Reset Filter
            </button>
        </div>
    </div>

    <!-- Memberships List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 min-h-[400px]">
        @if($memberships->count() > 0)
            <div class="space-y-4">
                @foreach($memberships as $membership)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusBadgeColorClass($membership->status) }}">
                                        {{ $this->getStatusLabelProperty()[$membership->status] ?? 'Unknown' }}
                                    </span>
                                    <span class="text-sm font-bold text-gray-900">
                                        Rp {{ number_format($membership->price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    Dibuat: {{ $membership->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="text-right flex items-center gap-2">
                                @if($membership->status === 'pending')
                                    <button wire:click="$set('showUploadModal', true); $set('membershipId', {{ $membership->id }})"
                                            class="px-3 py-1.5 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-bold rounded transition-colors">
                                        📤 Upload Bukti
                                    </button>
                                @endif

                                <button wire:click="viewDetail({{ $membership->id }})"
                                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded transition-colors">
                                    👁️ Detail
                                </button>

                                @if(in_array($membership->status, ['pending', 'expired']))
                                    <button wire:click="openDeleteModal({{ $membership->id }})"
                                            class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded transition-colors">
                                        🗑️ Hapus
                                    </button>
                                @endif
                            </div>
                        </div>

                        <div class="text-sm text-gray-700 grid grid-cols-2 md:grid-cols-4 gap-3 mt-3 pt-3 border-t border-gray-200">
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Dimulai</p>
                                <p class="font-bold text-gray-900">{{ $membership->started_at ? $membership->started_at->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Berakhir</p>
                                <p class="font-bold text-gray-900">{{ $membership->expires_at ? $membership->expires_at->format('d M Y') : '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Sisa Hari</p>
                                <p class="font-bold text-gray-900">
                                    @if($this->getDaysRemaining($membership))
                                        {{ $this->getDaysRemaining($membership) }} hari
                                    @else
                                        -
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 font-medium">Metode Bayar</p>
                                <p class="font-bold text-gray-900">{{ $membership->payment_method === 'bank_transfer' ? '🏦 Transfer Bank' : $membership->payment_method }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
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
                    Mulai dengan membeli premium membership untuk menikmati keuntungan eksklusif kami.
                </p>
                <a href="{{ route('premium.purchase') }}" 
                   class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-600">
                    💎 Beli Premium Sekarang
                </a>
            </div>
        @endif
    </div>

    <!-- Detail Modal -->
    @if($showDetailModal && $selectedMembership)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full max-h-[90vh] overflow-y-auto p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-900">📋 Detail Membership</h3>
                    <button wire:click="closeDetailModal" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-600 font-medium">Status</p>
                        <span class="inline-block mt-1 px-3 py-1 text-xs font-bold rounded-full {{ $this->getStatusBadgeColorClass($selectedMembership->status) }}">
                            {{ $this->getStatusLabelProperty()[$selectedMembership->status] ?? 'Unknown' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-medium">Harga</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($selectedMembership->price, 0, ',', '.') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 font-medium">Metode Pembayaran</p>
                        <p class="text-sm font-bold text-gray-900">{{ $selectedMembership->payment_method === 'bank_transfer' ? '🏦 Transfer Bank' : $selectedMembership->payment_method }}</p>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <p class="text-sm text-gray-600 font-medium mb-3">Timeline</p>
                        <div class="space-y-2">
                            <div>
                                <p class="text-xs text-gray-500">Dibuat</p>
                                <p class="text-sm font-bold text-gray-900">{{ $selectedMembership->created_at?->format('d M Y H:i') ?? '-' }}</p>
                            </div>
                            @if($selectedMembership->started_at)
                                <div>
                                    <p class="text-xs text-gray-500">Dimulai</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $selectedMembership->started_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                            @if($selectedMembership->expires_at)
                                <div>
                                    <p class="text-xs text-gray-500">Berakhir</p>
                                    <p class="text-sm font-bold text-gray-900">{{ $selectedMembership->expires_at->format('d M Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($selectedMembership->payment_proof_path)
                        <div class="border-t border-gray-200 pt-4">
                            <p class="text-sm text-gray-600 font-medium mb-2">Bukti Transfer</p>
                            <a href="{{ Storage::url($selectedMembership->payment_proof_path) }}" target="_blank" 
                               class="text-blue-600 hover:text-blue-700 font-medium text-sm">
                                📄 Lihat Bukti
                            </a>
                        </div>
                    @endif
                </div>

                <button wire:click="closeDetailModal"
                        class="w-full mt-6 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2.5 px-4 rounded-lg transition-all duration-200">
                    Tutup
                </button>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedMembership)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-lg max-w-sm w-full p-6">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900">⚠️ Hapus Membership?</h3>
                </div>

                <p class="text-gray-600 mb-6">
                    Apakah Anda yakin ingin menghapus membership ini? 
                    <br><strong>Aksi ini tidak bisa dibatalkan.</strong>
                </p>

                <div class="bg-gray-50 p-3 rounded-lg mb-6">
                    <p class="text-sm text-gray-600">Membership:</p>
                    <p class="font-bold text-gray-900">Rp {{ number_format($selectedMembership->price, 0, ',', '.') }} - {{ $this->getStatusLabelProperty()[$selectedMembership->status] ?? 'Unknown' }}</p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="closeDeleteModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-900 font-bold py-2.5 px-4 rounded-lg transition-all duration-200">
                        Batal
                    </button>
                    <button wire:click="deleteMembership"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 px-4 rounded-lg shadow-sm transition-all duration-200">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
