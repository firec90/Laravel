<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
// use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //return response()->json(Product::all());
        $products = DB::select('SELECT * FROM product ORDER BY product_id DESC');
        return response()->json($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /*
        $validated = $request->validate([
            'product_name' => 'required|string|max:200',
            'product_price' => 'required|numeric',
        ]);

        $product = Product::create($validated);
        return response()->json($product, 201);
        */

        DB::insert('INSERT INTO product (product_name, product_price) VALUES (?, ?)',
        [$request->product_name, $request->product_price]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        /*
        $product = Product::findOrFail($id);
        return response()->json($product);
        */

        $product = DB::select('SELECT * FROM product WHERE product_id = ?', [$id]);
        if (!$product) {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }
        return response()->json($product[0]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        /*
        $product = Product::findOrFail($id);
        $product->update($request->all());
        return response()->json($product);
        */

        $affected = DB::update('UPDATE product SET product_name = ?, product_price = ? WHERE product_id = ?', [$request->product_name, $request->product_price, $id]);

        if ($affected) {
            return response()->json(['message' => 'Product berhasil diperbarui']);
        } else {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        /*
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
        */

        $deleted = DB::delete('DELETE FROM product WHERE product_id = ?', [$id]);
        if($deleted) {
            return response()->json(['message' => 'Produk berhasil dihapus']);
        } else {
            return response()->json(['message' => 'Product tidak ditemukan'], 404);
        }
    }
}
