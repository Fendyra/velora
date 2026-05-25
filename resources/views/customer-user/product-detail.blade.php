@extends('layouts.app')

@section('content')
<main class=" mt-[110px] container mx-auto px-4 md:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 mb-16">
        <div class="flex flex-col">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-3">{{ $product->name }}</h1>

            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Description</h2>
                <p class="text-gray-600 leading-relaxed text-justify max-w-[250px] md:max-w-[350px] break-words">{{ $product->description ?? 'No description available.' }}</p>
            </div>
            <div class="flex justify-between items-center mb-3 mr-[30px]">
                <h2 class="text-xl font-semibold text-gray-900">Size Guide</h2>
                <button id="size-guide-toggle" class="text-sm text-blue-600 hover:underline">Size Guide</button>
            </div>
            <div id="size-guide" class="text-sm text-gray-500 mt-1 hidden">
                <ul class="list-disc list-inside">
                    <li>XS: W 46 cm, L 68 cm</li>
                    <li>S: W 48 cm, L 70 cm</li>
                    <li>M: W 50 cm, L 72 cm</li>
                    <li>L: W 52 cm, L 74 cm</li>
                    <li>XL: W 54 cm, L 76 cm</li>
                    <li>XXL: W 56 cm, L 78 cm</li>
                    <li>One Size</li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col items-center gap-6">
            {{-- Display Front Image --}}
            @php
                $imageFrontUrl = '';
                if ($product->image_front) {
                    if (str_starts_with($product->image_front, 'products/')) {
                        $imageFrontUrl = asset('storage/' . $product->image_front);
                    } else {
                        $imageFrontUrl = asset($product->image_front);
                    }
                } else {
                    $imageFrontUrl = 'https://via.placeholder.com/500x500?text=No+Front+Image';
                }
            @endphp
            {{-- Front Image --}}
            <div class="w-full max-w-[340px] h-full aspect-square bg-white rounded-2xl">
                <img src="{{ $imageFrontUrl }}"
                alt="{{ $product->name }} Front View"
                loading="eager" fetchpriority="high" decoding="async"
                class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-300 ease-in-out">
           </div>

            {{-- Display Back Image --}}
            @php
                $imageBackUrl = '';
                if ($product->image_back) {
                    if (str_starts_with($product->image_back, 'products/')) {
                        $imageBackUrl = asset('storage/' . $product->image_back);
                    } else {
                        $imageBackUrl = asset($product->image_back);
                    }
                } else {
                    $imageBackUrl = 'https://via.placeholder.com/500x500?text=No+Back+Image';
                }
            @endphp
            <div class="w-full max-w-[340px] h-full aspect-square bg-white rounded-2xl">
                <img src="{{ $imageBackUrl }}"
                alt="{{ $product->name }} Back View"
                loading="lazy"
                class="w-full h-full object-cover object-center transform hover:scale-105 transition-transform duration-300 ease-in-out">
            </div>
        </div>

        <div class="flex flex-col justify-between">
            <div>
                <h1 class="text-2xl font-bold md:text-3xl text-gray-800 mb-6">IDR {{ number_format($product->price, 0, ',', '.') }}</h1>

                <p class="text-sm text-gray-600 mb-4">
                    @if($product->stock > 0)
                        <span class="text-green-600 font-medium">In Stock ({{ $product->stock }} left)</span>
                    @else
                        <span class="text-red-600 font-medium">Out of Stock</span>
                    @endif
                </p>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h2 class="text-xl font-semibold text-gray-900">Size</h2>
                    </div>
                    @if(!empty($product->sizes))
                        <div class="flex flex-wrap gap-2">
                            @foreach($product->sizes as $availableSize)
                                <button type="button" class="size-btn border border-gray-300 text-gray-800 rounded-md px-4 py-2 text-sm hover:bg-blue-600 transition-colors" data-size="{{ $availableSize }}" onclick="selectSize(this)">
                                    {{ $availableSize }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No size information available.</p>
                    @endif
                </div>

                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Amount</h2>
                    <div class="flex items-center space-x-3">
                        <button type="button" onclick="updateAmount(-1)" class="border border-gray-300 rounded-md px-3 py-1 text-gray-600 hover:bg-gray-100">-</button>
                        <input type="number" id="amount" min="1" max="{{ $product->stock }}" value="1" class="border border-gray-300 rounded-md px-2 py-2 w-20 text-center focus:outline-none focus:ring-2 focus:ring-blue-500" style="background-color: white;" readonly>
                        <button type="button" onclick="updateAmount(1)" class="border border-gray-300 rounded-md px-3 py-1 text-gray-600 hover:bg-gray-100">+</button>
                    </div>
                </div>

                <button id="add-to-cart" class="w-full bg-[#010BEB] text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-lg {{ $product->stock <= 0 ? 'opacity-50 cursor-not-allowed' : '' }}" {{ $product->stock <= 0 ? 'disabled' : '' }}>
                    Add to Cart
                </button>
            </div>
        </div>
    </div>

    @if($relatedProducts->isNotEmpty())
        <div class="mt-[80px] mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8">You Might Also Like</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-12 gap-x-8">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="flex flex-col items-center bg-white p-4 rounded-lg shadow-sm transition-all duration-300 transform hover:scale-105 hover:shadow-lg h-full">
                        <a href="{{ route('product.show', $relatedProduct->id) }}" class="flex flex-col items-center w-full h-full">
                            @php
                                $relatedImageUrl = '';
                                // Prefer image_front if available, otherwise fallback to 'image'
                                $imageSource = $relatedProduct->image_front ?? $relatedProduct->image;

                                if ($imageSource) {
                                    if (str_starts_with($imageSource, 'products/')) {
                                        $relatedImageUrl = asset('storage/' . $imageSource);
                                    } else {
                                        $relatedImageUrl = asset($imageSource);
                                    }
                                } else {
                                    $relatedImageUrl = 'https://via.placeholder.com/300x400?text=No+Image';
                                }
                            @endphp
                            <div class="w-full h-72 overflow-hidden mb-4 rounded-lg">
                                <img src="{{ $relatedImageUrl }}" alt="{{ $relatedProduct->name }}" loading="lazy" class="w-full h-full object-cover object-center">
                            </div>
                            <div class="flex-grow text-center w-full">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1 leading-tight">{{ $relatedProduct->name }}</h3>
                                <p class="text-gray-600">IDR {{ number_format($relatedProduct->price, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Cart Popup (keep as is) --}}
    <div id="cart-popup" class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-2xl transform translate-x-full transition-transform duration-300 z-50">
        <div class="flex flex-col h-full">
            <div class="flex justify-between items-center p-6 mt-[50px]">
                <h2 class="underline underline-offset-4 text-2xl font-bold text-gray-900">Cart</h2>
                <button id="close-cart" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div id="cart-items" class="flex-1 overflow-y-auto p-6 space-y-4">
            </div>

            <div class="p-6">
                <div class="flex justify-between items-center mb-4 border-t border-gray-600">
                    <span class="text-lg font-semibold text-gray-900">SUB TOTAL</span>
                    <span id="cart-subtotal" class="text-lg font-semibold text-gray-900">IDR 0</span>
                </div>
                <button id="proceed-to-checkout" class="block w-full bg-[#010BEB] text-white text-center px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>

    <div id="cart-overlay" class="fixed inset-0 bg-black opacity-0 pointer-events-none transition-opacity duration-300 z-40"></div>

    <div
        id="product-page-config"
        data-is-auth="{{ Auth::check() ? '1' : '0' }}"
        data-login-url="{{ route('login') }}"
        data-checkout-url="{{ route('checkout.index') }}"
        data-save-cart-url="{{ route('cart.save-for-checkout') }}"
        data-asset-base-url="{{ asset('/') }}"
        data-storage-base-url="{{ asset('storage/') }}"
        data-product-id="{{ $product->id }}"
        data-product-name="{{ $product->name }}"
        data-product-price="{{ $product->price }}"
        data-product-image="{{ $product->image_front ?? $product->image }}"
        data-product-stock="{{ $product->stock }}"
        hidden
    ></div>
</main>
@endsection

@push('scripts')
<script>
// =====================================================
// Product Detail Page Script
// Catatan: IS_AUTHENTICATED, LOGIN_URL, CHECKOUT_URL,
// SAVE_CART_URL, CSRF_TOKEN sudah dideklarasikan di
// app.blade.php - JANGAN redeclare di sini!
// =====================================================

const pageConfigEl = document.getElementById('product-page-config');
const pageConfig = pageConfigEl ? pageConfigEl.dataset : {};

// Ambil dari pageConfig khusus halaman produk
const ASSET_BASE_URL = pageConfig.assetBaseUrl || '/';
const STORAGE_BASE_URL = pageConfig.storageBaseUrl || '/storage/';
const PRODUCT_STOCK = parseInt(pageConfig.productStock || '0', 10);

// Override IS_AUTHENTICATED dari pageConfig halaman ini jika perlu
// (sudah tersedia dari app.blade.php sebagai IS_AUTHENTICATED)

document.getElementById('proceed-to-checkout').addEventListener('click', function() {
    if (!IS_AUTHENTICATED) {
        alert('Please login to proceed to checkout.');
        window.location.href = LOGIN_URL;
        return;
    }

    if (cart.length > 0) {
        fetch(SAVE_CART_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF_TOKEN,
            },
            body: JSON.stringify({ cart: cart })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = CHECKOUT_URL;
            } else {
                alert('Failed to save cart. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    } else {
        alert('Your cart is empty.');
    }
});

let selectedSize = null;
let cart = JSON.parse(localStorage.getItem('cart')) || [];

document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    // Select the first size button by default if available
    const firstSizeBtn = document.querySelector('.size-btn');
    if (firstSizeBtn) {
        selectSize(firstSizeBtn);
    }
});

function selectSize(button) {
    selectedSize = button.getAttribute('data-size');
    const sizeButtons = document.querySelectorAll('.size-btn');
    sizeButtons.forEach(btn => {
        // Reset all buttons to unselected state
        btn.style.backgroundColor = '';
        btn.style.color = '';
        btn.style.borderColor = '';
        btn.classList.remove('selected-size');
    });
    // Highlight selected button
    button.style.backgroundColor = '#010BEB';
    button.style.color = '#ffffff';
    button.style.borderColor = '#010BEB';
    button.classList.add('selected-size');
}

function updateAmount(change) {
    const amountInput = document.getElementById('amount');
    let amount = parseInt(amountInput.value);
    amount += change;
    if (amount < 1) amount = 1; // Minimum quantity is 1
    if (amount > PRODUCT_STOCK) amount = PRODUCT_STOCK;
    amountInput.value = amount;
}

document.getElementById('size-guide-toggle').addEventListener('click', function() {
    const sizeGuide = document.getElementById('size-guide');
    sizeGuide.classList.toggle('hidden');
});

// Helper function to get correct image URL for JavaScript
function getProductImageUrl(imagePath) {
    if (!imagePath) {
        return 'https://via.placeholder.com/150';
    }
    // Already a full URL
    if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) {
        return imagePath;
    }
    // Path from storage (uploaded images)
    if (imagePath.startsWith('products/')) {
        return STORAGE_BASE_URL.replace(/\/$/, '') + '/' + imagePath;
    }
    // Path from public/assets (seeded images with leading slash)
    if (imagePath.startsWith('/')) {
        return window.location.origin + imagePath;
    }
    // Fallback
    return ASSET_BASE_URL.replace(/\/$/, '') + '/' + imagePath;
}

