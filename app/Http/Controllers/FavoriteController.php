<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function index(Request $request){
         $favorites = Favorite::where('user_id', $request->user()->id)
         ->with('product_id')
         ->get();
         return response()->json($favorites);
    }
    public function store(Request $request){
          $request->validate([
            'product_id' => 'required|exists:product,id'
          ]);
          $favorites = Favorite::firstOrCreate([
              'user_id' => $request->user()->id,
              'product_id' => $request->product_id,
          ]);
          return response()->json([
            'messgae' => 'Added to favorites',
            'date' => $favorites
          ],201);
    }
    public function destroy($product_id, Request $request){
        Favorite::where('user_id', $request->user()->id)
        ->where('product_id', $product_id)
        ->delete();
        return response()->json([
            'message' => 'removed from list favorite'
        ]);
    }
}
