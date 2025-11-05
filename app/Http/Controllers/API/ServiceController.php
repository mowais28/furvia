<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Get all user services
     */
    public function index()
    {
        $services = UserService::where('user_id', Auth::id())
            ->with('service')
            ->get()
            ->map->formatted();

        return response()->json([
            'status' => 'success',
            'data' => $services
        ]);
    }

    /**
     * Add or update a service
     */
    public function save(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:services,id'],
            'list_service_id' => ['required', 'exists:list_services,id'],
            'description' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = Auth::id();

        $service = UserService::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        $service->load('service');

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Service updated successfully.'
                : 'Service added successfully.',
            'data' => $service->formatted(),
        ]);
    }

    /**
     * Delete a service
     */
    public function destroy($id)
    {
        $service = UserService::where('user_id', Auth::id())->find($id);

        if (!$service) {
            return response()->json([
                'status' => 'error',
                'message' => 'Service not found.'
            ], 404);
        }

        $service->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Service deleted successfully.'
        ]);
    }
}
