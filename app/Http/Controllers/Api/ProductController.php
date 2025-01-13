<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;



class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return ProductResource::collection(Product::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        return new ProductResource($product);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }

    public function purchase(Request $request, Product $product)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'purchase_quantity' => 'required|integer|min:1|max:' . $product->quantity_available,
            'total_amount' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $totalAmount = preg_replace('/[^\d.]/', '', $request->total_amount);
            
            $transaction = \DB::transaction(function () use ($product, $request, $totalAmount) {
                // Check if product is still available
                if ($product->quantity_available < $request->purchase_quantity) {
                    throw new \Exception('Insufficient quantity available');
                }

                $product->decrement('quantity_available', $request->purchase_quantity);
                
                return Transaction::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'quantity' => $request->purchase_quantity,
                    'total_amount' => $totalAmount
                ]);
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase completed successfully',
                'data' => new TransactionResource($transaction)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
