    <!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="mytheme">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }}</title>
        <meta name="description" content="@yield('meta_description', 'Busy Weekends e-commerce store. Browse products and checkout securely.')">
        <link rel="icon" type="image/png" href="{{ asset('/assets/images/logo.png') }}">
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <meta name="author" content="bsyweeknds" />
        <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.gstatic.com/">
        <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/daisyui@3.8.0/dist/full.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="{{ asset('assets/css/plugins/swiper.min.css') }}" type="text/css" />
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
        <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" type="text/css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer">
        <style>
            .navbar-hidden {
                transform: translateY(-100%);
                transition: transform 0.3s ease-in-out;
            }
            .navbar-visible {
                transform: translateY(0);
                transition: transform 0.3s ease-in-out;
            }
        </style>
        @stack("styles")
    </head>

    <body>
        @php
            /** @var \App\Models\User|null $authUser */
            $authUser = Auth::user();
        @endphp
        <div
            id="app-config"
            data-is-auth="{{ Auth::check() ? '1' : '0' }}"
            data-login-url="{{ route('login') }}"
            data-checkout-url="{{ route('checkout.index') }}"
            data-save-cart-url="{{ route('cart.save-for-checkout') }}"
            data-search-url="{{ route('search.products') }}"
            data-product-show-template="{{ route('product.show', ['id' => '__ID__']) }}"
            hidden
        ></div>
        <!-- Header Nav -->
        <header id="header" class="header header-fullwidth header-transparent-bg">
            <nav id="MainNavbar" class="navbar fixed top-0 left-0 right-0 z-50 transition-all duration-300 ease-in-out px-2 sm:px-5 py-1 flex flex-wrap justify-between items-center navbar-visible">
                <div class="flex items-center space-x-2 sm:space-x-4 ml-4 sm:ml-6">
                    <!-- Logo -->
                    <a href="{{ route('index') }}" class="text-lg fw-bold">
                        <img src="{{ asset('assets/images/Logo.png') }}" alt="Logo" class="logo h-8 sm:h-12"/>
                    </a>

                    <!-- Nav Menu - Visible on all screens -->
                    <ul class="flex sm:flex items-center gap-4 text-white font-semibold text-base sm:text-[17px]">
                        <li><a href="{{ route('shop') }}" class="hover:border-b-3">Shop</a></li>
                        <li><a href="{{ route('about') }}" class="hover:border-b-3">About Us</a></li>
                    </ul>
                </div>

                <!-- Mobile menu button - Only visible on small screens -->
                <div class="sm:hidden">
                    <button id="mobileMenuBtn" class="text-white">
                        <iconify-icon icon="heroicons:menu" width="24" height="24" style="color: #fff"></iconify-icon>
                    </button>
                </div>

                <!-- Icons - All aligned to same height -->
                <div class="flex items-center space-x-2 sm:space-x-4 h-fit mr-2 sm:mr-2">
                    <div class="icons flex items-center space-x-2 sm:space-x-3 h-fit    ">
                        <!-- Search Form -->
                        <div class="relative flex items-center h-fit ">
                            <button id="searchBtn" class="header-tools__item flex items-center justify-center" aria-label="Search products">
                                <iconify-icon icon="material-symbols:search-rounded" width="22" height="22" style="color: #fff"></iconify-icon>
                            </button>
                            <div id="searchForm" class="hidden absolute top-0 right-[30px] sm:right-[38px] w-40 sm:w-64 transition-all duration-300">
                                <input type="text" id="searchInput" placeholder="Search for products..." class="input input-bordered input-sm w-full text-black bg-white focus:outline-none rounded-lg shadow-md" onkeyup="searchProducts()" onfocus="showSearchResults()" onblur="hideSearchResults()">
                            </div>
                            <div id="searchResults" class="absolute top-10 right-[30px] sm:right-[38px] w-40 sm:w-64 bg-white shadow-lg rounded-lg z-50 max-h-64 overflow-y-auto space-y-2 hidden">
                            </div>
                        </div>

                        <!-- Cart Icon -->
                        <div class="relative flex items-center h-fit">
                            <button id="cartBtn" class="header-tools__item flex items-center justify-center" aria-label="View cart">
                                <iconify-icon icon="uil:cart" width="22" height="22" style="color: #fff;"></iconify-icon>
                                <span id="cartCount" class="absolute -top-2 -right-2 bg-red-600 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                            </button>
                        </div>

                        <!-- User/Auth - Fixed alignment -->
                        <div class="flex items-center h-fit">
                            @guest
                                <a href="{{ route('login') }}" class="header-tools__item flex items-center justify-center" aria-label="Login to account">
                                    <iconify-icon icon="ph:user-circle" width="22" height="22" style="color: #fff;"></iconify-icon>
                                </a>
                            @else
                            <div class="dropdown dropdown-end h-fit">
                                <label tabindex="0" class="header-tools__item flex items-center gap-2 cursor-pointer h-fit min-h-fit">
                                    <iconify-icon icon="ph:user-circle" width="22" height="22" style="color: #fff;"></iconify-icon>
                                    <span class="hidden sm:inline text-xs sm:text-sm font-semibold text-white truncate max-w-[80px] sm:max-w-full leading-none">{{ $authUser?->name }}</span>
                                </label>
                                <ul tabindex="0" class="menu dropdown-content mt-2 p-2 shadow bg-white rounded-box w-52">
                                    <li class="rounded-box text-blue-700">
                                        @if($authUser?->utype === 'ADM')
                                            <a href="{{ route('admin.dashboard') }}">Dashboard Admin</a>
                                        @elseif($authUser?->utype === 'OWN')
                                            <a href="{{ route('owner.dashboard') }}">Dashboard Owner</a>
                                        @else
                                            <a href="{{ route('customer-user.index') }}">Profile User</a>
                                        @endif
                                    </li>
                                    <li class="rounded-box text-red-500">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="w-full text-left text-red-500">Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endguest
                        </div>          
                    </div>
                </div>
            </nav>
            
            <!-- Mobile Navigation Menu - Dropdown -->
            <div id="mobileMenu" class="hidden w-full bg-primary sm:hidden">
                <ul class="flex flex-col py-2 px-4">
                    <li class="py-2"><a href="{{ route('shop') }}" class="text-white font-semibold">Shop</a></li>
                    <li class="py-2"><a href="{{ route('about') }}" class="text-white font-semibold">About Us</a></li>
                </ul>
            </div>
        </header>

        <!-- Cart Dropdown -->
        <div id="cartDropdown" class="hidden fixed top-14 right-4 bg-white shadow-lg rounded-lg w-80 z-50 p-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-bold text-lg text-black">Cart</h3>
                <button onclick="closeCartDropdown()" class="text-gray-600 hover:text-gray-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="cartItems" class="max-h-64 overflow-y-auto space-y-4">
                <!-- Cart items will be dynamically added here -->
            </div>
            <div class="border-t border-gray-200 pt-4 mt-4">
                <div class="flex justify-between mb-2">
                    <span class="font-semibold text-gray-900">Subtotal</span>
                    <span id="cartSubtotal" class="font-semibold text-gray-900">IDR 0</span>
                </div>
                <button onclick="proceedToCheckout()" class="block w-full bg-primary text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm">
                    Proceed to Checkout
                </button>
            </div>
        </div>

        <!-- Login Required Modal -->
        <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Login Required</h3>
                    <button onclick="closeLoginModal()" class="text-gray-600 hover:text-gray-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-gray-600 mb-6">Please login first to view your cart.</p>
                <a href="{{ route('login') }}" class="block w-full bg-[#010BEB] text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                    Login Now
                </a>
            </div>
        </div>

        <!-- Content Main -->
        <main>
            @yield("content")
        </main>

        <!-- Footer -->
        <footer class="text-base-content m-0 p-0" style="background-color: var(--tw-primary);">
            <div class="flex flex-col md:flex-row justify-between items-start px-4 sm:px-10 py-4 gap-6 h-auto">
                <nav class="w-full md:w-auto">
                    <h3 class="mt-6 mb-3 text-white font-bold text-lg sm:text-xl">EXPLORE</h3>
                    <ul>
                        <li><a class="footer-link" href="{{ route('shop') }}">Shop</a></li>
                        <li><a class="footer-link" href="{{ route('about') }}">About Us</a></li>
                    </ul>
                    <h3 class="mt-6 mb-3 text-white font-bold text-lg sm:text-xl">CONTACT US</h3>
                    <ul>
                        <li><a class="footer-link" href="https://wa.me/6285602683420?text=Halo,%20saya%20tertarik%20dengan%20produk%20Anda" target="_blank">+(62) 8560 2683 420</a></li>
                        <li><a class="footer-link break-words" href="mailto:admin@example.com?subject=Pertanyaan&body=Halo,%20saya%20ingin%20bertanya%20tentang%20produk%20anda">busyweeknds@gmail.com</a></li>
                        <li><a class="footer-link" href="https://www.google.com/maps?q=Wonosobo,Central+Java,Indonesia" target="_blank">Wonosobo, Central Java, Indonesia</a></li>
                    </ul>
                </nav>
                <div class="flex flex-col self-center md:self-end mt-4 md:mt-0">
                    <div class="flex gap-4">
                        <a>
                            <iconify-icon icon="mingcute:instagram-line" width="25" height="25" style="color: #fff"></iconify-icon>
                        </a>
                        <a href="https://wa.me/6285602683420?text=Halo,%20saya%20tertarik%20dengan%20produk%20Anda" target="_blank">
                            <iconify-icon icon="ic:baseline-whatsapp" width="25" height="25" style="color: #fff"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-600 text-center py-4 mt-4">
                <p class="text-sm">© Copyright 2025 © Busy Weekends Jr</p>
            </div>
        </footer>

        <div id="scrollTop" class="visually-hidden end-0"></div>

        <!-- Scripts -->
        <script src="{{ asset('assets/js/plugins/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/bootstrap-slider.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/swiper.min.js') }}"></script>
        <script src="{{ asset('assets/js/plugins/countdown.js') }}"></script>
        <script src="{{ asset('assets/js/theme.js') }}"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.min.js" integrity="sha384-RuyvpeZCxMJCqVUGFI0Do1mQrods/hhxYlcVfGPOfQtPJh0JCw12tUAZ/Mv10S7D" crossorigin="anonymous"></script>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
        <script>
            const appConfigEl = document.getElementById('app-config');
            const appConfig = appConfigEl ? appConfigEl.dataset : {};
            const IS_AUTHENTICATED = appConfig.isAuth === '1';
            const LOGIN_URL = appConfig.loginUrl || '/login';
            const CHECKOUT_URL = appConfig.checkoutUrl || '/checkout';
            const SAVE_CART_URL = appConfig.saveCartUrl || '/cart/save-for-checkout';
            const SEARCH_URL = appConfig.searchUrl || '/search';
            const PRODUCT_SHOW_TEMPLATE = appConfig.productShowTemplate || '/shop/product/__ID__';
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            // Scroll-based navbar visibility
            let lastScrollTop = 0;
            const navbar = document.getElementById('MainNavbar');
            const mobileMenu = document.getElementById('mobileMenu');

            window.addEventListener('scroll', function() {
                let currentScroll = window.pageYOffset || document.documentElement.scrollTop;

                // Only hide/show navbar if scrolled more than 50px to avoid jitter
                if (Math.abs(currentScroll - lastScrollTop) > 50) {
                    if (currentScroll > lastScrollTop && currentScroll > 50) {
                        // Scroll down
                        navbar.classList.remove('navbar-visible');
                        navbar.classList.add('navbar-hidden');
                        mobileMenu.classList.add('hidden'); // Hide mobile menu when scrolling down
                    } else if (currentScroll < lastScrollTop) {
                        // Scroll up
                        navbar.classList.remove('navbar-hidden');
                        navbar.classList.add('navbar-visible');
                    }
                    lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Prevent negative scroll
                }
            });

            // Mobile menu toggle
            document.addEventListener('DOMContentLoaded', function () {
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                const mobileMenu = document.getElementById('mobileMenu');

                if (mobileMenuBtn && mobileMenu) {
                    mobileMenuBtn.addEventListener('click', function() {
                        mobileMenu.classList.toggle('hidden');
                        // Ensure navbar stays visible when mobile menu is toggled
                        navbar.classList.remove('navbar-hidden');
                        navbar.classList.add('navbar-visible');
                    });
                }

                // Search Form Toggle
                const searchBtn = document.getElementById('searchBtn');
                const searchForm = document.getElementById('searchForm');
                const searchInput = document.getElementById('searchInput');

                searchBtn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    searchForm.classList.toggle('hidden');
                    if (!searchForm.classList.contains('hidden')) {
                        searchInput.focus();
                        // Ensure navbar stays visible when search is active
                        navbar.classList.remove('navbar-hidden');
                        navbar.classList.add('navbar-visible');
                    } else {
                        const searchResults = document.getElementById('searchResults');
                        searchResults.classList.add('hidden');
                    }
                });

                document.addEventListener('click', function(event) {
                    if (!searchBtn.contains(event.target) && !searchForm.contains(event.target) && !document.getElementById('searchResults').contains(event.target)) {
                        searchForm.classList.add('hidden');
                        const searchResults = document.getElementById('searchResults');
                        searchResults.classList.add('hidden');
                    }
                });

                document.getElementById('searchResults').addEventListener('click', function(event) {
                    event.stopPropagation();
                });

                window.showSearchResults = function() {
                    const searchResults = document.getElementById('searchResults');
                    searchResults.classList.remove('hidden');
                };

                window.hideSearchResults = function() {
                    const searchResults = document.getElementById('searchResults');
                    setTimeout(() => {
                        if (!searchResults.matches(':hover') && !document.getElementById('searchInput').matches(':focus')) {
                            searchResults.classList.add('hidden');
                        }
                    }, 200);
                };

                // Cart Dropdown
                const cartBtn = document.getElementById('cartBtn');
                const cartDropdown = document.getElementById('cartDropdown');
                let cart = JSON.parse(localStorage.getItem('cart')) || [];

                cartBtn.addEventListener('click', function(event) {
                    event.stopPropagation();
                    if (!IS_AUTHENTICATED) {
                        showLoginModal();
                        return;
                    }

                    updateCartUI();
                    cartDropdown.classList.toggle('hidden');
                    // Ensure navbar stays visible when cart is active
                    navbar.classList.remove('navbar-hidden');
                    navbar.classList.add('navbar-visible');
                });

                document.addEventListener('click', function(event) {
                    if (!cartBtn.contains(event.target) && !cartDropdown.contains(event.target)) {
                        cartDropdown.classList.add('hidden');
                    }
                });

                window.closeCartDropdown = function() {
                    cartDropdown.classList.add('hidden');
                };

                window.showLoginModal = function() {
                    const loginModal = document.getElementById('loginModal');
                    loginModal.classList.remove('hidden');
                };

                window.closeLoginModal = function() {
                    const loginModal = document.getElementById('loginModal');
                    loginModal.classList.add('hidden');
                };

                window.proceedToCheckout = function() {
                    if (!IS_AUTHENTICATED) {
                        showLoginModal();
                        return;
                    }

                    if (cart.length === 0) {
                        alert('Your cart is empty.');
                        return;
                    }

                    fetch(SAVE_CART_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF_TOKEN
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
                };

                function updateCartUI() {
                    const cartItemsContainer = document.getElementById('cartItems');
                    const cartCount = document.getElementById('cartCount');
                    const cartSubtotal = document.getElementById('cartSubtotal');
                    cartItemsContainer.innerHTML = '';

                    if (!IS_AUTHENTICATED) {
                        cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center">Please login to view your cart.</p>';
                        cartCount.classList.add('hidden');
                        cartSubtotal.textContent = 'IDR 0';
                        return;
                    }

                    if (cart.length === 0) {
                        cartItemsContainer.innerHTML = '<p class="text-gray-500 text-center">Your cart is empty.</p>';
                        cartCount.classList.add('hidden');
                        cartSubtotal.textContent = 'IDR 0';
                        return;
                    }

                    let subtotal = 0;
                    cartCount.classList.remove('hidden');
                    cartCount.textContent = cart.reduce((sum, item) => sum + item.quantity, 0);

                    cart.forEach((item, index) => {
                        const itemTotal = item.price * item.quantity;
                        subtotal += itemTotal;

                        const itemElement = `
                            <div class="flex items-start space-x-4 pb-4">
                                <img src="${item.image}" alt="${item.name}" class="w-16 h-16 object-cover rounded-lg border-2 border-primary">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900">${item.name}</h3>
                                    <p class="text-xs text-gray-600 mb-1">IDR ${item.price.toLocaleString('id-ID')}</p>
                                    <p class="text-xs text-gray-600 mb-1">Size: <span class="bg-primary text-white px-1 py-0.5 rounded">${item.size}</span></p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <button onclick="decreaseQuantity(${index}, event)" class="text-white bg-gray-500 hover:bg-gray-600 rounded-full w-6 h-6 flex items-center justify-center">-</button>
                                        <span class="text-sm">${item.quantity}</span>
                                        <button onclick="increaseQuantity(${index}, event)" class="text-white bg-gray-500 hover:bg-gray-600 rounded-full w-6 h-6 flex items-center justify-center">+</button>
                                    </div>
                                </div>
                                <button onclick="removeCartItem(${index})" class="text-red-600 hover:text-red-800">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        `;
                        cartItemsContainer.innerHTML += itemElement;
                    });

                    cartSubtotal.textContent = `IDR ${subtotal.toLocaleString('id-ID')}`;
                }

                window.removeCartItem = function(index) {
                    cart.splice(index, 1);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartUI();
                };

                window.increaseQuantity = function(index, event) {
                    event.stopPropagation();
                    cart[index].quantity += 1;
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartUI();
                };

                window.decreaseQuantity = function(index, event) {
                    event.stopPropagation();
                    if (cart[index].quantity > 1) {
                        cart[index].quantity -= 1;
                    } else {
                        removeCartItem(index);
                        return;
                    }
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartUI();
                };

                window.searchProducts = function() {
                    const query = document.getElementById('searchInput').value;
                    const searchResults = document.getElementById('searchResults');

                    if (query.length < 2) {
                        searchResults.innerHTML = '<p class="text-gray-500 text-sm p-2">Enter at least 2 characters to search.</p>';
                        return;
                    }

                    fetch(SEARCH_URL + '?q=' + encodeURIComponent(query), {
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';

                        if (data.length === 0) {
                            searchResults.innerHTML = '<p class="text-gray-500 text-sm p-2">No products found.</p>';
                            return;
                        }

                        data.forEach(product => {
                            const productUrl = PRODUCT_SHOW_TEMPLATE.replace('__ID__', encodeURIComponent(product.id));
                            const productElement = `
                                <a href="${productUrl}" class="flex items-center space-x-3 p-2 hover:bg-gray-100 rounded-lg">
                                    <img src="${product.image}" alt="${product.name}" class="w-10 h-10 object-cover rounded-lg">
                                    <div>
                                        <h4 class="text-sm font-semibold text-gray-900">${product.name}</h4>
                                        <p class="text-xs text-gray-600">IDR ${product.price.toLocaleString('id-ID')}</p>
                                    </div>
                                </a>
                            `;
                            searchResults.innerHTML += productElement;
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchResults.innerHTML = '<p class="text-red-500 text-sm p-2">An error occurred while searching.</p>';
                    });
                };

                updateCartUI();
            });
        </script>
        @stack("scripts")
    </body>
    </html>