// Deklarasi PRODUCT_BASE setelah getProductImageUrl tersedia
const PRODUCT_BASE = {
    id: parseInt(pageConfig.productId || '0', 10),
    name: pageConfig.productName || '',
    price: Number(pageConfig.productPrice || 0),
    image: getProductImageUrl(pageConfig.productImage || ''),
};

document.getElementById('add-to-cart').addEventListener('click', function() {
    if (!IS_AUTHENTICATED) {
        alert('Please login to add items to your cart.');
        window.location.href = LOGIN_URL;
        return;
    }

    if (this.disabled) return;

    const amount = parseInt(document.getElementById('amount').value);
    if (!selectedSize) {
        alert('Please select a size.');
        return;
    }
    if (amount <= 0 || amount > PRODUCT_STOCK) {
        alert(`Please enter a valid amount (up to ${PRODUCT_STOCK}).`);
        return;
    }

    const product = {
        ...PRODUCT_BASE,
        size: selectedSize,
        quantity: amount,
    };

    const existingItemIndex = cart.findIndex(item => item.id === product.id && item.size === product.size);
    if (existingItemIndex !== -1) {
        cart[existingItemIndex].quantity += product.quantity;
    } else {
        cart.push(product);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showCartPopup();
});

function updateCartUI() {
    const cartItemsContainer = document.getElementById('cart-items');
    cartItemsContainer.innerHTML = ''; // Clear existing items

    if (!IS_AUTHENTICATED) {
        cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center">Please login to view your cart.</p>';
        document.getElementById('cart-subtotal').textContent = 'IDR 0';
        return;
    }

    if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center">Your cart is empty.</p>';
        document.getElementById('cart-subtotal').textContent = 'IDR 0';
        return;
    }

    let subtotal = 0;
    cart.forEach((item, index) => {
        const itemTotal = item.price * item.quantity;
        subtotal += itemTotal;

        // Use the helper function for image URL
        const cartItemImageUrl = getProductImageUrl(item.image);

        const itemElement =
            `<div class="flex items-start space-x-4 border-b pb-4">
                <img src="${cartItemImageUrl}" alt="${item.name}" class="w-20 h-20 object-cover rounded-lg border-2 border-blue-600">
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900">${item.name}</h3>
                    <p class="text-sm text-gray-600 mb-2">IDR ${item.price.toLocaleString('id-ID')}</p>
                    <p class="text-sm text-gray-600 mb-2">Size: <span class="bg-blue-600 text-white px-2 py-1 rounded mb-2">${item.size}</span></p>
                    <div class="flex items-center space-x-2 mt-2">
                        <button onclick="updateCartItem(${index}, -1)" class="border border-gray-300 rounded-md px-2 py-1 text-gray-600 hover:bg-gray-100">-</button>
                        <span class="text-gray-800">${item.quantity}</span>
                        <button onclick="updateCartItem(${index}, 1)" class="border border-gray-300 rounded-md px-2 py-1 text-gray-600 hover:bg-gray-100">+</button>
                    </div>
                </div>
                <button onclick="removeCartItem(${index})" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>`;
        cartItemsContainer.innerHTML += itemElement;
    });

    document.getElementById('cart-subtotal').textContent = `IDR ${subtotal.toLocaleString('id-ID')}`;
}

