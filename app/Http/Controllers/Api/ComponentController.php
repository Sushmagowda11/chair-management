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
        return response()->json([
            'data' => $this->service->list()
        ]);
    }

    public function store(Request $request)
    {
        $component = $this->service->store($request->all());

        return response()->json([
            'message' => 'Component created successfully',
            'data' => $component
        ]);
    }

    public function update(Request $request, Component $component)
    {
        $component = $this->service->update($component, $request->all());

        return response()->json([
            'message' => 'Component updated successfully',
            'data' => $component
        ]);
    }

    public function destroy(Component $component)
    {
        $this->service->delete($component);

        return response()->json([
            'message' => 'Component deleted successfully'
        ]);
    }
}
