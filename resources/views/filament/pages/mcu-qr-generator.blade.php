<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- BAGIAN KIRI: Form Input (2/3 Grid) -->
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit.prevent="downloadQrCode">
                {{ $this->form }}
            </form>
        </div>

        <!-- BAGIAN KANAN: Preview & Download Panel (1/3 Grid) -->
        <div class="lg:col-span-1">
            <div class="sticky top-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col items-center">
                
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-100 mb-4 text-center">
                    Preview & Export QR
                </h3>

                <!-- Tampilan QR Code Dummy / Live Preview -->
                <div class="w-full bg-gray-50 dark:bg-gray-900 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg flex items-center justify-center p-4 mb-6" style="min-height: 250px;">
                    @if($generatedUrl)
                        <!-- Render QR Base64 untuk Preview -->
                        {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($generatedUrl) !!}
                    @else
                        <span class="text-gray-400 text-sm text-center">
                            Masukkan Nama & No MCU untuk memuat preview
                        </span>
                    @endif
                </div>

                <!-- Opsi Format & Size -->
                <div class="w-full flex space-x-2 mb-6">
                    <div class="w-1/2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Format</label>
                        <select wire:model.live="qr_format" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="png">PNG</option>
                            <option value="svg">SVG</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Size</label>
                        <select wire:model.live="qr_size" class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-lg shadow-sm text-sm focus:ring-primary-500 focus:border-primary-500">
                            <option value="500">500x500 px</option>
                            <option value="1000">1000x1000 px</option>
                            <option value="2000">2000x2000 px</option>
                        </select>
                    </div>
                </div>

                <!-- Tombol Download -->
                <button 
                    wire:click="downloadQrCode"
                    @if(!$generatedUrl) disabled @endif
                    class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 transition-colors">
                    <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2" />
                    DOWNLOAD QR CODE
                </button>
                
                <p class="text-xs text-center text-gray-500 dark:text-gray-400 mt-4">
                    Tautan QR terenkripsi dengan aman.
                </p>
            </div>
        </div>

    </div>
</x-filament-panels::page>
