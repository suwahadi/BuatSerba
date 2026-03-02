<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-gray-200 flex items-center gap-5">
            <div class="p-3 rounded-full bg-green-100">
                <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Saldo Tersedia</p>
                <p class="text-2xl font-bold text-gray-900">{{ format_rupiah(auth()->user()->wallet->balance) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 flex items-center gap-5">
            <div class="p-3 rounded-full bg-yellow-100">
                <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Saldo Terkunci</p>
                <p class="text-2xl font-bold text-gray-900">{{ format_rupiah(auth()->user()->wallet->locked_balance) }}</p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200 flex items-center gap-5">
            <div class="p-3 rounded-full bg-blue-100">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H4a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Cashback</p>
                <p class="text-2xl font-bold text-gray-900">{{ format_rupiah($this->totalCashback) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-3 mb-6">
            <div class="flex items-center gap-3">
                <label for="date_from" class="text-sm font-medium text-gray-700">Dari</label>
                <input type="date" id="date_from" name="date_from" value="{{ $date_from }}">
            </div>
            <div class="flex items-center gap-3">
                <label for="date_to" class="text-sm font-medium text-gray-700">Sampai</label>
                <input type="date" id="date_to" name="date_to" value="{{ $date_to }}">
            </div>
            <div class="flex items-center gap-3">
                <label for="status" class="text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status">
                    <option value="">Semua</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                </select>
            </div>
            <div class="flex items-center gap-3">
                <label for="type" class="text-sm font-medium text-gray-700">Type</label>
                <select id="type" name="type">
                    <option value="">Semua</option>
                    <option value="cashback">Cashback</option>
                    <option value="bonus">Bonus</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
        </div>
    </div>
</div>