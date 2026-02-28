<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BuatSerba' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        .floating-label-group { position: relative; margin-bottom: 0.5rem; }
        .floating-label-group input {
            width: 100%; padding: 12px; border: 1px solid #e5e7eb;
            border-radius: 0.5rem; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 1rem; background-color: transparent;
        }
        .floating-label-group input:focus { border-color: #16a34a; box-shadow: 0 0 0 1px #16a34a; }
        .floating-label-group label {
            position: absolute; top: -0.6rem; left: 10px;
            background-color: #fff; padding: 0 4px;
            font-size: 0.75rem; color: #9CA3AF;
            pointer-events: none; transition: 0.2s; z-index: 10;
        }
        .floating-label-group input:focus ~ label { color: #16a34a; }
    </style>
</head>
<body>
    <div class="bg-gray-50 min-h-screen">
        <!-- Navigation -->
        <x-navbar />
        
        <!-- Main Content -->
        <div class="py-32 px-4">
            <div class="max-w-md mx-auto">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <!-- Footer -->
        <x-footer />

        <!-- Notification -->
        <div x-data="{ show: false, message: '', type: 'success' }"
             x-on:notify.window="message = $event.detail.message; type = $event.detail.type; show = true; setTimeout(() => show = false, 5000)"
             x-show="show" x-transition.opacity
             class="fixed top-20 right-4 z-50 max-w-md">
            <div :class="{
                'bg-green-100 border-green-500 text-green-900': type === 'success',
                'bg-red-100 border-red-500 text-red-900': type === 'error'
            }" class="border-l-4 p-4 rounded shadow-lg">
                <p class="font-medium" x-text="message"></p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
