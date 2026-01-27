<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
<<<<<<< HEAD
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use App\Models\Product;
=======
use App\Services\ProductService;
use App\Models\Product;
use App\Http\Requests\UpdateProductRequest;

>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

<<<<<<< HEAD
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
=======
    public function index()
    {
        return response()->json([
            'data' => Product::latest()->get()
        ]);
    }

>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());

        return response()->json([
<<<<<<< HEAD
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
=======
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

>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
}
