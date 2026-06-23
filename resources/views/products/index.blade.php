<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daftar Produk') }}
            </h2>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 transition ease-in-out duration-150">
                + Tambah Produk
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($products->isEmpty())
                    <div class="p-12 text-center">
                        <p class="text-gray-500 text-lg mb-2">Belum ada produk.</p>
                        <p class="text-gray-400 text-sm mb-6">Mulai tambahkan produk pertama Anda.</p>
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                            + Tambah Produk Pertama
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Jual</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($products as $product)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <a href="{{ route('products.show', $product) }}" class="font-medium text-indigo-600 hover:text-indigo-900">
                                                {{ $product->name }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($product->purchase_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 text-right">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 text-sm text-right">
                                            <span class="font-semibold {{ $product->current_stock <= $product->min_stock_threshold ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ $product->current_stock }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($product->current_stock <= $product->min_stock_threshold)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Stok Rendah</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Normal</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium space-x-2 whitespace-nowrap">
                                            <a href="{{ route('products.show', $product) }}" class="text-gray-600 hover:text-gray-900">Lihat</a>
                                            <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            <form method="POST" action="{{ route('products.destroy', $product) }}" class="inline-block"
                                                  onsubmit="return confirm('Hapus produk ini? Data tidak akan hilang permanen.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if ($products->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
