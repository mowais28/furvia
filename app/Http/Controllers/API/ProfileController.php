<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['sometimes', 'string', 'max:20'],
        ];

        if ($user->type === 'provider') {
            $rules['location'] = ['sometimes', 'string', 'max:255'];
            $rules['zip'] = ['sometimes', 'string', 'max:255'];
            $rules['title'] = ['sometimes', 'string', 'max:255'];
            $rules['description'] = ['sometimes', 'string'];
        }

        $validated = Validator::make($request->all(), $rules);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();


        DB::transaction(function () use ($user, $data) {
            $user->update($data);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ], 200);
    }

    public function getUser(Request $request)
    {
        $userId = $request->query('id');

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.',
                ], 404);
            }
        } else {
            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized.',
                ], 401);
            }
        }

        return response()->json([
            'status' => 'success',
            'user' => $user,
        ], 200);
    }

    public function add_location(Request $request)
    {
        $validated = Validator::make($request->all(), [
            "location" => ["required"],
            "zip_code" => ["sometimes"],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $user = $request->user();
        $user->location = $request->location;
        $user->zip_code = $request->zip_code;
        $user->save();

        return response()->json([
            "status" => "success",
            "message" => "Location updated successfully",
            "data" => $user
        ], 200);
    }
}
