<!-- Modal Notification Component -->
<div x-data="modalNotification()" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center" 
     style="display: none;" @keydown.escape="close()"
     @show-modal-error.window="show({type: 'error', title: $event.detail.title, message: $event.detail.message, errors: $event.detail.errors})"
     @show-modal-success.window="show({type: 'success', title: $event.detail.title, message: $event.detail.message})"
     @show-modal-warning.window="show({type: 'warning', title: $event.detail.title, message: $event.detail.message})"
     @show-modal-info.window="show({type: 'info', title: $event.detail.title, message: $event.detail.message})">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50" @click="close()"></div>
    
    <!-- Modal Content -->
    <div class="relative bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-6 z-10" @click.stop>
        <!-- Close Button -->
        <button @click="close()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Icon and Title -->
        <div class="flex items-start">
            <div :class="iconClasses" class="flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full">
                <svg x-show="type === 'error'" class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="type === 'success'" class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="type === 'warning'" class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <svg x-show="type === 'info'" class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4 flex-1">
                <h3 class="text-lg font-medium" :class="titleClasses" x-text="title"></h3>
            </div>
        </div>

        <!-- Message -->
        <div class="mt-4">
            <p class="text-sm text-gray-600" x-text="message"></p>
            
            <!-- Error List -->
            <template x-if="errors && errors.length > 0">
                <ul class="mt-3 space-y-2">
                    <template x-for="error in errors" :key="error">
                        <li class="text-sm text-red-600 flex items-start">
                            <span class="mr-2">•</span>
                            <span x-text="error"></span>
                        </li>
                    </template>
                </ul>
            </template>
        </div>

        <!-- Buttons -->
        <div class="mt-6 flex gap-3 justify-end">
            <button @click="close()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                Tutup
            </button>
            <template x-if="confirmCallback">
                <button @click="confirm()" :class="buttonClasses" class="px-4 py-2 text-white rounded-lg hover:opacity-90 transition-opacity font-medium">
                    OK
                </button>
            </template>
        </div>
    </div>
</div>

        <script>
        function modalNotification() {
            return {
                open: false,
                type: 'info', // error, success, warning, info
                title: '',
                message: '',
                errors: [],
                confirmCallback: null,

                show(config = {}) {
                    this.type = config.type || 'info';
                    this.title = config.title || 'Notifikasi';
                    this.message = config.message || '';
                    this.errors = Array.isArray(config.errors) ? config.errors : [];
                    this.confirmCallback = config.onConfirm || null;
                    this.open = true;
                    document.body.style.overflow = 'hidden';
                },

                close() {
                    this.open = false;
                    document.body.style.overflow = 'auto';
                },

                confirm() {
                    if (this.confirmCallback) {
                        this.confirmCallback();
                    }
                    this.close();
                },

                get iconClasses() {
                    const baseClasses = 'flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full';
                    switch (this.type) {
                        case 'error':
                            return baseClasses + ' bg-red-100';
                        case 'success':
                            return baseClasses + ' bg-green-100';
                        case 'warning':
                            return baseClasses + ' bg-yellow-100';
                        default:
                            return baseClasses + ' bg-blue-100';
                    }
                },

                get titleClasses() {
                    switch (this.type) {
                        case 'error':
                            return 'text-red-900';
                        case 'success':
                            return 'text-green-900';
                        case 'warning':
                            return 'text-yellow-900';
                        default:
                            return 'text-blue-900';
                    }
                },

                get buttonClasses() {
                    switch (this.type) {
                        case 'error':
                            return 'bg-red-600 hover:bg-red-700';
                        case 'success':
                            return 'bg-green-600 hover:bg-green-700';
                        case 'warning':
                            return 'bg-yellow-600 hover:bg-yellow-700';
                        default:
                            return 'bg-blue-600 hover:bg-blue-700';
                    }
                }
            }
        }
    </script>
