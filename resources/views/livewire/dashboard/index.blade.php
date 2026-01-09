<div>
    <h1 class="text-xl font-bold text-gray-900 mb-4">Daftar Transaksi</h1>

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
                       placeholder="Cari transaksimu di sini" 
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

        <!-- Status Filters -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-8">
            <span class="text-sm font-bold text-gray-900 whitespace-nowrap">Status</span>
            <div class="flex flex-wrap gap-2 items-center flex-grow">
                <button wire:click="$set('statusFilter', 'all')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'all' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Semua
                </button>
                <button wire:click="$set('statusFilter', 'pending')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'pending' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Menunggu
                </button>
                <button wire:click="$set('statusFilter', 'completed')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'completed' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Berhasil
                </button>
                <button wire:click="$set('statusFilter', 'failed')"
                        class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors {{ $statusFilter === 'failed' ? 'bg-green-50 border-green-600 text-green-600' : 'border-gray-300 text-gray-600 bg-white hover:bg-gray-50' }}">
                    Tidak Berhasil
                </button>
            </div>
            <button wire:click="resetFilters"
                    class="text-sm font-bold text-green-600 hover:text-green-700 transition-colors whitespace-nowrap">
                Reset Filter
            </button>
        </div>

        <!-- Orders List or Empty State -->
        @if($this->orders->count() > 0)
            <div class="space-y-4">
                @foreach($this->orders as $order)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <a href="{{ route('order.detail', $order->order_number) }}" 
                                   class="font-bold text-green-600 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                <p class="text-xs text-gray-500">
                                    {{ $order->created_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                            <span class="px-3 py-1 text-xs font-medium rounded-full
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                                   ($order->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ $order->payment_status === 'paid' ? 'Lunas' : 
                                   ($order->payment_status === 'pending' ? 'Menunggu' : 'Dibatalkan') }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-700">
                            <p>{{ $order->items->count() }} produk</p>
                            <p class="font-bold mt-1 text-gray-900">{{ format_rupiah($order->total) }}</p>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <a href="{{ route('order.detail', $order->order_number) }}" 
                               class="text-sm text-green-600 hover:text-green-700 font-medium">
                                Lihat Detail
                            </a>
                            @if($order->payment_status === 'pending')
                                <span class="text-gray-300">|</span>
                                <a href="{{ route('payment', $order->order_number) }}" 
                                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                                    Bayar Sekarang
                                </a>
                            @endif
                            @if($order->status === 'completed')
                                <span class="text-gray-300">|</span>
                                @if($order->reviews->isNotEmpty())
                                    <span class="text-sm text-gray-500 font-medium">
                                        Sudah Dinilai
                                    </span>
                                @else
                                    <a href="{{ route('order.rating', $order->order_number) }}" 
                                       class="text-sm text-green-600 hover:text-green-700 font-medium">
                                        Beri Penilaian
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $this->orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="flex flex-col items-center justify-center text-center py-16">
                <div class="mb-4 text-gray-300">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">
                    Data Tidak Ditemukan
                </h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto text-xs leading-relaxed">
                    Yuk, mulai belanja di {{ config('app.name') }}!
                </p>
                <a href="{{ route('catalog') }}" 
                   class="bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 px-10 rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-600">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
    @if(session()->has('success'))
        <div x-init="setTimeout(() => $dispatch('notify', { message: '{{ session('success') }}', type: 'success' }), 500)"></div>
    @endif
    @if(session()->has('error'))
        <div x-init="setTimeout(() => $dispatch('notify', { message: '{{ session('error') }}', type: 'error' }), 500)"></div>
    @endif
</div>
