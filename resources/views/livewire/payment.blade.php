<div>
    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold">Pembayaran Pesanan</h1>
                        <p class="mt-1 opacity-90">Order #{{ $order->order_number }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-3xl font-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                        <p class="mt-1 text-sm opacity-90">Total Pembayaran</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Nama Pemesan</p>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="font-medium">{{ $order->customer_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Metode Pembayaran</p>
                        <p class="font-medium capitalize">{{ str_replace('-', ' ', $order->payment_method) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status Pembayaran</p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                            @if($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            @if(isset($paymentInstructions) && !empty($paymentInstructions))
            <div class="p-6 border-b border-gray-100 bg-blue-50">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Cara Pembayaran</h3>
                
                @if($paymentInstructions['type'] === 'virtual_account')
                <div class="bg-white rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-medium">{{ strtoupper($paymentInstructions['bank']) }} Virtual Account</span>
                        <button onclick="copyToClipboard('{{ $paymentInstructions['va_number'] }}')" 
                                class="text-blue-600 hover:text-blue-800 text-sm">
                            Salin
                        </button>
                    </div>
                    <div class="text-2xl font-mono font-bold text-center py-3 bg-gray-50 rounded">
                        {{ $paymentInstructions['va_number'] }}
                    </div>
                    <p class="text-sm text-gray-600 mt-2 text-center">
                        Gunakan nomor ini untuk melakukan pembayaran melalui ATM, mobile banking, atau internet banking
                    </p>
                </div>
                @endif

                @if($paymentInstructions['type'] === 'ewallet')
                <div class="bg-white rounded-lg p-4 mb-4">
                    <div class="text-center">
                        <span class="font-medium">{{ strtoupper($paymentInstructions['provider']) }}</span>
                        <p class="text-sm text-gray-600 mt-2">
                            Ikuti instruksi di aplikasi {{ ucfirst($paymentInstructions['provider']) }} untuk menyelesaikan pembayaran
                        </p>
                    </div>
                </div>
                @endif

                @if($paymentInstructions['type'] === 'qris')
                <div class="bg-white rounded-lg p-4 mb-4 text-center">
                    <p class="font-medium mb-2">Scan Kode QR</p>
                    <div class="border rounded p-2 inline-block">
                        <!-- In a real implementation, you would display the QR code here -->
                        <div class="bg-gray-200 border-2 border-dashed rounded-xl w-48 h-48 flex items-center justify-center">
                            <span class="text-gray-500">QR Code</span>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">
                        Scan kode QR di atas menggunakan aplikasi pembayaran QRIS
                    </p>
                </div>
                @endif

                @if(isset($paymentInstructions['instructions']) && !empty($paymentInstructions['instructions']))
                <ol class="list-decimal list-inside space-y-2">
                    @foreach($paymentInstructions['instructions'] as $instruction)
                    <li class="text-sm text-gray-700">{{ $instruction }}</li>
                    @endforeach
                </ol>
                @endif
            </div>
            @endif

            <!-- Actions -->
            <div class="p-6 flex flex-col sm:flex-row gap-3">
                <a href="{{ route('home') }}" 
                   class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center transition">
                    Kembali ke Beranda
                </a>
                
                @if($order->payment_status !== 'paid')
                <a href="{{ route('order.detail', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-center transition">
                    Cek Status Pembayaran
                </a>
                @else
                <a href="{{ route('order.detail', $order->order_number) }}" 
                   class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center transition">
                    Lihat Detail Pesanan
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Nomor VA berhasil disalin!');
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}
</script>