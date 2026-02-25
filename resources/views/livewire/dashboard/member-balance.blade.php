<div>
    <h1 class="text-xl font-bold text-gray-900 mb-4">Saldo Saya</h1>

    <!-- Balance Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Available Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Saldo Tersedia</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($availableBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-green-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Locked Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Saldo Terkunci</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($lockedBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-yellow-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Balance -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Saldo Awal</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">
                        Rp {{ number_format($initialBalance, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-blue-100 p-2.5 rounded-full">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 min-h-[600px]">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <div class="relative flex-grow">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 sm:text-sm transition-colors" 
                       placeholder="Cari transaksi..." 
                       type="text"/>
            </div>
            
            <div class="relative min-w-[200px]">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <input wire:model.live="dateFilter"
                       class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-green-600 focus:border-green-600 sm:text-sm transition-colors cursor-pointer" 
                       type="date"/>
            </div>
        </div>

        <!-- Type Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-8">
            <span class="text-sm font-bold text-gray-900 whitespace-nowrap">Tipe</span>
            <div class="flex flex-wrap gap-2 items-center flex-grow">
                <button wire:click="$set('typeFilter', 'all')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $typeFilter === 'all' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Semua
                </button>
                <button wire:click="$set('typeFilter', 'credit')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $typeFilter === 'credit' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Masuk
                </button>
                <button wire:click="$set('typeFilter', 'debit')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $typeFilter === 'debit' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Keluar
                </button>
            </div>
            <button wire:click="resetFilters"
                    class="text-sm font-bold text-green-600 hover:text-green-700 transition-colors whitespace-nowrap">
                Reset Filter
            </button>
        </div>

        <!-- Transactions List or Empty State -->
        @if($this->transactions->count() > 0)
            <div class="space-y-4">
                @foreach($this->transactions as $transaction)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="{{ $this->getTypeColor($transaction->type) }} font-medium">
                                        {{ $this->getTypeLabel($transaction->type) }}
                                    </span>
                                    <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                        {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500">Saldo Setelah</p>
                            </div>
                        </div>
                        <div class="text-sm text-gray-700">
                            <p class="mb-1">{{ $transaction->description }}</p>
                            <p class="text-xs text-gray-500">
                                Referensi: {{ $this->getReferenceLabel($transaction) }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $this->transactions->links() }}
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
                    Belum Ada Transaksi
                </h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto text-xs leading-relaxed">
                    Mulai berbelanja untuk melihat riwayat transaksi saldo Anda.
                </p>
                <a href="{{ route('catalog') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>
