<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SkillController extends Controller
{
    /**
     * Get all user skills
     */
    public function index()
    {
        $skills = Skill::where('user_id', Auth::id())
            ->with('skill')
            ->get()
            ->map->formatted();

        return response()->json([
            'status' => 'success',
            'data' => $skills
        ]);
    }

    /**
     * Add or update skill
     */
    public function save(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:skills,id'],
            'list_skill_id' => ['required', 'exists:list_skills,id'],
            'proficiency' => ['nullable', 'integer', 'min:1', 'max:5'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = Auth::id();

        $skill = Skill::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        $skill->load('skill');

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Skill updated successfully.'
                : 'Skill added successfully.',
            'data' => $skill->formatted(),
        ]);
    }

    /**
     * Delete a skill
     */
    public function destroy($id)
    {
        $skill = Skill::where('user_id', Auth::id())->find($id);

        if (!$skill) {
            return response()->json([
                'status' => 'error',
                'message' => 'Skill not found.'
            ], 404);
        }

        $skill->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Skill deleted successfully.'
        ]);
    }
}
