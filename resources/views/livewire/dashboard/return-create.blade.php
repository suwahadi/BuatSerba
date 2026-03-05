<div>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Ajukan Permohonan Retur Barang</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Pilih pesanan dan barang yang ingin diretur</p>
                </div>
                <a href="{{ route('returns.index') }}" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Error Message -->
        @if ($errorMessage)
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm text-red-700">{{ $errorMessage }}</p>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="confirmSubmit" class="space-y-4 sm:space-y-6">
            <!-- Order Selection -->
            <div>
                <label for="selectedOrderId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Pilih Pesanan <span class="text-red-500">*</span>
                </label>
                <select
                    id="selectedOrderId"
                    wire:model="selectedOrderId"
                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    @if($showConfirmDialog) disabled @endif
                >
                    <option value="">-- Pilih Pesanan --</option>
                    @foreach ($orders as $order)
                        <option value="{{ $order['id'] }}">{{ $order['label'] }}</option>
                    @endforeach
                </select>
                @error('selectedOrderId')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Hanya pesanan yang sudah lunas dan selesai yang dapat diretur</p>
            </div>

            <!-- Items List Section -->
            @if (!empty($items))
                <div wire:target="updatedSelectedOrderId" wire:loading.remove>
                    <label for="selectedOrderItemId" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                        Pilih Barang untuk Diretur <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="selectedOrderItemId"
                        wire:model.live="selectedOrderItemId"
                        class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                        @if($showConfirmDialog) disabled @endif
                    >
                        <option value="">-- Pilih Barang --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item['id'] }}">{{ $item['label'] }}</option>
                        @endforeach
                    </select>
                    @error('selectedOrderItemId')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <!-- Loading State -->
                <div wire:target="updatedSelectedOrderId" wire:loading class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex gap-3 items-center">
                        <svg class="inline-block w-5 h-5 animate-spin text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm text-blue-700">Memuat barang...</p>
                    </div>
                </div>
            @elseif (blank($selectedOrderId))
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-blue-700">Silakan pilih pesanan untuk melihat daftar barang</p>
                    </div>
                </div>
            @endif

            <!-- Note Section -->
            <div>
                <label for="note" class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                    Catatan / Alasan Retur (Opsional, maksimal 1000 karakter)
                </label>
                <textarea
                    id="note"
                    wire:model="note"
                    rows="4"
                    placeholder="Contoh: Barang rusak saat dikirim, warna tidak sesuai, dll..."
                    class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition"
                    @if($showConfirmDialog) disabled @endif
                ></textarea>
                @error('note')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">{{ strlen($note) }}/1000 karakter</p>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 pt-2 sm:pt-4 border-t border-gray-200">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition text-sm sm:text-base flex items-center justify-center gap-2"
                >
                    <span wire:loading.remove>
                    </span>
                    <span wire:loading.remove>Ajukan Retur</span>
                    <span wire:loading>
                        Mengirim...
                    </span>
                </button>
                <a
                    href="{{ route('returns.index') }}"
                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-4 rounded-lg transition text-center text-sm sm:text-base flex items-center justify-center gap-2"
                >
                    Batal
                </a>
            </div>
        </form>
    </div>

    <!-- Confirmation Dialog -->
    @if($showConfirmDialog)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6 transform transition-all">
                <!-- Icon -->
                <div class="flex justify-center mb-4">
                    <div class="flex-shrink-0">
                        <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 0v2m0-12v-2m0 0L9.707 5.293m4.586 0L14.293 5.293M5 12a7 7 0 1114 0 7 7 0 01-14 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Content -->
                <h3 class="text-lg font-bold text-gray-900 text-center mb-2">Konfirmasi Retur Barang</h3>
                <p class="text-sm text-gray-600 text-center mb-6">
                    Apakah Anda yakin ingin mengajukan permohonan retur untuk barang ini? Anda dapat melihat status permohonan di halaman daftar retur.
                </p>

                <!-- Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6 text-sm">
                    @php
                        $selectedOrder = collect($orders)->firstWhere('id', $selectedOrderId);
                        $selectedItem = collect($items)->firstWhere('id', $selectedOrderItemId);
                    @endphp
                    @if($selectedOrder && $selectedItem)
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Pesanan:</span>
                                <span class="font-medium text-gray-900">{{ $selectedOrder['label'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Barang:</span>
                                <span class="font-medium text-gray-900 text-right">{{ $selectedItem['label'] }}</span>
                            </div>
                            @if($note)
                                <div class="flex justify-between items-start">
                                    <span class="text-gray-600">Catatan:</span>
                                    <span class="font-medium text-gray-900 text-right max-w-xs">{{ Str::limit($note, 50) }}</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button
                        wire:click="cancelConfirm"
                        type="button"
                        class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-900 font-medium py-2 px-4 rounded-lg transition text-sm"
                    >
                        Tidak
                    </button>
                    <button
                        wire:click="submit"
                        wire:loading.attr="disabled"
                        type="button"
                        class="flex-1 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition text-sm flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove>Ya, Ajukan Retur</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="inline-block w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Proses...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
