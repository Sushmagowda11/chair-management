<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Services\ComponentService;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    public function __construct(private ComponentService $service) {}

<<<<<<< HEAD
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
=======
    public function index()
    {
        return response()->json([
            'data' => $this->service->list()
        ]);
    }

>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    public function store(Request $request)
    {
        $component = $this->service->store($request->all());

        return response()->json([
<<<<<<< HEAD
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
=======
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

>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    public function destroy(Component $component)
    {
        $this->service->delete($component);

<<<<<<< HEAD
        return response()->json([], 200)
            ->header('X-STATUS-CODE', 200)
            ->header('X-STATUS', 'ok')
            ->header('X-STATUS-MSG', config('messages.component_deleted'));
=======
        return response()->json([
            'message' => 'Component deleted successfully'
        ]);
>>>>>>> 12d698d386402a5adf1bdb0eee155e55a1882bba
    }
}
