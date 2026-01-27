<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /* ===============================
     | GET: PRODUCT LIST
     =============================== */
    public function index()
    {
        $products = Product::latest()->get();

        if ($products->isEmpty()) {
            return response()->json([], 404)
                ->header('X-STATUS-CODE', 404)
                ->header('X-STATUS', 'fail')
                ->header('X-STATUS-MSG', config('messages.data_not_found'));
        }

        return response()->json([
            'data' => $products,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.product_list_fetched'));
    }

    /* ===============================
     | POST: CREATE PRODUCT
     =============================== */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());

        return response()->json([
            'data' => $product,
        ], 201)
        ->header('X-STATUS-CODE', 201)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.product_created'));
    }

    /* ===============================
     | PUT: UPDATE PRODUCT
     =============================== */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::findOrFail($id);

        $updated = $this->productService->update($product, $request->validated());

        return response()->json([
            'data' => $updated,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.product_updated'));
    }

    /* ===============================
     | DELETE: REMOVE PRODUCT
     =============================== */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        $this->productService->delete($product);

        return response()->json([], 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.product_deleted'));
    }
}
