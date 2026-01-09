<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-6 text-white">
            <h1 class="text-xl font-bold">Konfirmasi Pembayaran</h1>
            <p class="text-sm opacity-90 mt-1">Order ID: #{{ $orderNumber }}</p>
        </div>

        <div class="p-6">
            <form wire:submit="submit" class="space-y-6">
                
                <!-- Order ID -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order ID # <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ $orderNumber }}" disabled 
                        class="w-full rounded-[5px] border border-gray-300 bg-gray-100 text-gray-500 shadow-sm sm:text-sm px-3 py-2">
                </div>

                <!-- Sender Name -->
                <div>
                    <label for="sender_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap Pengirim <span class="text-red-500">*</span></label>
                    <input wire:model="sender_name" type="text" id="sender_name"
                        class="w-full rounded-[5px] border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-3 py-2">
                    @error('sender_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Sender Bank -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Rekening Pengirim <span class="text-red-500">*</span></label>
                    <select wire:model="sender_bank" id="sender_bank" class="w-full rounded-[5px] border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-3 py-2">
                        <option value="">Pilih Bank Pengirim</option>
                        @foreach($banks as $bank)
                        <option value="{{ $bank }}">{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('sender_bank') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Sender Account Number -->
                <div>
                    <label for="sender_account_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Rekening Pengirim <span class="text-red-500">*</span></label>
                    <input wire:model="sender_account_number" type="text" id="sender_account_number" inputmode="numeric"
                        class="w-full rounded-[5px] border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-3 py-2">
                    @error('sender_account_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Proof File -->
                <div>
                    <label for="proof_file" class="block text-sm font-medium text-gray-700 mb-1">Upload Bukti Transfer <span class="text-red-500">*</span></label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-[5px] hover:bg-gray-50 transition-colors cursor-pointer relative"
                        x-data="{ isUploading: false, progress: 0 }"
                        x-on:livewire-upload-start="isUploading = true"
                        x-on:livewire-upload-finish="isUploading = false"
                        x-on:livewire-upload-error="isUploading = false"
                        x-on:livewire-upload-progress="progress = $event.detail.progress">
                        
                        <input wire:model="proof_file" id="proof_file" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                        
                        <div class="space-y-1 text-center">
                            @if($proof_file)
                                <div class="text-sm text-green-600 font-medium">
                                    File terpilih: {{ $proof_file->getClientOriginalName() }}
                                </div>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600 justify-center">
                                    <span class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                        <span>Upload file</span>
                                    </span>
                                    <p class="pl-1">atau drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF up to 2MB</p>
                            @endif

                            <!-- Progress Bar -->
                            <div x-show="isUploading" class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
                                <div class="bg-green-600 h-2.5 rounded-full" :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>
                    </div>
                    @error('proof_file') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
                    <textarea wire:model="notes" id="notes" rows="3"
                        class="w-full rounded-[5px] border border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm px-3 py-2"></textarea>
                    @error('notes') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="pt-4 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('payment', ['code' => $orderNumber]) }}" 
                       class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 text-center transition">
                        Kembali
                    </a>
                    <button type="submit" 
                        wire:loading.attr="disabled"
                        class="flex-1 px-4 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition flex items-center justify-center space-x-2 disabled:opacity-75 disabled:cursor-not-allowed">
                        <span wire:loading.remove>Kirim Konfirmasi</span>
                        <span wire:loading class="flex items-center">
                            Mengirim...
                        </span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
