<div x-data="{ showDetailModal: false, detailData: null }">
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
                                    <div class="flex items-start gap-3">
                                        <!-- Thumbnail Bukti -->
                                        @if($request->image_proof && count($request->image_proof) > 0)
                                            <div class="flex-shrink-0">
                                                <img 
                                                    src="{{ Storage::url($request->image_proof[0]) }}" 
                                                    alt="Bukti"
                                                    class="w-12 h-12 object-cover rounded border border-gray-200"
                                                >
                                                @if(count($request->image_proof) > 1)
                                                    <span class="text-xs text-gray-500 mt-1 block">+{{ count($request->image_proof) - 1 }} lagi</span>
                                                @endif
                                            </div>
                                        @endif
                                        <div>
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
                                        </div>
                                    </div>
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
                                        @click="showDetailModal = true; detailData = @js([
                                            'order_number' => $request->order_number,
                                            'status' => $request->status->value,
                                            'status_label' => $request->status->label(),
                                            'created_at' => $request->created_at->toISOString(),
                                            'note' => $request->note,
                                            'image_proof' => $request->image_proof ?? [],
                                            'admin_note' => $request->admin_note,
                                            'handled_at' => $request->handled_at?->toISOString(),
                                            'items' => $request->items->map(fn($item) => [
                                                'product_name' => $item->orderItem->product_name ?? 'Produk tidak ditemukan',
                                                'sku_code' => $item->orderItem->sku_code ?? '-',
                                                'quantity' => $item->quantity,
                                            ])->toArray(),
                                        ])"
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

    <!-- Detail Modal - Minimalis -->
    <template x-if="showDetailModal && detailData">
        <div class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50 p-4" @click.self="showDetailModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full overflow-hidden">
                <!-- Header -->
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-900">Detail Retur</h3>
                    <button @click="showDetailModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Content -->
                <div class="p-4 space-y-3 max-h-[70vh] overflow-y-auto">
                    <!-- Order Number & Status -->
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500">Nomor Pesanan</p>
                            <p class="text-sm font-semibold text-gray-900" x-text="detailData.order_number"></p>
                        </div>
                        <span 
                            class="px-2.5 py-1 rounded-full text-xs font-medium"
                            :class="{
                                'bg-yellow-100 text-yellow-700': detailData.status === 'pending',
                                'bg-green-100 text-green-700': detailData.status === 'approved',
                                'bg-red-100 text-red-700': detailData.status === 'rejected'
                            }"
                            x-text="detailData.status_label"
                        ></span>
                    </div>

                    <!-- Items -->
                    <template x-if="detailData.items && detailData.items.length > 0">
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5">Barang yang Diretur</p>
                            <div class="space-y-1">
                                <template x-for="(item, index) in detailData.items" :key="index">
                                    <div class="text-sm text-gray-900">
                                        <span x-text="item.product_name"></span>
                                        <span class="text-gray-500" x-text="' (' + item.sku_code + ')'"></span>
                                        <span class="text-gray-500"> - Qty: <span x-text="item.quantity"></span></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Catatan User -->
                    <template x-if="detailData.note">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Alasan Retur</p>
                            <p class="text-sm text-gray-900 bg-gray-50 rounded p-2" x-text="detailData.note"></p>
                        </div>
                    </template>

                    <!-- Bukti Foto -->
                    <template x-if="detailData.image_proof && detailData.image_proof.length > 0">
                        <div>
                            <p class="text-xs text-gray-500 mb-1.5">Bukti Foto</p>
                            <div class="flex gap-2 flex-wrap">
                                <template x-for="(img, index) in detailData.image_proof" :key="index">
                                    <a :href="'/storage/' + img" target="_blank" class="block">
                                        <img 
                                            :src="'/storage/' + img" 
                                            :alt="'Bukti ' + (index + 1)"
                                            class="w-16 h-16 object-cover rounded border border-gray-200 hover:opacity-80 transition"
                                        >
                                    </a>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Admin Note -->
                    <template x-if="detailData.admin_note">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Catatan Admin</p>
                            <p class="text-sm text-gray-900 bg-blue-50 rounded p-2 border border-blue-100" x-text="detailData.admin_note"></p>
                        </div>
                    </template>

                    <!-- Metadata -->
                    <div class="pt-2 border-t border-gray-100 space-y-1">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Tanggal Ajuan:</span>
                            <span x-text="new Date(detailData.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })"></span>
                        </div>
                        <template x-if="detailData.handled_at">
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Ditangani:</span>
                                <span x-text="new Date(detailData.handled_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
                    <button 
                        @click="showDetailModal = false"
                        class="w-full bg-gray-200 hover:bg-gray-300 text-gray-900 font-medium py-1.5 px-3 rounded text-xs transition"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
