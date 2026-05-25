<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Busyweeknds') }} Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('/assets/images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        'brand': {
                            '50': '#f9f5ff', '100': '#ece6fb', '200': '#d8c9f6', '300': '#c0a8f0',
                            '400': '#a885ea', '500': '#8f63e3', '600': '#7a4ede', '700': '#653fd6',
                            '800': '#4e32b8', '900': '#3a2592', '950': '#2d1c6e',
                        },
                        'slate': {
                            '50': '#f8fafc', '100': '#f1f5f9', '200': '#e2e8f0', '300': '#cbd5e1',
                            '400': '#94a3b8', '500': '#64748b', '600': '#475569', '700': '#334155',
                            '800': '#1e293b', '900': '#0f172a', '950': '#020617'
                        }
                    }
                }
            }
        }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Poppins', sans-serif; }
        [x-cloak] { display: none !important; }
        #delete-modal { display: none; }
        #delete-modal.modal-open { display: flex; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased w-full h-screen">
    <div x-data="{ sidebarOpen: window.innerWidth > 1024 }" 
        @resize.window="sidebarOpen = window.innerWidth > 1024" 
        class="flex h-screen">

        <!-- Sidebar -->
        <aside 
            x-show="sidebarOpen" 
            x-cloak
            @click.outside="if(window.innerWidth < 1024) sidebarOpen = false"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed lg:static z-40 w-64 h-full bg-[#010BEB] from-brand-900 to-brand-800 text-white shadow-2xl transition-all duration-300 ease-in-out">
            <div class="flex flex-start p-4 left-0 border-b border-brand-700">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="h-30 w-auto">
                    <span class="text-3xl font-bold text-white transition-opacity duration-300">Admin</span>
                </a>
            </div>
            <nav class="flex-1 space-y-2 px-4 py-6">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ Route::is('admin.dashboard') ? 'bg-[#5DA9FF] text-white' : 'hover:bg-[#5DA9FF]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M13 9V3h8v6zM3 13V3h8v10zm10 8V11h8v10zM3 21v-6h8v6zm2-10h4V5H5zm10 8h4v-6h-4zm0-12h4V5h-4zM5 19h4v-2H5zm4-2"></path>
                </svg>                    
                <span class="text-lg transition-opacity duration-300">Dashboard</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ Route::is('admin.products.*') ? 'bg-[#5DA9FF] text-white' : 'hover:bg-[#5DA9FF]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 256 256">
                    <path fill="currentColor" d="m235.65 121.64l-54.27-81.41A14 14 0 0 0 169.73 34H86.27a14 14 0 0 0-11.65 6.23l-54.27 81.41a14 14 0 0 0-1.86 11.45l21.44 78.59A14 14 0 0 0 53.43 222H80a14 14 0 0 0 14-14v-18h68v18a14 14 0 0 0 14 14h26.57a14 14 0 0 0 13.5-10.32l21.44-78.59a14 14 0 0 0-1.86-11.45M80 178a2 2 0 0 1-2-2V65.49L106 82v54a6 6 0 0 0 12 0V89.07l7 4.1a6 6 0 0 0 6.1 0l6.95-4.1V128a6 6 0 0 0 12 0V82l28-16.51V176a2 2 0 0 1-2 2Zm6.27-132h83.46a2 2 0 0 1 1.66.89l4.1 6.15L128 81L80.51 53l4.1-6.15a2 2 0 0 1 1.66-.85M82 208a2 2 0 0 1-2 2H53.43a2 2 0 0 1-1.92-1.47l-21.44-78.6a2 2 0 0 1 .27-1.63L66 74.8V176a14 14 0 0 0 14 14h2Zm143.93-78.07l-21.44 78.6a2 2 0 0 1-1.92 1.47H176a2 2 0 0 1-2-2v-18h2a14 14 0 0 0 14-14V74.8l35.66 53.5a2 2 0 0 1 .27 1.63"></path>
                </svg>                
                <span class="text-lg transition-opacity duration-300">Products</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ Route::is('admin.orders.*') ? 'bg-[#5DA9FF] text-white' : 'hover:bg-[#5DA9FF]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 24 24">
                    <path fill="currentColor" d="m17.371 19.827l2.84-2.796l-.626-.627l-2.214 2.183l-.955-.975l-.627.632zM6.77 8.731h10.462v-1H6.769zM18 22.116q-1.671 0-2.835-1.165Q14 19.787 14 18.116t1.165-2.836T18 14.116t2.836 1.164T22 18.116q0 1.67-1.164 2.835Q19.67 22.116 18 22.116M4 20.769V5.616q0-.672.472-1.144T5.616 4h12.769q.67 0 1.143.472q.472.472.472 1.144v5.944q-.244-.09-.484-.154q-.241-.064-.516-.1v-5.69q0-.231-.192-.424T18.384 5H5.616q-.231 0-.424.192T5 5.616V19.05h6.344q.068.41.176.802q.109.392.303.748l-.034.034l-1.135-.826l-1.346.961l-1.346-.961l-1.346.961l-1.347-.961zm2.77-4.5h4.709q.056-.275.138-.515t.192-.485H6.77zm0-3.769h7.31q.49-.387 1.05-.645q.56-.259 1.197-.355H6.769zM5 19.05V5z"></path>
                </svg>
                <span class="text-lg transition-opacity duration-300">Orders</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 transition-all {{ Route::is('admin.users.*') ? 'bg-[#5DA9FF] text-white' : 'hover:bg-[#5DA9FF]' }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 16 16">
                    <path fill="none" stroke="currentColor" strokeLinejoin="round" d="M2.5 14v-.5a4 4 0 0 1 4-4h1a4 4 0 0 1 4 4v.5m2.421-10a1 1 0 0 0-.414.093c-.13.062-.25.152-.35.265l-.156.18l-.16-.18a1.1 1.1 0 0 0-.349-.265a.97.97 0 0 0-.827 0q-.198.094-.35.265a1.32 1.32 0 0 0-.315.866c0 .324.113.635.316.866L13 8l1.683-1.91c.203-.231.316-.542.316-.866s-.113-.635-.316-.866a1.1 1.1 0 0 0-.35-.265A1 1 0 0 0 13.922 4ZM9.5 5a2.5 2.5 0 1 1-5 0a2.5 2.5 0 0 1 5 0Z" strokeWidth={1}></path>
                </svg>
                <span class="text-lg transition-opacity duration-300">Customers</span>
                </a>
            </nav>
            <div class="p-4 border-t border-brand-700">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-red-200 transition-all hover:bg-red-700 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30px" height="30px" viewBox="0 0 14 14">
                        <path fill="none" stroke="currentColor" strokeLinecap="round" strokeLinejoin="round" d="M9.5 10.5v2a1 1 0 0 1-1 1h-7a1 1 0 0 1-1-1v-11a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v2M6.5 7h7m-2-2l2 2l-2 2" strokeWidth={1}></path>
                    </svg>           
                    <span class="text-lg transition-opacity duration-300">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col w-full">
            <header class="sticky top-0 z-20 bg-white/90 backdrop-blur-lg border-b border-slate-200 shadow-md">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 text-slate-500 hover:text-brand-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </button>
                    <div class="flex-1 flex justify-center">
                        <input type="text" placeholder="Quick search..." class="w-full max-w-md rounded-xl border border-slate-300 px-4 py-2 focus:border-brand-500 focus:ring-brand-500 transition-all">
                    </div>
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 rounded-full bg-brand-100 p-2 hover:bg-brand-200 transition-all">
                            <img class="h-8 w-8 rounded-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=3a2592&color=fff" alt="Admin">
                            <span class="hidden sm:block font-medium text-slate-700">{{ Auth::user()->name }}</span>
                            <svg x-bind:class="{ 'rotate-180': dropdownOpen }" class="h-4 w-4 text-brand-700 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-cloak class="absolute right-0 mt-2 w-48 origin-top-right rounded-xl bg-white py-1 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                            <a href="{{ route('admin.profile') }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-brand-50">My Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-brand-50">Sign out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>
        </div>

        <!-- Delete Modal -->
        <div id="delete-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/50 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-start gap-4">
                    <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z" /></svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-slate-900">Delete Item</h3>
                        <p class="mt-1 text-sm text-slate-500">Are you sure you want to delete this item? This action cannot be undone.</p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button onclick="closeDeleteModal()" type="button" class="rounded-xl border bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-md hover:bg-slate-50 focus:outline-none">
                        Cancel
                    </button>
                    <form id="delete-form" action="" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-md hover:bg-red-700 focus:outline-none">
                            Yes, Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openDeleteModal(deleteUrl) {
                document.getElementById('delete-form').action = deleteUrl;
                document.getElementById('delete-modal').classList.add('modal-open');
            }
            function closeDeleteModal() {
                document.getElementById('delete-modal').classList.remove('modal-open');
                document.getElementById('delete-form').action = '';
            }
            // Close modal when clicking outside
            document.getElementById('delete-modal').addEventListener('click', function(e) {
                if (e.target === this) closeDeleteModal();
            });
            // Listen for Alpine.js event (backward compat)
            window.addEventListener('open-delete-modal', function(e) {
                openDeleteModal(e.detail.deleteUrl);
            });
        </script>

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show" x-cloak
                class="fixed bottom-5 right-5 z-50 rounded-xl bg-brand-800 px-5 py-3 text-white shadow-lg">
                <div class="flex items-center gap-3">
                    <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75l3 3.75m0 0l3-3.75m-3 3.75V3.375m0 16.5v-7.5m0 0l3-3.75m-3 3.75l-3-3.75" /></svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @yield('scripts')
    </div>
</body>
</html>