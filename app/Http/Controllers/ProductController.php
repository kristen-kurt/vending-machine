<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Transaction;
use DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin')->except(['index', 'show', 'purchase']);
    }
    public function index(Request $request)
    {
        $query = Product::query();
        
        if ($request->has('sort')) {
            $direction = $request->direction;
            $sortField = $request->sort;
           
            $allowedSortFields = ['name', 'price', 'quantity_available'];
            if (in_array($sortField, $allowedSortFields)) {
                $query->orderBy($sortField, $direction);
            }
        } else {
            $query->orderBy('created_at', 'asc');
        }
        
        $products = $query->paginate(10);
 
        return view('products.index', compact('products'));
    }
    public function create()
    {
        return view('products.form');
    }
    public function store(ProductRequest $request)
    {
        Product::create($request->validated());
        return redirect()->route('products.index')
            ->with('success', 'Product created successfully');
    }
    public function show(Product $product)
    {
        return view('products.show', compact('product'));
    }
    public function edit(Product $product)
    {
        return view('products.form', compact('product'));
    }
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully');
    }
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->transactions()->delete();
            $product->delete();
        });

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully');
    }
    public function purchase(Request $request, Product $product)
    {
        $request->validate([
            'purchase_quantity' => 'required|integer|min:1|max:' . $product->quantity_available,
        ]);

        $totalAmount = preg_replace('/[^\d.]/', '', $request->total_amount);
        
        \DB::transaction(function () use ($product, $request, $totalAmount) {
            $product->decrement('quantity_available', $request->purchase_quantity);
            
            Transaction::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'quantity' => $request->purchase_quantity,
                'total_amount' => $totalAmount
            ]);
        });

        return redirect()->route('products.index')
            ->with('success', 'Purchase completed successfully');
    }
}
