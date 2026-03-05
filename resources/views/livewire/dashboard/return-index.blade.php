<div>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">Permohonan Retur Barang Saya</h2>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Kelola dan pantau status permohonan retur barang Anda</p>
            </div>
            <a
                href="{{ route('returns.create') }}"
                wire:navigate
                class="w-full sm:w-auto bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm flex items-center justify-center gap-2"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajukan Retur Baru
            </a>
        </div>

        <!-- Success Message -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- List or Empty State -->
        @if ($returnRequests->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 bg-gray-50">
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700">Nomor Pesanan</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700">Barang</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700">Status</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700">Tanggal Ajuan</th>
                            <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($returnRequests as $request)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                <td class="px-3 py-3 text-xs sm:text-sm text-gray-900 font-medium">
                                    {{ $request->order_number }}
                                </td>
                                <td class="px-3 py-3 text-xs sm:text-sm text-gray-700">
                                    @if ($request->items->count() > 0)
                                        @foreach ($request->items as $item)
                                            <div class="mb-1">
                                                <span class="font-medium">{{ $item->orderItem->product_name ?? 'Produk tidak ditemukan' }}</span>
                                                <span class="text-gray-600">({{ $item->orderItem->sku_code ?? '-' }})</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-gray-500">-</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-xs sm:text-sm">
                                    @php
                                        $statusClass = match($request->status->value) {
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            default => 'bg-gray-100 text-gray-800',
                                        };
                                    @endphp
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $request->status->label() }}
                                    </span>
                                </td>
                                <td class="px-3 py-3 text-xs sm:text-sm text-gray-700">
                                    {{ $request->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <button
                                        type="button"
                                        @click="showDetailModal = true; detailData = @json($request)"
                                        class="inline-block bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-1 px-3 rounded text-xs transition"
                                    >
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($returnRequests->hasPages())
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <div class="text-xs sm:text-sm text-gray-600">
                            Menampilkan {{ $returnRequests->firstItem() }} sampai {{ $returnRequests->lastItem() }} dari {{ $returnRequests->total() }} hasil
                        </div>
                        <div class="flex gap-2">
                            @if ($returnRequests->onFirstPage())
                                <button disabled class="px-3 py-1 text-xs sm:text-sm border border-gray-300 rounded text-gray-500 cursor-not-allowed">← Sebelumnya</button>
                            @else
                                <a href="{{ $returnRequests->previousPageUrl() }}" wire:navigate class="px-3 py-1 text-xs sm:text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700">← Sebelumnya</a>
                            @endif

                            @if ($returnRequests->hasMorePages())
                                <a href="{{ $returnRequests->nextPageUrl() }}" wire:navigate class="px-3 py-1 text-xs sm:text-sm border border-gray-300 rounded hover:bg-gray-50 text-gray-700">Berikutnya →</a>
                            @else
                                <button disabled class="px-3 py-1 text-xs sm:text-sm border border-gray-300 rounded text-gray-500 cursor-not-allowed">Berikutnya →</button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada permohonan retur</h3>
                <p class="mt-1 text-sm text-gray-600">Mulai dengan mengajukan permohonan retur untuk barang yang ingin Anda kembalikan.</p>
                <a
                    href="{{ route('returns.create') }}"
                    wire:navigate
                    class="mt-4 inline-block bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-lg transition text-sm"
                >
                    Ajukan Retur Baru
                </a>
            </div>
        @endif
    </div>
</div>