function updateCartItem(index, change) {
    const productMaxStock = PRODUCT_STOCK;
    const currentQuantity = cart[index].quantity;

    if (change > 0 && (currentQuantity + change) > productMaxStock) {
        alert('Cannot add more than available stock.');
        return;
    }

    cart[index].quantity += change;
    if (cart[index].quantity <= 0) {
        // If quantity drops to 0 or below, remove the item
        cart.splice(index, 1);
    }
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI(); // Re-render the cart UI
}

function removeCartItem(index) {
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    // Also sync with global cart if exists
    if (typeof window.removeCartItem === 'function' && window.removeCartItem !== removeCartItem) {
        localStorage.setItem('cart', JSON.stringify(cart));
    }
}

function showCartPopup() {
    const cartPopup = document.getElementById('cart-popup');
    const overlay = document.getElementById('cart-overlay');
    cartPopup.classList.remove('translate-x-full');
    overlay.classList.remove('opacity-0', 'pointer-events-none');
    overlay.classList.add('opacity-50', 'pointer-events-auto');
}

function hideCartPopup() {
    const cartPopup = document.getElementById('cart-popup');
    const overlay = document.getElementById('cart-overlay');
    cartPopup.classList.add('translate-x-full');
    overlay.classList.add('opacity-0', 'pointer-events-none');
    overlay.classList.remove('opacity-50', 'pointer-events-auto');
}

document.getElementById('close-cart').addEventListener('click', hideCartPopup);
document.getElementById('cart-overlay').addEventListener('click', hideCartPopup);
</script>
@endpush