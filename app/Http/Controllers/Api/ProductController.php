<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private ProductService $productService) {}

    /* ===============================
     | GET: PRODUCT LIST
     | Success → data only
     =============================== */
    public function index()
    {
        $products = Product::latest()->get();

        return response()->json([
            'data' => $products,
        ], 200);
    }

    /* ===============================
     | GET: PRODUCT BY ID
     | Success → data only
     | Fail → message + code
     =============================== */
    public function show($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        return response()->json([
            'data' => $product
        ], 200);
    }

    /* ===============================
     | POST: CREATE PRODUCT
     | Frontend → message + code
     | Postman → full data
     =============================== */
    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());

        // Frontend
        if ($request->boolean('ui')) {
            return response()->json([
                'message' => config('messages.product_created'),
                'code'    => 201
            ], 201);
        }

        // Postman / backend testing
        return response()->json([
            'data' => $product
        ], 201);
    }

    /* ===============================
     | PUT: UPDATE PRODUCT
     | Frontend → message + code
     | Postman → full data
     | Invalid ID → message + code
     =============================== */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        $updated = $this->productService->update(
            $product,
            $request->validated()
        );

        // Frontend
        if ($request->boolean('ui')) {
            return response()->json([
                'message' => config('messages.product_updated'),
                'code'    => 200
            ], 200);
        }

        // Postman
        return response()->json([
            'data' => $updated
        ], 200);
    }

    /* ===============================
     | DELETE: REMOVE PRODUCT
     | Success → message + code
     | Fail → message + code
     =============================== */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (! $product) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        $this->productService->delete($product);

        return response()->json([
            'message' => config('messages.product_deleted'),
            'code'    => 200
        ], 200);
    }
}
