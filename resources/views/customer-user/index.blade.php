@extends('layouts.app')

@section('content')
    @php
        /** @var \App\Models\User $user */
        $user = Auth::user();
    @endphp
    <!-- Background Image Section -->
    <div class="relative w-full h-40 md:h-48 lg:h-56 bg-cover bg-center" style="background-image: url('/assets/images/section.png');">
        <div class="absolute inset-0 bg-black bg-opacity-10"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <h1 class="text-4xl font-bold text-[#0009c4] text-center mt-12">PROFILE</h1>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="container mx-auto px-4 py-8">
            @if(session('error'))
                <div class="alert alert-error mb-4">
                    {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-6 mt-4">
                <div class="space-y-4">
                    <!-- Name -->
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="text-lg font-semibold text-black">{{ $user->name }}</p>
                    </div>

                    <!-- Email -->
                    <div class="border-b pb-2">
                        <p class="text-sm text-gray-600">Email</p>
                        <p class="text-lg font-semibold text-black">{{ $user->email }}</p>
                    </div>

                    <!-- Password -->
                    <div class="border-b pb-2 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Password</p>
                            <p class="text-lg font-semibold text-black">********</p>
                        </div>
                        <button class="text-blue-600 hover:underline text-sm" onclick="document.getElementById('password-modal').showModal()">change</button>
                    </div>

                    <!-- Telephone -->
                    <div class="border-b pb-2 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600">Telephone</p>
                            <p class="text-lg font-semibold text-black">{{ $user->telephone }}</p>
                        </div>
                        <button class="text-blue-600 hover:underline text-sm" onclick="document.getElementById('telephone-modal').showModal()">change</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modal Change Password -->
    <dialog id="password-modal" class="z-50 bg-transparent p-0 m-0 border-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="relative bg-white p-6 rounded-xl w-full max-w-md shadow-lg">
                <h2 class="font-bold text-black text-xl mb-4">Change Password</h2>
                <span class="absolute top-4 right-4 text-black text-3xl cursor-pointer" onclick="document.getElementById('password-modal').close()">&times;</span>
                <form method="POST" action="{{ route('account.update-password') }}">
                    @csrf
                    @method('PATCH')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-black mb-1">Current Password</label>
                            <input type="password" name="current_password" class="w-full border border-black bg-white rounded-md p-2 text-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-black mb-1">New Password</label>
                            <input type="password" name="new_password" class="w-full border border-black bg-white rounded-md p-2 text-gray-700" required>
                        </div>
                        <div>
                            <label class="block text-black mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" class="w-full border border-black bg-white  rounded-md p-2 text-gray-700" required>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-700 text-white py-2 px-4 rounded-md font-semibold hover:bg-blue-800 transition">Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Modal Change Telephone -->
    <dialog id="telephone-modal" class="z-50 bg-transparent p-0 m-0 border-0">
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="relative bg-white p-6 rounded-xl w-full max-w-md shadow-lg">
                <h2 class="font-bold text-black text-xl mb-4">Change Telephone</h2>
                <span class="absolute top-4 right-4 text-black text-3xl cursor-pointer" onclick="document.getElementById('telephone-modal').close()">&times;</span>
                <form method="POST" action="{{ route('account.update-telephone') }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label class="block text-black mb-1">New Telephone Number</label>
                        <input type="tel" name="telephone" value="{{ $user->telephone }}" class="w-full border border-black bg-white rounded-md p-2 text-gray-700" required>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="w-full bg-blue-700 text-white py-2 px-4 rounded-md font-semibold hover:bg-blue-800 transition">Change Telephone</button>
                    </div>
                </form>
            </div>
        </div>
    </dialog>

    <!-- Optional: close modal on outside click -->
    <script>
        document.querySelectorAll("dialog").forEach(dialog => {
            dialog.addEventListener("click", (e) => {
                if (e.target === dialog) dialog.close();
            });
        });
    </script>
@endsection
