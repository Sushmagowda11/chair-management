<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Services\ComponentService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function __construct(private ComponentService $service) {}

    /* ===============================
     | GET: COMPONENT LIST
     =============================== */
    public function index()
    {
        $components = $this->service->list();

        if ($components->isEmpty()) {
            return response()->json([], 404)
                ->header('X-STATUS-CODE', 404)
                ->header('X-STATUS', 'fail')
                ->header('X-STATUS-MSG', config('messages.data_not_found'));
        }

        return response()->json([
            'data' => $components,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.component_list_fetched'));
    }

    /* ===============================
     | POST: CREATE COMPONENT
     =============================== */
    public function store(Request $request)
    {
        $component = $this->service->store($request->all());

        return response()->json([
            'data' => $component,
        ], 201)
        ->header('X-STATUS-CODE', 201)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.component_created'));
    }

    /* ===============================
     | PUT: UPDATE COMPONENT
     =============================== */
    public function update(Request $request, Component $component)
    {
        $updated = $this->service->update($component, $request->all());

        return response()->json([
            'data' => $updated,
        ], 200)
        ->header('X-STATUS-CODE', 200)
        ->header('X-STATUS', 'ok')
        ->header('X-STATUS-MSG', config('messages.component_updated'));
    }

    /* ===============================
     | DELETE: REMOVE COMPONENT
     =============================== */
    public function destroy(Component $component)
    {
        $this->service->delete($component);

        return response()->json([], 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.component_deleted'));
    }
}
