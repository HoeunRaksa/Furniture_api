<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductImage;

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
            'price:' => 'required|numeric',
            'stock' => 'requierd|integer',
            'images' => 'required|mines:jpg,png,jpeg'
        ]);
        $product = Product::create($request->only([
             'category_id',
             'name',
             'description',
             'price',
             'discount',
             'stock',
             'rating'
        ]));
        if($request->hasFile('images')){
            foreach($request->file('images') as $image){
                $path = $image->store('products', 'public');
                ProductImage::create([
                     'product_id' => $product->id,
                     'image_url' => $path
                ]);
            }
        }
    }
    public function show($id){
        return Product::with('images')->findOrFail($id);
    }
    public function update(Request $request, $id){
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
    }
    public function destroy($id){
        $product = product::findOrFail($id)->delete();
        return response()->json(['message' => 'Product delated']);
    }
}
