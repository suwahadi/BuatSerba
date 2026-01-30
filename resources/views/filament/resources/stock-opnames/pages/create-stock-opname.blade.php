<x-filament-panels::page>
    <style>
        .opname-card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        .dark .opname-card {
            background: #1f2937;
            border-color: #374151;
        }
        .opname-card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .dark .opname-card-header {
            background: #111827;
            border-color: #374151;
        }
        .opname-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: #111827;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .dark .opname-card-title {
            color: #f9fafb;
        }
        .opname-card-subtitle {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .opname-card-body {
            padding: 1.5rem;
        }
        .opname-btn-group {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
        }
        .dark .opname-btn-group {
            background: #111827;
            border-color: #374151;
        }
        .opname-btn {
            padding: 0.625rem 1.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .opname-btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }
        .dark .opname-btn-outline {
            background: #374151;
            border-color: #4b5563;
            color: #f9fafb;
        }
        .opname-btn-outline:hover {
            background: #f9fafb;
        }
        .opname-btn-success {
            background: #10b981;
            color: white;
        }
        .opname-btn-success:hover {
            background: #059669;
        }
        .opname-header-icon {
            width: 1.5rem;
            height: 1.5rem;
            padding: 0.25rem;
            background: #ecfdf5;
            color: #10b981;
            border-radius: 6px;
        }
        .dark .opname-header-icon {
            background: rgba(16, 185, 129, 0.2);
        }
    </style>

    <form wire:submit="create">
        <div class="opname-card">
            <div class="opname-card-header">
                <h3 class="opname-card-title">
                    <svg class="opname-header-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                    </svg>
                    Tambah Stok Opname
                </h3>
                <p class="opname-card-subtitle">Isi formulir untuk menambahkan catatan stok opname baru</p>
            </div>
            <div class="opname-card-body">
                {{ $this->form }}
            </div>
            <div class="opname-btn-group">
                <a href="{{ \App\Filament\Resources\StockOpnames\StockOpnameResource::getUrl('index') }}" class="opname-btn opname-btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Batal
                </a>
                <button type="submit" class="opname-btn opname-btn-success">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Simpan
                </button>
            </div>
        </div>
    </form>
</x-filament-panels::page>
