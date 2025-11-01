<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\License;

class LicenseController extends Controller
{
    /**
     * Add or update a license record.
     */
    public function save(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:licenses,id'],
            'title' => ['required', 'string', 'max:255'],
            'license_number' => ['nullable', 'string', 'max:255'],
            'issued_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:issued_at'],
            'status' => ['nullable', 'in:Valid,Expired,Suspended,Pending'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = $user->id;

        $license = License::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => $request->id ? 'License updated successfully.' : 'License added successfully.',
            'license' => $license,
        ], 200);
    }

    /**
     * List all licenses for the authenticated user.
     */
    public function index()
    {
        $user = Auth::user();
        $licenses = License::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'licenses' => $licenses,
        ], 200);
    }

    /**
     * Delete a license record.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $license = License::where('id', $id)->where('user_id', $user->id)->first();

        if (!$license) {
            return response()->json([
                'status' => 'error',
                'message' => 'License not found.',
            ], 404);
        }

        $license->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'License deleted successfully.',
        ], 200);
    }
}
