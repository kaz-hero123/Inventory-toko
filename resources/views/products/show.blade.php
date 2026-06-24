<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('products.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $product->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Product Detail Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                            <p class="text-sm text-gray-500 mt-0.5">{{ $product->category->name ?? 'Tanpa Kategori' }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($product->current_stock <= $product->min_stock_threshold)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    ⚠️ Stok Rendah
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    ✓ Stok Normal
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 py-4 border-t border-b border-gray-100">
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Stok Saat Ini</p>
                            <p class="text-2xl font-bold {{ $product->current_stock <= $product->min_stock_threshold ? 'text-red-600' : 'text-gray-900' }}">
                                {{ $product->current_stock }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Min. Threshold</p>
                            <p class="text-2xl font-bold text-gray-700">{{ $product->min_stock_threshold }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Harga Beli</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-0.5">Harga Jual</p>
                            <p class="text-lg font-semibold text-gray-900">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    {{-- Stock Projection --}}
                    <div class="mt-4">
                        <p class="text-xs text-gray-500 mb-1">Proyeksi Stok Habis</p>
                        @if ($stockProjection === null)
                            <p class="text-sm text-gray-400 italic">Data belum cukup untuk proyeksi (butuh minimal 30 hari data penjualan).</p>
                        @elseif ($stockProjection === 0)
                            <p class="text-sm font-semibold text-red-600">Stok sudah habis atau di bawah threshold.</p>
                        @else
                            <p class="text-sm font-semibold {{ $stockProjection <= 7 ? 'text-red-600' : ($stockProjection <= 14 ? 'text-yellow-600' : 'text-green-600') }}">
                                ≈ {{ $stockProjection }} hari lagi
                            </p>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center gap-3 mt-5 pt-4 border-t border-gray-100">
                        <a href="{{ route('products.stock-transactions.create', $product) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 transition-colors">
                            + Catat Transaksi Stok
                        </a>
                        <a href="{{ route('products.edit', $product) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-50 transition-colors">
                            Edit Produk
                        </a>
                        <form method="POST" action="{{ route('products.destroy', $product) }}" class="ml-auto"
                              onsubmit="return confirm('Hapus produk ini? Data riwayat stok akan tetap tersimpan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 transition-colors">
                                Hapus Produk
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Stock History --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-gray-800">Riwayat Transaksi Stok</h3>
                </div>
                @if ($stockHistory->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-gray-400 text-sm">Belum ada transaksi stok untuk produk ini.</p>
                        <a href="{{ route('products.stock-transactions.create', $product) }}"
                           class="mt-3 inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-500 transition-colors">
                            + Catat Transaksi Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($stockHistory as $transaction)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-3 text-sm text-gray-600 whitespace-nowrap">
                                            {{ $transaction->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-center">
                                            @if ($transaction->type === 'in')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Masuk
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                    Keluar
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3 text-right text-sm font-semibold {{ $transaction->type === 'in' ? 'text-green-700' : 'text-orange-700' }}">
                                            {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                                        </td>
                                        <td class="px-6 py-3 text-sm text-gray-500">
                                            {{ $transaction->note ?? '-' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if ($stockHistory->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $stockHistory->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
