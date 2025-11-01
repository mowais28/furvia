<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Education;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EducationController extends Controller
{
    /**
     * Get all education records of authenticated user
     */
    public function index()
    {
        $educations = Education::where('user_id', auth()->id())
            ->with('degree')
            ->get()
            ->map->formatted();


        return response()->json([
            'status' => 'success',
            'data' => $educations
        ]);
    }

    /**
     * Add or update education
     */
    public function save(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:educations,id'],
            'degree_id' => ['required', 'exists:list_degrees,id'],
            'institution' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'digits:4', 'integer'],
            'honor' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = $user->id;
        $data['list_degree_id'] = $data['degree_id']; 
        unset($data['degree_id']);

        $education = Education::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        $education->load('degree');

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Education updated successfully.'
                : 'Education added successfully.',
            'data' => $education->formatted(),
        ]);
    }


    /**
     * Delete education
     */
    public function destroy($id)
    {
        $education = Education::where('user_id', Auth::id())->find($id);

        if (!$education) {
            return response()->json([
                'status' => 'error',
                'message' => 'Education not found.'
            ], 404);
        }

        $education->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Education deleted successfully.'
        ]);
    }
}
