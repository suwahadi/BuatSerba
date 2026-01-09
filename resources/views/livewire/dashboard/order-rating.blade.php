<div>
    <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
        <!-- Header -->
        <div class="border-b border-gray-200 pb-3 sm:pb-4 mb-4 sm:mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Beri Penilaian</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">Berikan penilaian untuk pesanan Anda</p>
                </div>
                <a href="{{ route('dashboard') }}" class="text-xs sm:text-sm text-gray-600 hover:text-gray-900 font-medium flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Info Order Section -->
        <div class="bg-gray-50 rounded-lg p-3 sm:p-4 mb-6 border border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between gap-2 sm:gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">No. Pesanan</p>
                    <p class="font-bold text-xs sm:text-sm text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-0.5">Tanggal Pesanan</p>
                    <p class="font-medium text-xs sm:text-sm text-gray-900">{{ $order->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>

        <form wire:submit="save" class="space-y-6 sm:space-y-8 divide-y divide-gray-100">
            @foreach($order->items as $index => $item)
                @php
                    $productRating = $items[$index]['rating'] ?? 0;
                @endphp
                <div wire:key="item-{{ $item->id }}" class="{{ !$loop->first ? 'pt-6 sm:pt-8' : '' }}">
                    <div class="flex flex-col md:flex-row gap-4 sm:gap-6">
                        <!-- Product Info -->
                        <div class="flex gap-3 sm:gap-4 md:w-1/3">
                            <div class="w-16 h-16 sm:w-20 sm:h-20 flex-shrink-0 bg-gray-100 rounded-lg overflow-hidden border border-gray-200">
                                @if($item->product->main_image)
                                    <img src="{{ Storage::url($item->product->main_image) }}" 
                                         alt="{{ $item->product->name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-xs sm:text-sm text-gray-900 line-clamp-2 leading-tight">{{ $item->product->name }}</h3>
                                <p class="text-[10px] sm:text-xs text-gray-500 mt-1">{{ $item->quantity }} x {{ format_rupiah($item->price) }}</p>
                            </div>
                        </div>

                        <!-- Rating Form -->
                        <div class="flex-grow space-y-3 sm:space-y-4">
                            <!-- Star Rating -->
                            <!-- Star Rating -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Penilaian Anda</label>
                                <div class="flex flex-row-reverse justify-end gap-1">
                                    @for($i = 5; $i >= 1; $i--)
                                        <input type="radio" 
                                               id="star_{{ $index }}_{{ $i }}" 
                                               name="rating_{{ $index }}" 
                                               value="{{ $i }}" 
                                               wire:model="items.{{ $index }}.rating" 
                                               class="peer hidden"
                                        >
                                        <label for="star_{{ $index }}_{{ $i }}" 
                                               class="cursor-pointer text-gray-200 peer-checked:text-yellow-400 peer-hover:text-yellow-400 hover:text-yellow-400 peer-checked:peer-hover:text-yellow-400 peer-checked:[&~label]:text-yellow-400 hover:[&~label]:text-yellow-400 transition-colors p-0.5">
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8 fill-current" viewBox="0 0 24 24">
                                                <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                            </svg>
                                        </label>
                                    @endfor
                                </div>
                                <div class="mt-1 h-4">
                                    @error("items.{$index}.rating") 
                                        <span class="text-red-600 text-[10px] sm:text-xs">{{ $message }}</span> 
                                    @enderror
                                </div>
                            </div>

                            <!-- Comment -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">Ulasan Anda</label>
                                <textarea wire:model="items.{{ $index }}.review"
                                          rows="3"
                                          class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 bg-gray-50 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition shadow-sm placeholder-gray-400"
                                          placeholder="Bagikan pengalaman kamu disini..."></textarea>
                                @error("items.{$index}.review") <span class="text-red-600 text-[10px] sm:text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1 sm:mb-2">
                                    Foto Ulasan Anda (Maks. 5)
                                </label>
                                <div class="flex flex-wrap gap-2 sm:gap-3">
                                    <!-- Preview Images -->
                                    @if(!empty($items[$index]['images']))
                                        @foreach($items[$index]['images'] as $key => $image)
                                            <div class="relative w-16 h-16 sm:w-20 sm:h-20 rounded-lg overflow-hidden border border-gray-200 group">
                                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                                                <button type="button" 
                                                        wire:click="removeImage({{ $index }}, {{ $key }})"
                                                        class="absolute top-0 right-0 p-1 bg-red-600/80 hover:bg-red-600 text-white opacity-0 group-hover:opacity-100 transition-opacity rounded-bl-lg backdrop-blur-sm">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    @endif

                                    <!-- Upload Button -->
                                    @if(count($items[$index]['images'] ?? []) < 5)
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 relative border-2 border-dashed border-gray-300 rounded-lg hover:border-green-500 hover:bg-green-50/50 transition-colors bg-white">
                                            <input type="file" 
                                                   wire:model="items.{{ $index }}.temp_images" 
                                                   multiple
                                                   accept="image/*"
                                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                            <div class="absolute inset-0 flex flex-col items-center justify-center text-gray-400 pointer-events-none">
                                                <svg class="w-5 h-5 sm:w-6 sm:h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                                <span class="text-[8px] sm:text-[10px] font-medium">Tambah</span>
                                            </div>
                                            <!-- Spinner -->
                                            <div wire:loading wire:target="items.{{ $index }}.temp_images" 
                                                 class="absolute inset-0 bg-white/90 flex items-center justify-center z-20 rounded-lg">
                                                <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <p class="text-[10px] sm:text-xs text-gray-400 mt-2">Format: JPG, PNG. Maks 2MB per file.</p>
                                @error("items.{$index}.images") <span class="text-red-600 text-[10px] sm:text-xs block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="pt-6 sm:pt-8 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('dashboard') }}" 
                   class="px-4 sm:px-6 py-2 sm:py-2.5 border border-gray-300 text-gray-700 text-xs sm:text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="px-4 sm:px-6 py-2 sm:py-2.5 bg-green-600 text-white text-xs sm:text-sm font-medium rounded-lg hover:bg-green-700 transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 flex items-center gap-2 shadow-sm">
                    <span wire:loading.remove>Kirim Penilaian</span>
                    <span wire:loading class="items-center">
                        Mengirim<span class="loading-dot">.</span><span class="loading-dot" style="animation-delay: 0.2s">.</span><span class="loading-dot" style="animation-delay: 0.4s">.</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
    <style>
        @keyframes smoothAppear {
            0% { opacity: 0; transform: translateY(2px); }
            20% { opacity: 1; transform: translateY(0); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .loading-dot {
            display: inline-block;
            opacity: 0;
            animation: smoothAppear 1.4s infinite;
            margin-left: 1px;
        }
    </style>
</div>
