@php
    /** @var \App\Models\PaymentConfirmation $record */
@endphp

<div class="space-y-3 text-sm">
    <div class="flex items-start gap-4">
        <div class="flex-1">
            <div class="font-medium text-base">{{ $record->order?->order_number ?? '-' }}</div>
            <div class="mt-1 text-sm text-gray-700">{{ strtoupper($record->bank ?? '-') }} - {{ $record->nomor_rekening ? ' ' . $record->nomor_rekening : '' }}</div>
            <div class="mt-1 text-sm text-gray-700">a/n {{ $record->nama_lengkap ?? '-' }}</div>
            @if($record->confirmed_at)
                <div class="mt-1 text-xs text-gray-500">Dikirim: {{ $record->confirmed_at->format('d M Y H:i') }}</div>
            @endif
            @if(!empty($record->catatan))
                <div class="mt-2 italic text-gray-600">{{ $record->catatan }}</div>
            @endif
        </div>
        <br>

        {{-- Thumbnail preview 100x100 --}}
        @if($record->bukti_transfer_path)
            <div class="shrink-0">
                <a href="{{ Storage::disk('public')->url($record->bukti_transfer_path) }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ Storage::disk('public')->url($record->bukti_transfer_path) }}" alt="Bukti Transfer" width="300" height="300" class="object-cover rounded border border-gray-200" /><small>Lihat Detail</small>
                </a>
            </div>
        @endif
    </div>

</div>
