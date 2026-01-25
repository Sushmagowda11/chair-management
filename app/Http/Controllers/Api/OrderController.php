<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Bom;
use App\Models\OrderComponent;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('product')
            ->latest()
            ->get()
            ->map(function ($order) {
                return [
                    'id'                => $order->id,
                    'order_number'      => $order->order_number,
                    'client_name'       => $order->client_name,
                    'product'           => optional($order->product)->product_name,
                    'quantity'          => $order->quantity,
                    'order_date'        => $order->order_date,
                    'expected_delivery' => $order->expected_delivery,
                    'total_amount'      => $order->total_amount,
                    'status'            => $order->status,
                ];
            });

       return response()->json([
    'data' => $orders,
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_list_fetched'));

    }

    /**
     * ✅ CREATE ORDER + SNAPSHOT COMPONENTS
     */
   public function store(StoreOrderRequest $request)
{
   DB::transaction(function () use ($request, &$order) {

    $order = Order::create([
        'order_number'       => 'TEMP', // placeholder
        'client_name'        => $request->client_name,
        'product_id'         => $request->product_id,
        'quantity'           => $request->quantity,
        'order_date'         => now()->toDateString(),
        'expected_delivery'  => $request->expected_delivery,
        'total_amount'       => Product::find($request->product_id)->price * $request->quantity,
        'status'             => 'In Production',
        'notes'              => $request->notes,
    ]);

    // ✅ Now safely generate unique order number
    $order->update([
        'order_number' => 'ORD-' . now()->year . '-' . str_pad($order->id, 3, '0', STR_PAD_LEFT)
    ]);

    // 🔹 READ BOM (ONLY READ)
   $boms = Bom::with('bomItems.component')
    ->where('product_id', $request->product_id)
    ->get();


foreach ($boms as $bom) {
    foreach ($bom->bomItems as $item) {
        OrderComponent::create([
            'order_id'           => $order->id,
            'product_id'         => $request->product_id,
            'component_id'       => $item->component_id,

            // SNAPSHOT (important)
            'component_name'     => $item->component->component_name,
            'component_unit'     => $item->component->unit,

            'quantity_per_unit'  => $item->quantity,
            'total_quantity'     => $item->quantity * $request->quantity,
        ]);
    }
}

});


   return response()->json([], 201)
    ->header('X-STATUS-CODE', 201)
    ->header('X-STATUS', 'ok')
    ->header('X-STATUS-MSG', config('messages.order_created'));

}


    public function update(UpdateOrderRequest $request, Order $order)
    {
        $product = Product::findOrFail($request->product_id);

        $order->update([
            'client_name'       => $request->client_name,
            'product_id'        => $product->id,
            'quantity'          => $request->quantity,
            'expected_delivery' => $request->expected_delivery,
            'total_amount'      => $product->price * $request->quantity,
            'notes'             => $request->notes,
        ]);

        return response()->json([
    'data' => $order,
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_updated'));

    }

    public function destroy($id)
    {
        Order::findOrFail($id)->delete();

      return response()->json([], 200)
    ->header('X-STATUS-CODE', 200)
    ->header('X-STATUS', 'ok')
    ->header('X-STATUS-MSG', config('messages.order_deleted'));

    }

    public function ordersInProduction()
    {
        $orders = Order::where('status', 'In Production')
            ->with(['product', 'orderComponents'])
            ->get();

       return response()->json([
    'data' => $orders,
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_in_production'));

    }

   

    public function dispatchHistory()
    {
        $orders = Order::where('status', 'Dispatched')
            ->with(['product', 'orderComponents'])
            ->get();

       return response()->json([
    'data' => $orders,
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_dispatch_history'));

    }

    public function dashboardCounts()
    {
       return response()->json([
    'data' => [
        'in_production'  => Order::where('status', 'In Production')->count(),
        'ready_dispatch' => Order::where('status', 'Ready for Dispatch')->count(),
        'dispatched'     => Order::where('status', 'Dispatched')->count(),
    ],
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_dashboard_counts'));

    }

    public function show(Order $order)
{
    $order->load(['product', 'orderComponents']);

   return response()->json([
    'data' => [
        'order' => [
            'id'                => $order->id,
            'order_number'      => $order->order_number,
            'client_name'       => $order->client_name,
            'product_id'        => $order->product_id,
            'product_name'      => $order->product->product_name,

            'quantity'          => $order->quantity,
            'order_date'        => $order->order_date,
            'expected_delivery' => $order->expected_delivery,
            'total_amount'      => $order->total_amount,
            'status'            => $order->status,
            'notes'             => $order->notes,
        ],
        'components' => $order->orderComponents->map(function ($item) {
            return [
                'id'                => $item->id,
                'component_name'    => $item->component_name,
                'component_unit'    => $item->component_unit,
                'quantity_per_unit' => $item->quantity_per_unit,
                'total_quantity'    => $item->total_quantity,
            ];
        }),
    ],
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_list_fetched'));

}

// app/Http/Controllers/Api/OrderController.php

public function productionOrders(Request $request)
{
    $status = $request->query('status');

    $orders = DB::table('orders')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->select(
            'orders.id',
            'orders.order_number',
            'orders.client_name',
            'products.product_name',
            'orders.quantity',
            'orders.order_date',
            'orders.expected_delivery',
            'orders.status'
        )
        ->when($status, function ($q) use ($status) {
            $q->where('orders.status', $status);
        })
        ->orderBy('orders.order_date', 'desc')
        ->get();

   return response()->json([
    'data' => $orders
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.order_in_production'));

}


public function readyForDispatch()
{
    $orders = DB::table('orders')
        ->join('products', 'orders.product_id', '=', 'products.id')
        ->select(
            'orders.id',
            'orders.order_number',
            'orders.client_name',
            'products.product_name',
            'orders.quantity',
            'orders.order_date',
            'orders.expected_delivery',
            'orders.status'
        )
        ->where('orders.status', 'Ready for Dispatch')
        ->orderBy('orders.order_date', 'desc')
        ->get();

   return response()->json([
    'data' => $orders
], 200)
->header('X-STATUS-CODE', 200)
->header('X-STATUS', 'ok')
->header('X-STATUS-MSG', config('messages.orders_ready_dispatch'));

}

}
