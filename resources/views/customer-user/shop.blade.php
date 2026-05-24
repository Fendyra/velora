@extends('layouts.app')

@section('content')
<main class="bg-white mt-12">
    <div class="px-4 md:px-18 py-12 mt-12 mx-2">
        <h1 class="text-4xl font-bold text-black">All Product</h1>
    </div>

    <div class="px-4 md:px-18 mx-2 flex flex-col lg:flex-row gap-12">
        <aside class="w-full lg:w-1/5">
            <form action="{{ route('shop') }}" method="GET" id="shop-filter-form">
                <div class="mb-8 text-black">
                    <h2 class="text-2xl font-bold mb-4">Category</h2>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop', array_merge(request()->except('category'), ['page' => 1])) }}" class="hover:underline {{ !request('category') ? 'font-bold' : '' }}">All</a></li>
                        @foreach($categories as $category)
                            <li>
                                <a href="{{ route('shop', array_merge(request()->except('category', 'page'), ['category' => $category->slug])) }}" 
                                   class="hover:underline {{ request('category') == $category->slug ? 'font-bold' : '' }}">
                                    {{ $category->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="text-black">
                    <h2 class="text-2xl font-bold mb-4">Size</h2>
                    <ul class="space-y-2">
                        <li><a href="{{ route('shop', array_merge(request()->except('size'), ['page' => 1])) }}" class="hover:underline {{ !request('size') ? 'font-bold' : '' }}">All Sizes</a></li>
                        @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'] as $sizeOption)
                            <li>
                                <a href="{{ route('shop', array_merge(request()->except('size', 'page'), ['size' => $sizeOption])) }}" 
                                   class="hover:underline {{ request('size') == $sizeOption ? 'font-bold' : '' }}">
                                    {{ $sizeOption }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </form>
        </aside>

        <section class="w-full lg:w-4/5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($products as $product)
                    <div class="product-card mb-12">
                        @php
                            // Logika untuk menentukan path gambar yang benar
                            $imageUrl = '';
                            if ($product->image) {
                                // Cek apakah path dimulai dengan 'products/' (dari storage)
                                if (str_starts_with($product->image, 'products/')) {
                                    $imageUrl = asset('storage/' . $product->image);
                                } else { // Asumsi path lain adalah dari public/assets
                                    $imageUrl = asset($product->image);
                                }
                            } else {
                                $imageUrl = 'https://via.placeholder.com/300x400?text=No+Image'; // Placeholder jika tidak ada gambar
                            }
                        @endphp
                        
                        @if (Auth::check())
                            <a href="{{ route('product.show', $product->id) }}" class="block">
                                <div class="mb-4 overflow-hidden">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" class="w-auto h-auto object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                                <h3 class="text-black font-bold mb-1 text-xl text-center">{{ $product->name }}</h3>
                                <p class="text-black text-reguler text-center">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="block">
                                <div class="mb-4 overflow-hidden">
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" loading="lazy" class="w-auto h-auto object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                                <h3 class="text-black font-bold mb-1 text-xl text-center">{{ $product->name }}</h3>
                                <p class="text-black text-reguler text-center">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                                <p class="text-blue-500 text-center mt-2">
                                    Login to view details
                                </p>
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12">
                        <p class="text-xl text-gray-500">No products available matching your criteria.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        </section>
    </div>
</main>
@endsection