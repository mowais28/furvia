<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Experience;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExperienceController extends Controller
{
    /**
     * Get all user experiences
     */
    public function index()
    {
        $experiences = Experience::where('user_id', Auth::id())
            ->latest('start_year')
            ->get()
            ->map->formatted();

        return response()->json([
            'status' => 'success',
            'data' => $experiences
        ]);
    }

    /**
     * Add or update experience
     */
    public function save(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:experiences,id'],
            'title' => ['required', 'string', 'max:255'],
            'organization' => ['required', 'string', 'max:255'],
            'start_year' => ['required', 'digits:4', 'integer'],
            'end_year' => ['nullable', 'digits:4', 'integer'],
            'is_current' => ['boolean'],
            'description' => ['nullable', 'string'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = Auth::id();

        if (!empty($data['is_current']) && $data['is_current']) {
            $data['end_year'] = null;
        }

        $experience = Experience::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Experience updated successfully.'
                : 'Experience added successfully.',
            'data' => $experience->formatted(),
        ]);
    }

    /**
     * Delete experience
     */
    public function destroy($id)
    {
        $experience = Experience::where('user_id', Auth::id())->find($id);

        if (!$experience) {
            return response()->json([
                'status' => 'error',
                'message' => 'Experience not found.'
            ], 404);
        }

        $experience->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Experience deleted successfully.'
        ]);
    }
}
