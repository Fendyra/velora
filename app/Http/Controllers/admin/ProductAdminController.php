<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductAdminController extends Controller
{
    use ImageUploadTrait;

    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        if ($request->filled('category')) {
            $categorySlug = $request->input('category');
            $category = Category::where('slug', $categorySlug)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        $products = $query->paginate(10);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'array',
            'sizes.*' => 'string|max:50',
            'gender' => 'required|string|in:Men,Women,Unisex',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:' . ($request->price ?? 0),
            'stock' => 'required|integer|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            // Validation for explicit front and back images
            'image_front' => $this->imageUploadRule(required: true),
            'image_back' => $this->imageUploadRule(required: false),
        ]);

        $data = $request->only([
            'name', 'description', 'sizes', 'gender', 'price',
            'discount', 'stock', 'shipping_cost', 'category_id'
        ]);

        $data['image_front'] = $this->storeImageFromRequest($request, 'image_front', 'products');
        $data['image'] = $data['image_front'];
        $data['image_back'] = $this->storeImageFromRequest($request, 'image_back', 'products');

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product added successfully.');
    }

    public function edit($id)
    {
        $product = Product::with('category')->findOrFail($id);
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sizes' => 'array',
            'sizes.*' => 'string|max:50',
            'gender' => 'required|string|in:Men,Women,Unisex',
            'price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:' . ($request->price ?? 0),
            'stock' => 'required|integer|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            // Images are nullable for update, as they might not be changed
            'image_front' => $this->imageUploadRule(required: false),
            'image_back' => $this->imageUploadRule(required: false),
        ]);

        $data = $request->only([
            'name', 'description', 'sizes', 'gender', 'price',
            'discount', 'stock', 'shipping_cost', 'category_id'
        ]);

        // Handle image_front update
        $newFront = $this->storeImageFromRequest($request, 'image_front', 'products', $product->image_front);
        if ($newFront) {
            $data['image_front'] = $newFront;
            $data['image'] = $newFront;
        }
        // If image_front is NOT provided in the request, and current image_front exists, retain it.
        // If you want to explicitly clear it via the form, you'd add a hidden field or checkbox.

        // Handle image_back update
        $newBack = $this->storeImageFromRequest($request, 'image_back', 'products', $product->image_back);
        if ($newBack) {
            $data['image_back'] = $newBack;
        }
        // If image_back is NOT provided, and current image_back exists, retain it.
        // If you want to explicitly clear it via the form, you'd add a hidden field or checkbox.

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Delete image_front if it exists
        if ($product->image_front && Storage::disk('public')->exists($product->image_front)) {
            Storage::disk('public')->delete($product->image_front);
        }
        // Delete image_back if it exists
        if ($product->image_back && Storage::disk('public')->exists($product->image_back)) {
            Storage::disk('public')->delete($product->image_back);
        }
        // Delete the general 'image' field if it's separate or a duplicate.
        // The condition `($product->image && $product->image !== $product->image_front)` prevents deleting the same file twice.
        if ($product->image && Storage::disk('public')->exists($product->image) && $product->image !== $product->image_front) {
             Storage::disk('public')->delete($product->image);
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }
}