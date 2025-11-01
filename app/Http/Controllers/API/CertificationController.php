<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CertificationController extends Controller
{
    /**
     * Get all certifications of authenticated user
     */
    public function index()
    {
        $certifications = Certification::where('user_id', Auth::id())
            ->with('certification')
            ->latest()
            ->get()
            ->map->formatted();

        return response()->json([
            'status' => 'success',
            'data' => $certifications,
        ]);
    }

    /**
     * Add or update certification
     */
    public function save(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:certifications,id'],
            'list_certification_id' => ['required', 'exists:list_certifications,id'],
            'institution' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'digits:4', 'integer'],
            'credential_id' => ['nullable', 'string', 'max:255'],
            'credential_url' => ['nullable', 'url'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = $user->id;

        $certification = Certification::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        $certification->load('certification');

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Certification updated successfully.'
                : 'Certification added successfully.',
            'data' => $certification->formatted(),
        ]);
    }

    /**
     * Delete certification
     */
    public function destroy($id)
    {
        $certification = Certification::where('user_id', Auth::id())->find($id);

        if (!$certification) {
            return response()->json([
                'status' => 'error',
                'message' => 'Certification not found.'
            ], 404);
        }

        $certification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Certification deleted successfully.'
        ]);
    }
}
