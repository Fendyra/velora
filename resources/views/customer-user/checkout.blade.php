{{-- resources/views/customer-user/checkout.blade.php --}}

@extends('layouts.app')

@section('content')
<main class="bg-white mt-12 container mx-auto px-4 md:px-6 lg:px-8">
    <div class="flex flex-col lg:flex-row gap-12 py-12">
        <div class="w-full grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
                <h1 class="text-3xl font-bold text-black mb-6">Delivery</h1>
                <form action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <select name="country" class="form-select w-full" aria-label="Select Country" required>
                                <option value="">Select Country</option>
                                <option value="Indonesia" selected>Indonesia</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" name="first_name" class="form-control w-full" placeholder="First name" aria-label="First name" required>
                        </div>
                        <div class="col">
                            <input type="text" name="last_name" class="form-control w-full" placeholder="Last name" aria-label="Last name" required>
                        </div>
                        <div class="md:col-span-2">
                            <input type="text" name="address" class="form-control w-full" placeholder="Address" aria-label="Address" required>
                        </div>
                        <div class="md:col-span-2">
                            <input type="text" name="apartment" class="form-control w-full" placeholder="Apartment, House, etc (optional)" aria-label="Apartment, House, etc (optional)">
                        </div>
                        <div class="md:col-span-2">
                            <input type="text" name="city" class="form-control w-full" placeholder="City" aria-label="City" required>
                        </div>
                        <div class="col">
                            <input type="text" name="province" class="form-control w-full" placeholder="Province" aria-label="Province" required>
                        </div>
                        <div class="col">
                            <input type="text" name="postal_code" class="form-control w-full" placeholder="Postal Code" aria-label="Postal Code" required>
                        </div>
                        <div class="md:col-span-2">
                            <input type="text" name="phone" class="form-control w-full" placeholder="Phone" aria-label="Phone" required>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h2 class="text-3xl font-bold text-black mt-4 mb-2">Payment</h2>
                        <p class="text-gray-600 text-sm mb-1">Send payment to BCA account</p>
                        <p class="text-gray-600 text-sm mb-1">129303282 A/N Khayyuna Faza</p>
                        <p class="text-gray-600 text-sm mb-4">Please insert Your Proof of Payment Below.</p>

                        <div id="upload-area" class="mt-1 flex justify-center px-6 pt-3 pb-6 border-2 border-gray-300 border-dashed rounded-[25px] transition-all duration-300">
                            <div id="upload-placeholder" class="space-y-1 text-center">
                                <iconify-icon icon="zondicons:cloud-upload" width="60" height="60" style="color: #737373"></iconify-icon>
                                <div class="flex text-sm text-gray-600">
                                    <label for="payment_proof" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                        <span class="text-[#010BEB]">Upload a Photo</span>
                                        <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG up to 2MB</p>
                                <p id="file-name" class="text-sm text-green-600 mt-2 hidden"></p>
                            </div>
                            <div id="image-preview" class="hidden relative w-full max-w-md">
                                <img id="preview-image" src="" alt="Payment Proof Preview" class="w-full h-auto max-h-80 object-contain rounded-lg shadow-lg">
                                <div class="mt-4 text-center">
                                    <p id="preview-filename" class="text-sm text-gray-700 font-medium mb-2"></p>
                                    <button type="button" id="change-image" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <iconify-icon icon="heroicons:photo" width="16" height="16" class="mr-2"></iconify-icon>
                                        Change Image
                                    </button>
                                    <button type="button" id="remove-image" class="ml-2 inline-flex items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <iconify-icon icon="heroicons:trash" width="16" height="16" class="mr-2"></iconify-icon>
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full bg-[#010BEB] text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors font-semibold mt-6">Pay Now</button>
                </form>
            </div>

            <div>
                <h2 class="text-2xl font-bold mb-4">My Cart</h2>
                {{-- Iterate over $detailedCart, which now contains fully resolved image URLs --}}
                @if (count($detailedCart) > 0)
                    @foreach ($detailedCart as $item)
                        <div class="flex items-start space-x-4 border-base-200 pb-4 mb-4">
                            {{-- Image src directly uses $item['image'] as it's already a full URL --}}
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" loading="lazy" class="w-20 h-20 object-cover rounded-lg border-2 border-primary">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold">{{ $item['name'] }}</h3>
                                <p class="text-sm font-reguler mb-2">IDR {{ number_format($item['price'], 0, ',', '.') }}</p>
                                <p class="text-sm font-reguler mb-2">Size: <span class="bg-primary text-white px-2 py-1 rounded">{{ $item['size'] }}</span></p>
                                <p class="text-sm font-reguler mb-2">Quantity: {{ $item['quantity'] ?? 1 }}</p>
                            </div>
                        </div>
                    @endforeach
                    <div class="border-base-200 pt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-reguler font-semibold">Subtotal</span>
                            <span class="text-reguler font-semibold">IDR {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-reguler font-semibold">Shipping</span>
                            <span class="text-reguler font-semibold">IDR {{ number_format($shippingCost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-base-200 pt-2">
                            <span class="text-xl font-bold">Total</span>
                            <span class="text-xl font-bold">IDR {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-base-content/50 mb-4">Your cart is empty.</p>
                        <a href="{{ route('shop') }}" class="btn btn-primary">Continue Shopping</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</main>

<script>
    const paymentProofInput = document.getElementById('payment_proof');
    const fileNameDisplay = document.getElementById('file-name');
    const uploadArea = document.getElementById('upload-area');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    const previewFilename = document.getElementById('preview-filename');
    const changeImageBtn = document.getElementById('change-image');
    const removeImageBtn = document.getElementById('remove-image');

    function showImagePreview(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewFilename.textContent = file.name;

            uploadPlaceholder.classList.add('hidden');
            imagePreview.classList.remove('hidden');

            uploadArea.classList.remove('border-gray-300');
            uploadArea.classList.add('border-green-500', 'bg-green-50');
        };
        reader.readAsDataURL(file);
    }

    function hideImagePreview() {
        uploadPlaceholder.classList.remove('hidden');
        imagePreview.classList.add('hidden');

        uploadArea.classList.remove('border-green-500', 'bg-green-50', 'border-blue-500', 'bg-blue-50');
        uploadArea.classList.add('border-gray-300');

        paymentProofInput.value = '';

        fileNameDisplay.textContent = '';
        fileNameDisplay.classList.add('hidden');
    }

    paymentProofInput.addEventListener('change', function() {
        if (this.files && this.files.length > 0) {
            const file = this.files[0];

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB.');
                this.value = '';
                return;
            }

            showImagePreview(file);
        } else {
            hideImagePreview();
        }
    });

    changeImageBtn.addEventListener('click', function() {
        paymentProofInput.click();
    });

    removeImageBtn.addEventListener('click', function() {
        hideImagePreview();
    });

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        if (!imagePreview.classList.contains('hidden')) return;
        uploadArea.classList.add('bg-gray-100');
    });

    uploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        if (!imagePreview.classList.contains('hidden')) return;
        uploadArea.classList.remove('bg-gray-100');
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadArea.classList.remove('bg-gray-100');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];

            if (!file.type.startsWith('image/')) {
                alert('Please select an image file.');
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB.');
                return;
            }

            const dt = new DataTransfer();
            dt.items.add(file);
            paymentProofInput.files = dt.files;

            showImagePreview(file);
        }
    });

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        document.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
</script>
@endsection