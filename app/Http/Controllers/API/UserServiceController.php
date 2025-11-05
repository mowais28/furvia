<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserServiceController extends Controller
{
    public function getUsersByService($service_id)
    {
        $users = User::whereHas('services', function ($query) use ($service_id) {
            $query->where('list_service_id', $service_id);
        })
            ->with(['services:id,name'])
            ->get();

        if (!$users) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found with Service',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Users providing this service retrieved successfully.',
            'data' => $users,
        ]);
    }
}
