<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index(){
        $products = Product::all();
        return new PostResource('200', "Berhasil mengambil data produk", $products);
    }
    public function show($id){
        $products = Product::find($id);
        $reviews = $products->reviews;
        return new PostResource('200', "Berhasil mengambil data produk", ['products' => $products, 'reviews' => $reviews]);
    }

    public function store(Request $request){
        $request -> validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'stock' => 'required',
            'image' => 'required',
            'category_id' => 'required',
            'size' => 'required',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'image' => $request->image,
            'category_id' => $request->category_id,
            'size' => $request->size,
        ]);

        return new PostResource('200', "Berhasil Menambahkan Product", $product);
    }

    public function update(Request $request, $id){
        $product = Product::find($id);

        if(!$product){
            return new PostResource('404', "Product Tidak Ditemukan", $product);
        }
        
        Product::update([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'stock' => $request->stock,
            'image' => $request->image,
            'category_id' => $request->category_id,
            'size' => $request->size,
        ]);
        return new PostResource('200', "Berhasil Mengupdate Product", $product);
    }

    public function destroy($id){
        $product = Product::find($id);
        if(!$product){
            return new PostResource('404', "Product Tidak Ditemukan", $product);
        }
        $product->delete();
        return new PostResource('200', "Behasil menghapus product", $product);
    }

}
