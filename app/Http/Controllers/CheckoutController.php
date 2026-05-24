<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function saveCartForCheckout(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Please login to proceed.']);
        }

        $cart = $request->input('cart', []);

        if (!is_array($cart) || empty($cart)) {
            return response()->json(['success' => false, 'message' => 'Invalid cart data']);
        }

        // The image paths in the $cart array coming from the frontend (product detail JS)
        // should already be full URLs due to getProductImageUrl, so no direct change here.
        Cookie::queue('cart', json_encode($cart), 1440);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }

        $cartFromCookie = json_decode(request()->cookie('cart') ?? '[]', true);
        $detailedCart = [];
        $subtotal = 0;

        foreach ($cartFromCookie as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                // Determine the correct image path to use for this product
                // Prioritize image_front, then image, then a placeholder.
                $imagePathForDisplay = $product->image_front ?? $product->image;

                // Resolve the image URL explicitly on the server side using asset()
                $resolvedImageUrl = 'https://via.placeholder.com/150'; // Default placeholder

                if ($imagePathForDisplay) {
                    if (str_starts_with($imagePathForDisplay, 'products/')) {
                        $resolvedImageUrl = asset('storage/' . $imagePathForDisplay);
                    } else {
                        // This else case is for images not in 'products/' subfolder, e.g., directly in 'public' or old format
                        $resolvedImageUrl = asset($imagePathForDisplay);
                    }
                }
                // If $item['image'] from the cookie is already a full URL, use it directly
                // This ensures flexibility for old cart data, but the above logic is safer for fresh product data.
                if (filter_var($item['image'], FILTER_VALIDATE_URL)) {
                    $resolvedImageUrl = $item['image'];
                }


                $detailedCart[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $resolvedImageUrl, // Pass the fully resolved URL to the view
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                ];
                $subtotal += ($product->price * $item['quantity']);
            }
        }

        $shippingCost = 40000;
        $total = $subtotal + $shippingCost;

        return view('customer-user.checkout', compact('detailedCart', 'subtotal', 'shippingCost', 'total'));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to complete your purchase.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'payment_proof' => 'required|image|max:2048',
        ]);

        $cart = json_decode(request()->cookie('cart') ?? '[]', true);
        if (empty($cart)) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $subtotal = array_reduce($cart, fn($sum, $item) => $sum + ($item['price'] * $item['quantity']), 0);
        $shippingCost = 40000;
        $total = $subtotal + $shippingCost;

        DB::beginTransaction();
        $paymentProofPath = null;

        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'shipping_cost' => $shippingCost,
                'total_amount' => $total,
                'status' => 'pending',
            ]);

            if ($request->hasFile('payment_proof')) {
                $paymentProofPath = $request->file('payment_proof')->store('payment_proofs', 'public');
                $order->update(['payment_proof' => $paymentProofPath]);
            }

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($item['id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            Cookie::queue(Cookie::forget('cart'));

            return redirect()->route('order.success')->with('success', 'Order placed successfully!');
        } catch (\Throwable $e) {
            DB::rollBack();

            if ($paymentProofPath && Storage::disk('public')->exists($paymentProofPath)) {
                Storage::disk('public')->delete($paymentProofPath);
            }

            report($e);

            return redirect()->back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function success()
    {
        return view('customer-user.order-success');
    }

    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to add items to your cart.');
        }

        $cart = json_decode(request()->cookie('cart') ?? '[]', true);

        $product = Product::find($request->product_id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $itemToAdd = [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            // Construct the image URL here using blade's asset logic, as this is server-side
            // This is the source for the `image` property in the cookie
            'image' => asset('storage/' . ($product->image_front ?? $product->image)), // Ensure absolute URL
            'size' => $request->size,
            'quantity' => $request->quantity ?? 1,
        ];

        $itemExists = false;
        foreach ($cart as &$cartItem) {
            if ($cartItem['id'] == $itemToAdd['id'] && $cartItem['size'] == $itemToAdd['size']) {
                $cartItem['quantity'] += $itemToAdd['quantity'];
                $itemExists = true;
                break;
            }
        }

        if (!$itemExists) {
            $cart[] = $itemToAdd;
        }

        Cookie::queue('cart', json_encode($cart), 1440);

        return redirect()->back()->with('success', 'Item added to cart!');
    }
}