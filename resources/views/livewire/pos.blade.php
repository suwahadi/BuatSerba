<div>
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }
        @media (min-width: 1024px) {
            .pos-container {
                grid-template-columns: 380px 1fr;
            }
        }
        .pos-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .dark .pos-card {
            background: #1f2937;
            border-color: #374151;
        }
        .pos-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .dark .pos-card-header {
            background: #111827;
            border-color: #374151;
        }
        .pos-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .dark .pos-card-title {
            color: #f9fafb;
        }
        .pos-card-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .pos-card-body {
            padding: 1.5rem;
        }
        .pos-form-group {
            margin-bottom: 1rem;
        }
        .pos-form-group:last-child {
            margin-bottom: 0;
        }
        .pos-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .dark .pos-label {
            color: #d1d5db;
        }
        .pos-input, .pos-select {
            width: 100%;
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            color: #111827;
        }
        .dark .pos-input, .dark .pos-select {
            background: #1f2937;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .pos-input:focus, .pos-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .pos-input:disabled {
            background: #f3f4f6;
            color: #9ca3af;
        }
        .dark .pos-input:disabled {
            background: #374151;
        }
        .pos-btn-group {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }
        .pos-btn {
            padding: 0.625rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
        }
        .pos-btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        .pos-btn-outline:hover {
            background: #f9fafb;
        }
        .pos-btn-success {
            background: #10b981;
            color: white;
            flex: 1;
        }
        .pos-btn-success:hover {
            background: #059669;
        }
        .pos-btn-success-lg {
            background: #10b981;
            color: white;
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
        }
        .pos-btn-success-lg:hover {
            background: #059669;
        }
        .pos-customer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        @media (min-width: 768px) {
            .pos-customer-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        .dark .pos-customer-grid {
            background: #111827;
        }
        .pos-table-wrapper {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1.5rem;
        }
        .dark .pos-table-wrapper {
            border-color: #374151;
        }
        .pos-table {
            width: 100%;
            min-width: 500px;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .pos-table th {
            background: #f9fafb;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .pos-table th {
            background: #111827;
            color: #9ca3af;
            border-color: #374151;
        }
        .pos-table td {
            padding: 0.875rem 1rem;
            color: #111827;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .pos-table td {
            color: #f9fafb;
            border-color: #374151;
        }
        .pos-table tr:last-child td {
            border-bottom: none;
        }
        .pos-table tr:hover td {
            background: #f9fafb;
        }
        .dark .pos-table tr:hover td {
            background: #1f2937;
        }
        .pos-delete-btn {
            background: #fef2f2;
            color: #dc2626;
            border: none;
            padding: 0.375rem;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .pos-delete-btn:hover {
            background: #fee2e2;
        }
        .pos-qty-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #4f46e5;
            background: #eef2ff;
            border-radius: 6px;
        }
        .dark .pos-qty-badge {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
        }
        .pos-summary {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .dark .pos-summary {
            background: #111827;
        }
        .pos-summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
        }
        .pos-summary-label {
            color: #6b7280;
            font-size: 0.875rem;
        }
        .pos-summary-value {
            font-weight: 500;
            color: #111827;
        }
        .dark .pos-summary-value {
            color: #f9fafb;
        }
        .pos-summary-total {
            border-top: 1px solid #e5e7eb;
            margin-top: 0.5rem;
            padding-top: 1rem;
        }
        .dark .pos-summary-total {
            border-color: #374151;
        }
        .pos-summary-total .pos-summary-label {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
        }
        .dark .pos-summary-total .pos-summary-label {
            color: #f9fafb;
        }
        .pos-summary-total .pos-summary-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #4f46e5;
        }
        .pos-discount-input {
            width: 140px;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-align: right;
        }
        .dark .pos-discount-input {
            background: #1f2937;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .pos-empty {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }
        .pos-empty svg {
            width: 3rem;
            height: 3rem;
            margin: 0 auto 1rem;
        }
        .pos-alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }
        .pos-alert-success {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .pos-alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .pos-error-text {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }
        .pos-required {
            color: #dc2626;
        }
        .pos-sku {
            font-size: 0.75rem;
            color: #9ca3af;
        }
        /* Searchable Select Styles */
        .pos-searchable-select {
            position: relative;
            width: 100%;
        }
        .pos-search-input {
            width: 100%;
            padding: 0.625rem 2.5rem 0.625rem 0.875rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: white;
            color: #111827;
            cursor: text;
        }
        .dark .pos-search-input {
            background: #1f2937;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .pos-search-input:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        .pos-search-input::placeholder {
            color: #9ca3af;
        }
        .pos-dropdown-icon {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            color: #6b7280;
        }
        .pos-dropdown-list {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            max-height: 280px;
            overflow-y: auto;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 50;
        }
        .dark .pos-dropdown-list {
            background: #1f2937;
            border-color: #4b5563;
        }
        .pos-dropdown-item {
            padding: 0.625rem 0.875rem;
            font-size: 0.875rem;
            color: #111827;
            cursor: pointer;
            transition: background-color 0.15s;
        }
        .dark .pos-dropdown-item {
            color: #f9fafb;
        }
        .pos-dropdown-item:hover,
        .pos-dropdown-item.active {
            background: #f3f4f6;
        }
        .dark .pos-dropdown-item:hover,
        .dark .pos-dropdown-item.active {
            background: #374151;
        }
        .pos-dropdown-item-sku {
            font-size: 0.75rem;
            color: #6b7280;
            margin-left: 0.5rem;
        }
        .pos-dropdown-empty {
            padding: 1rem;
            text-align: center;
            color: #9ca3af;
            font-size: 0.875rem;
        }
        .pos-selected-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.5rem;
            background: #eef2ff;
            color: #4f46e5;
            border-radius: 6px;
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }
        .dark .pos-selected-badge {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
        }
        .pos-clear-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: #6b7280;
            display: flex;
            align-items: center;
        }
        .pos-clear-btn:hover {
            color: #dc2626;
        }
        /* Modal Styles - Filament 4 Style */
        .pos-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .pos-modal {
            background: white;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            width: 100%;
            max-width: 400px;
            overflow: hidden;
        }
        .dark .pos-modal {
            background: #1f2937;
        }
        .pos-modal-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .dark .pos-modal-header {
            border-color: #374151;
        }
        .pos-modal-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .dark .pos-modal-title {
            color: #f9fafb;
        }
        .pos-modal-icon {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .pos-modal-icon-warning {
            background: #fef3c7;
            color: #d97706;
        }
        .dark .pos-modal-icon-warning {
            background: rgba(217, 119, 6, 0.2);
        }
        .pos-modal-body {
            padding: 1.25rem 1.5rem;
        }
        .pos-modal-text {
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.5;
            margin: 0;
        }
        .dark .pos-modal-text {
            color: #9ca3af;
        }
        .pos-modal-footer {
            padding: 1rem 1.5rem;
            background: #f9fafb;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        .dark .pos-modal-footer {
            background: #111827;
        }
        .pos-modal-btn {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
        }
        .pos-modal-btn-cancel {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        .dark .pos-modal-btn-cancel {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .pos-modal-btn-cancel:hover {
            background: #f3f4f6;
        }
        .dark .pos-modal-btn-cancel:hover {
            background: #4b5563;
        }
        .pos-modal-btn-danger {
            background: #dc2626;
            color: white;
        }
        .pos-modal-btn-danger:hover {
            background: #b91c1c;
        }
        .pos-modal-icon-success {
            background: #d1fae5;
            color: #059669;
        }
        .dark .pos-modal-icon-success {
            background: rgba(5, 150, 105, 0.2);
        }
        .pos-modal-btn-success {
            background: #10b981;
            color: white;
        }
        .pos-modal-btn-success:hover {
            background: #059669;
        }
    </style>

    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="pos-alert pos-alert-success">
            ✓ {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="pos-alert pos-alert-error">
            ✕ {{ session('error') }}
        </div>
    @endif

    {{-- Main Layout --}}
    <div class="pos-container">
        {{-- Left Panel - Add Product --}}
        <div class="pos-card">
            <div class="pos-card-header">
                <h3 class="pos-card-title">Tambah Produk</h3>
                <p class="pos-card-subtitle">Pilih produk dan tentukan kuantitas</p>
            </div>
            <div class="pos-card-body">
                <div class="pos-form-group">
                    <label class="pos-label">Produk</label>
                    <div class="pos-searchable-select"
                         x-data="{
                             open: false,
                             search: '',
                             products: @js($products->toArray()),
                             prices: @js($productPrices->toArray()),
                             selectedId: $wire.entangle('selectedProduct'),
                             currentPrice: $wire.entangle('price'),
                             get filteredProducts() {
                                 if (!this.search) return Object.entries(this.products);
                                 const term = this.search.toLowerCase();
                                 return Object.entries(this.products).filter(([id, name]) =>
                                     name.toLowerCase().includes(term)
                                 );
                             },
                             get selectedName() {
                                 return this.selectedId ? this.products[this.selectedId] : '';
                             },
                             selectProduct(id) {
                                 this.selectedId = id;
                                 this.currentPrice = this.prices[id] || 0;
                                 this.search = '';
                                 this.open = false;
                             },
                             clearSelection() {
                                 this.selectedId = null;
                                 this.currentPrice = 0;
                                 this.search = '';
                             },
                             formatPrice(price) {
                                 return 'Rp ' + new Intl.NumberFormat('id-ID').format(price || 0);
                             }
                         }"
                         @click.outside="open = false"
                         @keydown.escape.window="open = false">

                        {{-- Search Input --}}
                        <div class="relative">
                            <input type="text"
                                   class="pos-search-input"
                                   x-model="search"
                                   @focus="open = true"
                                   @click="open = true"
                                   :placeholder="selectedName || '-- Ketik untuk mencari produk --'"
                                   autocomplete="off">
                            <div class="pos-dropdown-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>

                        {{-- Selected Product Badge --}}
                        <template x-if="selectedId && selectedName">
                            <div class="pos-selected-badge">
                                <span x-text="selectedName"></span>
                                <button type="button" class="pos-clear-btn" @click="clearSelection()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Dropdown List --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 transform scale-95"
                             x-transition:enter-end="opacity-100 transform scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 transform scale-100"
                             x-transition:leave-end="opacity-0 transform scale-95"
                             class="pos-dropdown-list">

                            <template x-if="filteredProducts.length === 0">
                                <div class="pos-dropdown-empty">
                                    <span x-text="search ? 'Tidak ada produk yang cocok' : 'Tidak ada produk tersedia'"></span>
                                </div>
                            </template>

                            <template x-for="[id, name] in filteredProducts" :key="id">
                                <div class="pos-dropdown-item"
                                     :class="{ 'active': selectedId == id }"
                                     @click="selectProduct(id)">
                                    <span x-text="name"></span>
                                </div>
                            </template>
                        </div>

                        {{-- Harga Satuan Field (moved inside x-data scope) --}}
                        <div class="pos-form-group" style="margin-top: 1rem;">
                            <label class="pos-label">Harga Satuan</label>
                            <input type="text"
                                   class="pos-input"
                                   :value="formatPrice(currentPrice)"
                                   readonly
                                   disabled>
                        </div>
                    </div>
                </div>

                <div class="pos-form-group">
                    <label class="pos-label">Jumlah</label>
                    <input type="number" class="pos-input" wire:model="quantity" min="1" placeholder="1">
                </div>

                <div class="pos-btn-group" x-data="{ showClearModal: false }">
                    <button type="button" class="pos-btn pos-btn-outline" @click="showClearModal = true">Clear</button>

                    {{-- Clear Confirmation Modal --}}
                    <template x-teleport="body">
                        <div x-show="showClearModal"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="pos-modal-backdrop"
                             @click.self="showClearModal = false"
                             @keydown.escape.window="showClearModal = false"
                             style="display: none;">

                            <div class="pos-modal"
                                 x-show="showClearModal"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 @click.stop>

                                <div class="pos-modal-header">
                                    <h3 class="pos-modal-title">
                                        <span class="pos-modal-icon pos-modal-icon-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                                <line x1="12" y1="9" x2="12" y2="13"></line>
                                                <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                            </svg>
                                        </span>
                                        Konfirmasi Hapus
                                    </h3>
                                </div>

                                <div class="pos-modal-body">
                                    <p class="pos-modal-text">
                                        Apakah Anda yakin ingin mengosongkan semua item di keranjang? Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>

                                <div class="pos-modal-footer">
                                    <button type="button"
                                            class="pos-modal-btn pos-modal-btn-cancel"
                                            @click="showClearModal = false">
                                        Batal
                                    </button>
                                    <button type="button"
                                            class="pos-modal-btn pos-modal-btn-danger"
                                            @click="showClearModal = false; $wire.clearItems()">
                                        Ya, Hapus Semua
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                    <button type="button" class="pos-btn pos-btn-success" wire:click="addItem">+ Tambah Item</button>
                </div>
            </div>

            {{-- Recent Transactions Table --}}
            <div class="pos-card" style="border: none !important; border-radius: 0 !important; margin-top: 10px;">
                <div class="pos-card-header">
                    <h3 class="pos-card-title">10 Transaksi Terakhir</h3>
                </div>
                <div class="pos-card-body" style="padding: 0;">
                    <div style="width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                            <thead>
                                <tr style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Order</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Customer</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em;">Total (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $trx)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 0.75rem 1rem;">
                                            <a href="{{ route('filament.admin.pages.pos.{orderNumber}', $trx->order_number) }}" style="font-weight: 600; color: #2563eb; text-decoration: none;">{{ $trx->order_number }}</a>
                                            <br><span style="font-size: 0.75rem; color: #9ca3af;">{{ $trx->created_at->format('d M y - H:i:s') }}</span>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; color: #374151;">{{ $trx->customer_name }}</td>
                                        <td style="padding: 0.75rem 1rem; text-align: right; font-weight: 600; color: #059669;">{{ number_format($trx->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" style="padding: 2rem 1rem; text-align: center; color: #9ca3af;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 0.5rem;"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                                            <p>Belum ada transaksi</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </div>

        {{-- Right Panel - Order Summary --}}
        <div class="pos-card">
            <div class="pos-card-header">
                <h3 class="pos-card-title">Keranjang Belanja</h3>
                <p class="pos-card-subtitle" style="display: flex; align-items: center; gap: 0.5rem; margin-top: 0.25rem; color: #6b7280; font-size: 0.875rem;">
    <span>{{ count($items) }} item</span>
    <span style="color: #d1d5db;">•</span>
    <span style="display: inline-flex; align-items: center; gap: 0.25rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        Kasir: {{ Auth::user()->name }}
    </span>
</p>
            </div>
            <div class="pos-card-body">
                {{-- Customer Info --}}
                <div class="pos-customer-grid">
                    <div class="pos-searchable-select"
                         x-data="{
                             showSearch: false,
                             searchQuery: '',
                             selectedId: $wire.entangle('selectedCustomerId'),
                             get customers() {
                                 return $wire.searchResults || [];
                             },
                             openSearch() {
                                 this.showSearch = true;
                                 this.searchQuery = '';
                                 $wire.set('searchResults', []);
                                 $nextTick(() => {
                                     this.$refs.searchInput?.focus();
                                 });
                             },
                             doSearch() {
                                 if (this.searchQuery.length > 0) {
                                     $wire.set('customerSearch', this.searchQuery);
                                 }
                             },
                             selectCustomer(customer) {
                                 $wire.selectCustomer(customer.id);
                                 this.showSearch = false;
                                 this.searchQuery = '';
                             },
                             clearSelection() {
                                 $wire.clearCustomerSelection();
                             },
                             closeSearch() {
                                 this.showSearch = false;
                                 this.searchQuery = '';
                                 $wire.set('searchResults', []);
                             }
                         }"
                         @keydown.escape.window="closeSearch()">
                        <label class="pos-label">Nama Customer <span class="pos-required">*</span></label>
                        <div style="display: flex; gap: 0.5rem;">
                            <div style="flex: 1; position: relative;">
                                <input type="text"
                                       class="pos-input"
                                       wire:model="customerName"
                                       placeholder="Nama lengkap"
                                       :readonly="selectedId"
                                       autocomplete="off">
                            </div>
                            <button type="button"
                                    class="pos-btn pos-btn-outline"
                                    style="padding: 0.5rem 0.75rem; display: flex; align-items: center; gap: 0.25rem;"
                                    @click="openSearch()"
                                    title="Cari Customer Terdaftar">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <path d="m21 21-4.35-4.35"></path>
                                </svg>
                                <span style="font-size: 0.75rem;">Cari</span>
                            </button>
                        </div>

                        <template x-if="selectedId">
                            <div class="pos-selected-badge">
                                <span>Customer Terdaftar</span>
                                <button type="button" class="pos-clear-btn" @click="clearSelection()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Search Modal --}}
                        <template x-teleport="body">
                            <div x-show="showSearch"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="pos-modal-backdrop"
                                 @click.self="closeSearch()"
                                 style="display: none;">

                                <div class="pos-modal" style="max-width: 500px;"
                                     x-show="showSearch"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 transform scale-100"
                                     x-transition:leave-end="opacity-0 transform scale-95"
                                     @click.stop>

                                    <div class="pos-modal-header">
                                        <h3 class="pos-modal-title">
                                            <span class="pos-modal-icon" style="background: #eef2ff; color: #4f46e5;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <circle cx="11" cy="11" r="8"></circle>
                                                    <path d="m21 21-4.35-4.35"></path>
                                                </svg>
                                            </span>
                                            Cari Customer Terdaftar
                                        </h3>
                                    </div>

                                    <div class="pos-modal-body">
                                        <div style="margin-bottom: 1rem;">
                                            <input type="text"
                                                   class="pos-input"
                                                   x-ref="searchInput"
                                                   x-model="searchQuery"
                                                   @input.debounce.300ms="doSearch()"
                                                   placeholder="Ketik nama, email, atau phone...">
                                        </div>

                                        <div style="max-height: 300px; overflow-y: auto;">
                                            <template x-if="customers.length === 0 && searchQuery.length > 0">
                                                <div class="pos-dropdown-empty" style="padding: 2rem; text-align: center;">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto 1rem; color: #9ca3af;">
                                                        <circle cx="11" cy="11" r="8"></circle>
                                                        <path d="m21 21-4.35-4.35"></path>
                                                    </svg>
                                                    <p style="color: #6b7280;">Tidak ditemukan data customer...</p>
                                                </div>
                                            </template>

                                            <template x-if="customers.length === 0 && searchQuery.length === 0">
                                                <div class="pos-dropdown-empty" style="padding: 2rem; text-align: center;">
                                                    <p style="color: #6b7280;">Menemukan data customer...</p>
                                                </div>
                                            </template>

                                            <template x-for="customer in customers" :key="customer.id">
                                                <div class="pos-dropdown-item"
                                                     style="padding: 1rem; border-bottom: 1px solid #e5e7eb; cursor: pointer;"
                                                     :class="{ 'active': selectedId == customer.id }"
                                                     @click="selectCustomer(customer)">
                                                    <div style="font-weight: 600; font-size: 0.95rem; margin-bottom: 0.5rem; color: #111827;" x-text="customer.name"></div>
                                                    <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                                        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #6b7280;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                                                <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                                            </svg>
                                                            <span x-text="customer.email || '-'" style="word-break: break-all;"></span>
                                                        </div>
                                                        <div x-show="customer.phone" style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; color: #6b7280;">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0;">
                                                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"></path>
                                                            </svg>
                                                            <span x-text="customer.phone"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <div class="pos-modal-footer">
                                        <button type="button"
                                                class="pos-modal-btn pos-modal-btn-cancel"
                                                @click="closeSearch()">
                                            Tutup
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>

                        @error('customerName')
                            <p class="pos-error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="pos-label">Email <span class="pos-required">*</span></label>
                        <input type="email" class="pos-input" wire:model="customerEmail" placeholder="email@example.com" :readonly="$wire.selectedCustomerId">
                        @error('customerEmail')
                            <p class="pos-error-text">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="pos-label">Telepon <span class="pos-required">*</span></label>
                        <input type="text" class="pos-input" wire:model="customerPhone" placeholder="08xxxxxxxxxx" :readonly="$wire.selectedCustomerId">
                        @error('customerPhone')
                            <p class="pos-error-text">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="pos-table-wrapper">
                    <table class="pos-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Produk</th>
                                <th style="text-align: right;">Harga</th>
                                <th style="text-align: center; width: 70px;">Qty</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td>
                                        <button type="button" class="pos-delete-btn" wire:click="removeItem({{ $index }})">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M3 6h18"></path>
                                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </td>
                                    <td>
                                        <div style="font-weight: 500;">{{ $item['product_name'] }}</div>
                                        <div class="pos-sku">{{ $item['sku_code'] }}</div>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
                                    <td style="text-align: center;">
                                        <span class="pos-qty-badge">{{ $item['quantity'] }}</span>
                                    </td>
                                    <td style="text-align: right; font-weight: 600; white-space: nowrap;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">
                                        <div class="pos-empty">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                                            </svg>
                                            <p style="font-weight: 500;">Keranjang masih kosong</p>
                                            <p style="font-size: 0.75rem;">Pilih produk dari panel sebelah kiri</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Order Summary --}}
                <div class="pos-summary">
                    <div class="pos-summary-row">
                        <span class="pos-summary-label">Subtotal</span>
                        <span class="pos-summary-value">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="pos-summary-row">
                        <span class="pos-summary-label">Diskon (Rp)</span>
                        <input type="number" class="pos-discount-input" wire:model.live="discount" min="0" placeholder="0">
                    </div>
                    <div class="pos-summary-row pos-summary-total">
                        <span class="pos-summary-label">Grand Total</span>
                        <span class="pos-summary-value">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Checkout Button --}}
                <div x-data="{ showCheckoutModal: false }">
                    <button type="button" class="pos-btn pos-btn-success-lg" @click="showCheckoutModal = true">
                        CHECKOUT
                    </button>

                    {{-- Checkout Confirmation Modal --}}
                    <template x-teleport="body">
                        <div x-show="showCheckoutModal"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="pos-modal-backdrop"
                             @click.self="showCheckoutModal = false"
                             @keydown.escape.window="showCheckoutModal = false"
                             style="display: none;">

                            <div class="pos-modal"
                                 x-show="showCheckoutModal"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 transform scale-95"
                                 x-transition:enter-end="opacity-100 transform scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 transform scale-100"
                                 x-transition:leave-end="opacity-0 transform scale-95"
                                 @click.stop>

                                <div class="pos-modal-header">
                                    <h3 class="pos-modal-title">
                                        Konfirmasi Checkout
                                    </h3>
                                </div>

                                <div class="pos-modal-body">
                                    <p class="pos-modal-text">
                                        Apakah Anda yakin ingin menyelesaikan transaksi ini? Pastikan semua data pelanggan dan item sudah benar.
                                    </p>
                                </div>

                                <div class="pos-modal-footer">
                                    <button type="button"
                                            class="pos-modal-btn pos-modal-btn-cancel"
                                            @click="showCheckoutModal = false">
                                        Batal
                                    </button>
                                    <button type="button"
                                            class="pos-modal-btn pos-modal-btn-success"
                                            @click="showCheckoutModal = false; $wire.checkout()">
                                        Ya, Proses Checkout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{ 
            showSuccessModal: false, 
            orderNumber: '' 
         }"
         x-on:show-success-modal.window="showSuccessModal = true; orderNumber = $event.detail.orderNumber">
        <template x-teleport="body">
            <div x-show="showSuccessModal"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="pos-modal-backdrop"
                 style="display: none;">

                <div class="pos-modal"
                     x-show="showSuccessModal"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 transform scale-100"
                     x-transition:leave-end="opacity-0 transform scale-95"
                     @click.stop>

                    <div class="pos-modal-header">
                        <h3 class="pos-modal-title">
                            <span class="pos-modal-icon pos-modal-icon-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 6L9 17l-5-5"></path>
                                </svg>
                            </span>
                            Transaksi Berhasil
                        </h3>
                    </div>

                    <div class="pos-modal-body">
                        <p class="pos-modal-text">
                            Transaksi telah berhasil disimpan dengan nomor order:
                        </p>
                        <p style="font-size: 1.125rem; font-weight: 700; color: #10b981; margin-top: 0.5rem;" x-text="orderNumber"></p>
                    </div>

                    <div class="pos-modal-footer">
                        <button type="button"
                                class="pos-modal-btn pos-modal-btn-cancel"
                                @click="showSuccessModal = false">
                            Tutup
                        </button>
                        <a :href="'/admin/pos/' + orderNumber"
                           class="pos-modal-btn pos-modal-btn-success"
                           style="text-decoration: none; display: inline-block; text-align: center;">
                            Detail Struk
                        </a>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        document.addEventListener('livewire:init', function() {
            Livewire.on('showSuccessModal', (data) => {
                window.dispatchEvent(new CustomEvent('show-success-modal', { 
                    detail: { orderNumber: data.orderNumber }
                }));
            });
        });
    </script>
</div>
