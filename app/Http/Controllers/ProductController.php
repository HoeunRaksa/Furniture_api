<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Jobs\ProcessProductImage;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return Product::with('images')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:225',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'images.*' => 'required|mimes:jpg,png,jpeg|max:5120' // Max 5MB
        ]);

        $product = Product::create($request->only([
            'category_id', 'name', 'description', 'price', 'discount', 'stock', 'rating'
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Save temp file quickly
                $tempPath = $image->store('temp', 'public');
                
                // Process in background
                ProcessProductImage::dispatch($product->id, $tempPath);
            }
        }

        // Return product immediately (images will be processed in background)
        return response()->json($product->load('images'), 201);
    }

    public function show($id)
    {
        return Product::with('images')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product->load('images'));
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete associated images
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_url);
        }
        
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    // Optional: Upload images to existing product
    public function uploadImages(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'images.*' => 'required|mimes:jpg,png,jpeg|max:5120' // Max 5MB
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Save temp file quickly
                $tempPath = $image->store('temp', 'public');
                
                // Process in background
                ProcessProductImage::dispatch($product->id, $tempPath);
            }
        }

        return response()->json($product->load('images'));
    }
}