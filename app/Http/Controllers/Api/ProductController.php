<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;
use App\Models\Product;
use App\Http\Requests\UpdateProductRequest;


class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    public function index()
    {
        return response()->json([
            'data' => Product::latest()->get()
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product
        ], 201);
    }

public function update(UpdateProductRequest $request, $id)
{
    $product = Product::findOrFail($id);

    $updated = $this->productService->update($product, $request->validated());

    return response()->json([
        'message' => 'Product updated successfully',
        'data' => $updated
    ]);
}

public function destroy($id)
{
    $product = Product::findOrFail($id);

    $this->productService->delete($product);

    return response()->json([
        'message' => 'Product deleted successfully'
    ]);
}

}
