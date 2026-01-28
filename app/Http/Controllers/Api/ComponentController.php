<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Services\ComponentService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function __construct(private ComponentService $service) {}

   
    public function index()
    {
        $components = $this->service->list();

        return response()->json([
            'data' => $components
        ], 200);
    }

   
    public function show($id)
    {
        $component = Component::find($id);

        if (! $component) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        return response()->json([
            'data' => $component
        ], 200);
    }

   
    public function store(Request $request)
    {
        $component = $this->service->store($request->all());

        // Frontend
        if ($request->boolean('ui')) {
            return response()->json([
                'message' => config('messages.component_created'),
                'code'    => 201
            ], 201);
        }

        // Postman
        return response()->json([
            'data' => $component
        ], 201);
    }

       public function update(Request $request, $id)
    {
        $component = Component::find($id);

        if (! $component) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        $updated = $this->service->update($component, $request->all());

        // Frontend
        if ($request->boolean('ui')) {
            return response()->json([
                'message' => config('messages.component_updated'),
                'code'    => 200
            ], 200);
        }

        // Postman
        return response()->json([
            'data' => $updated
        ], 200);
    }

        public function destroy($id)
    {
        $component = Component::find($id);

        if (! $component) {
            return response()->json([
                'message' => config('messages.data_not_found'),
                'code'    => 404
            ], 404);
        }

        $this->service->delete($component);

        return response()->json([
            'message' => config('messages.component_deleted'),
            'code'    => 200
        ], 200);
    }
}
