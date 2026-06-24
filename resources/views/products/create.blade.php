<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tambah Produk Baru') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('products.store') }}" class="space-y-5">
                        @csrf

                        {{-- Nama Produk --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Nama Produk <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Contoh: Mie Instan Goreng"
                                class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="category_id"
                                name="category_id"
                                class="block w-full px-3 py-2 border {{ $errors->has('category_id') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="">-- Pilih Kategori --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Harga --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Beli (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="purchase_price"
                                    name="purchase_price"
                                    value="{{ old('purchase_price') }}"
                                    min="0"
                                    placeholder="0"
                                    class="block w-full px-3 py-2 border {{ $errors->has('purchase_price') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                @error('purchase_price')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-1">
                                    Harga Jual (Rp) <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="number"
                                    id="selling_price"
                                    name="selling_price"
                                    value="{{ old('selling_price') }}"
                                    min="0"
                                    placeholder="0"
                                    class="block w-full px-3 py-2 border {{ $errors->has('selling_price') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                >
                                @error('selling_price')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Min Stock Threshold --}}
                        <div>
                            <label for="min_stock_threshold" class="block text-sm font-medium text-gray-700 mb-1">
                                Batas Minimum Stok <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="min_stock_threshold"
                                name="min_stock_threshold"
                                value="{{ old('min_stock_threshold', 5) }}"
                                min="0"
                                class="block w-full px-3 py-2 border {{ $errors->has('min_stock_threshold') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            <p class="mt-1 text-xs text-gray-400">Produk akan ditandai "Stok Rendah" jika stok ≤ nilai ini.</p>
                            @error('min_stock_threshold')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                            <a href="{{ route('products.index') }}"
                               class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors">
                                Batal
                            </a>
                            <button type="submit"
                                    class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 active:bg-indigo-700 transition-colors">
                                Simpan Produk
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
