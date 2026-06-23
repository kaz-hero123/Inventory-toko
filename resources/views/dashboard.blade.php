<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Produk</p>
                    <p class="mt-1 text-3xl font-bold text-gray-900">{{ $summary['total_products'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 {{ $summary['low_stock_count'] > 0 ? 'border-l-4 border-red-500' : '' }}">
                    <p class="text-sm font-medium text-gray-500">Produk Stok Rendah</p>
                    <p class="mt-1 text-3xl font-bold {{ $summary['low_stock_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $summary['low_stock_count'] }}
                    </p>
                </div>
            </div>

            {{-- Low Stock Alert --}}
            @if ($lowStockProducts->isNotEmpty())
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-red-600 mb-4">⚠️ Produk Stok Rendah</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produk</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Stok Saat Ini</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Min. Threshold</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Proyeksi Habis</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($lowStockProducts as $product)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <a href="{{ route('products.show', $product) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                                    {{ $product->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $product->category->name ?? '-' }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    {{ $product->current_stock }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600 text-right">{{ $product->min_stock_threshold }}</td>
                                            <td class="px-4 py-3 text-sm text-right">
                                                @php $proj = $stockProjections[$product->id] ?? null; @endphp
                                                @if ($proj === null)
                                                    <span class="text-gray-400 text-xs">Data belum cukup</span>
                                                @elseif ($proj === 0)
                                                    <span class="text-red-600 font-semibold text-xs">Stok habis</span>
                                                @else
                                                    <span class="{{ $proj <= 7 ? 'text-red-600' : 'text-yellow-600' }} font-medium text-xs">
                                                        {{ $proj }} hari
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Analytics Section with Alpine.js period filter --}}
            <div
                x-data="{
                    period: 'weekly',
                    topSelling: @js($topSellingProducts),
                    slowSelling: @js($slowSellingProducts),
                    projections: @js($stockProjections),
                    loading: false,
                    async fetchAnalytics() {
                        this.loading = true;
                        try {
                            const res = await fetch(`{{ route('dashboard.analytics') }}?period=${this.period}`, {
                                headers: { 'Accept': 'application/json' }
                            });
                            const data = await res.json();
                            this.topSelling = data.topSelling;
                            this.slowSelling = data.slowSelling;
                            this.projections = data.projections;
                        } finally {
                            this.loading = false;
                        }
                    }
                }"
            >
                {{-- Period Filter --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="flex items-center gap-4">
                        <span class="text-sm font-medium text-gray-700">Filter Periode:</span>
                        <div class="flex gap-2">
                            <button
                                @click="period = 'weekly'; fetchAnalytics()"
                                :class="period === 'weekly' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                            >
                                Mingguan
                            </button>
                            <button
                                @click="period = 'monthly'; fetchAnalytics()"
                                :class="period === 'monthly' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium rounded-md transition-colors"
                            >
                                Bulanan
                            </button>
                        </div>
                        <div x-show="loading" class="text-xs text-gray-400 animate-pulse">Memuat...</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Top Selling --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">🏆 Produk Terlaris</h3>
                            <template x-if="topSelling.length === 0">
                                <p class="text-sm text-gray-500 py-4 text-center">Belum ada data penjualan pada periode ini.</p>
                            </template>
                            <ul class="divide-y divide-gray-100">
                                <template x-for="(product, idx) in topSelling" :key="product.id">
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-gray-400 w-5" x-text="idx + 1 + '.'"></span>
                                            <span class="text-sm font-medium text-gray-900" x-text="product.name"></span>
                                        </div>
                                        <span class="text-sm text-gray-600">
                                            <span x-text="product.sold_quantity"></span> terjual
                                        </span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Slow Selling --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">📉 Produk Paling Sedikit Terjual</h3>
                            <template x-if="slowSelling.length === 0">
                                <p class="text-sm text-gray-500 py-4 text-center">Belum ada data penjualan pada periode ini.</p>
                            </template>
                            <ul class="divide-y divide-gray-100">
                                <template x-for="(product, idx) in slowSelling" :key="product.id">
                                    <li class="py-3 flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs font-bold text-gray-400 w-5" x-text="idx + 1 + '.'"></span>
                                            <span class="text-sm font-medium text-gray-900" x-text="product.name"></span>
                                        </div>
                                        <span class="text-sm text-gray-600">
                                            <span x-text="product.sold_quantity"></span> terjual
                                        </span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
