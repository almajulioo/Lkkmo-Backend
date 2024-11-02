<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Order;
use Validator;

class OrderController extends Controller
{
    public function index(){
        $user_id = auth()->user()->id;
        $orders = Order::where('user_id', $user_id)->get();
        return new PostResource('200', "Berhasil mengambil data order", $orders);
    }

    public function getAll(){
        $orders = Order::with('user', 'product')->get();
        return new PostResource('200', "Berhasil mengambil semua data order", $orders);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'rental_start' => 'required',
            'rental_end' => 'required',
            'total_price' => 'required',
            'quantity' => 'required',
            'product_id' => 'required',
            'status' => 'required|in:Belum,Selesai',
            'size' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        Order::create([
            'user_id' => auth()->user()->id,
            'quantity' => $request->quantity,
            'product_id' => $request->product_id,
            'size' => $request->size,
            'rental_start' => $request->rental_start,
            'rental_end' => $request->rental_end,
            'total_price' => $request->total_price,
            'status' => $request->status
        ]);

        $product = Product::find($request->product_id);
        $product->update([
            'stock' => $product->stock - $request->quantity
        ]);

        return new PostResource('200', "Berhasil Menambahkan Order", null);
    }

    public function update(Request $request, $id){
        $order = Order::find($id);
        if(!$order){
            return new PostResource('404', "Order Tidak Ditemukan", $order);
        }

        if(auth()->user()->id == $order->user_id || auth()->user()->role == 'admin'){
            $order->update([
                'quantity' => $request->quantity,
                'product_id' => $request->product_id,
                'rental_start' => $request->rental_start,
                'rental_end' => $request->rental_end,
                'total_price' => $request->total_price,
                'status' => $request->status
            ]);
            return new PostResource('200', "Berhasil Mengupdate Order", $order);
        }
        return new PostResource('401', "Anda Tidak Memiliki Akses");
    }

    public function delete($id){
        $order = Order::find($id);
        if(!$order){
            return new PostResource('404', "Order Tidak Ditemukan", $order);
        }
        if(auth()->user()->id == $order->user_id || auth()->user()->role == 'admin'){
            $order->delete();
            return new PostResource('200', "Behasil Menghapus Order", $order);
        }
        return new PostResource('401', "Anda Tidak Memiliki Akses");
    }
}
