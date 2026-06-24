<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.show', $product) }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Catat Transaksi Stok: {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Product Summary --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-5 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">Produk</p>
                        <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                        <p class="text-xs text-gray-500">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 mb-0.5">Stok Saat Ini</p>
                        <p class="text-3xl font-bold {{ $product->current_stock <= $product->min_stock_threshold ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->current_stock }}
                        </p>
                        @if ($product->current_stock <= $product->min_stock_threshold)
                            <p class="text-xs text-red-500 mt-0.5">⚠️ Di bawah threshold ({{ $product->min_stock_threshold }})</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Transaction Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Detail Transaksi</h3>
                </div>
                <div class="p-6">
                    {{-- Error flash (dari InsufficientStockException) --}}
                    @if (session('error'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('products.stock-transactions.store', $product) }}" class="space-y-5">
                        @csrf

                        {{-- Tipe Transaksi --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Transaksi <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-3">
                                <label class="flex-1 flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors
                                    {{ old('type', 'in') === 'in' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <input type="radio" name="type" value="in"
                                           {{ old('type', 'in') === 'in' ? 'checked' : '' }}
                                           class="text-green-600 focus:ring-green-500">
                                    <span class="text-sm font-medium text-gray-800">📦 Stok Masuk</span>
                                </label>
                                <label class="flex-1 flex items-center gap-3 p-3 border rounded-lg cursor-pointer transition-colors
                                    {{ old('type') === 'out' ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300' }}">
                                    <input type="radio" name="type" value="out"
                                           {{ old('type') === 'out' ? 'checked' : '' }}
                                           class="text-orange-600 focus:ring-orange-500">
                                    <span class="text-sm font-medium text-gray-800">🛒 Stok Keluar</span>
                                </label>
                            </div>
                            @error('type')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jumlah --}}
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="quantity"
                                name="quantity"
                                value="{{ old('quantity') }}"
                                min="1"
                                placeholder="0"
                                class="block w-full px-3 py-2 border {{ $errors->has('quantity') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('quantity')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-1">
                                Catatan <span class="text-gray-400 font-normal">(opsional)</span>
                            </label>
                            <textarea
                                id="note"
                                name="note"
                                rows="2"
                                placeholder="Contoh: Pembelian dari supplier ABC, penjualan ke toko XYZ..."
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                            >{{ old('note') }}</textarea>
                            @error('note')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <a href="{{ route('products.show', $product) }}"
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 active:bg-indigo-700 transition-colors">
                                Catat Transaksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
