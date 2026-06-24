<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Kategori') }}
        </h2>
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

            {{-- Add Category Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Tambah Kategori Baru</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('categories.store') }}" class="flex items-start gap-3">
                        @csrf
                        <div class="flex-1">
                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Nama kategori baru..."
                                class="block w-full px-3 py-2 border {{ $errors->has('name') ? 'border-red-400 bg-red-50' : 'border-gray-300' }} rounded-md shadow-sm text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 transition-colors whitespace-nowrap">
                            + Tambah
                        </button>
                    </form>
                </div>
            </div>

            {{-- Categories List --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-base font-semibold text-gray-800">Daftar Kategori</h3>
                </div>

                @if ($categories->isEmpty())
                    <div class="p-10 text-center">
                        <p class="text-gray-400 text-sm">Belum ada kategori. Tambahkan kategori pertama di atas.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($categories as $category)
                            <div class="px-6 py-4 flex items-center gap-4" x-data="{ editing: false }">

                                {{-- Display Mode --}}
                                <div class="flex-1 min-w-0" x-show="!editing">
                                    <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $category->products_count }} produk
                                    </p>
                                </div>

                                {{-- Edit Mode --}}
                                <form method="POST" action="{{ route('categories.update', $category) }}"
                                      class="flex-1 flex items-center gap-2" x-show="editing" x-cloak>
                                    @csrf
                                    @method('PUT')
                                    <input
                                        type="text"
                                        name="name"
                                        value="{{ $category->name }}"
                                        class="flex-1 px-3 py-1.5 border border-indigo-400 rounded-md text-sm focus:outline-none focus:ring-indigo-500"
                                        @click.away="editing = false"
                                    >
                                    <button type="submit"
                                            class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-500 transition-colors">
                                        Simpan
                                    </button>
                                    <button type="button" @click="editing = false"
                                            class="px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-md hover:bg-gray-200 transition-colors">
                                        Batal
                                    </button>
                                </form>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2 shrink-0" x-show="!editing">
                                    <button @click="editing = true"
                                            class="text-xs text-indigo-600 hover:text-indigo-900 font-medium transition-colors">
                                        Edit
                                    </button>
                                    <span class="text-gray-200">|</span>
                                    <form method="POST" action="{{ route('categories.destroy', $category) }}"
                                          onsubmit="return confirm('Hapus kategori \'{{ addslashes($category->name) }}\'? Kategori yang masih memiliki produk tidak bisa dihapus.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-600 hover:text-red-900 font-medium transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>

                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
