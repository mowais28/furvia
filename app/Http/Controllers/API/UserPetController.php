<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserPet;
use App\Traits\FileUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserPetController extends Controller
{
    use FileUploader;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pets = UserPet::where('user_id', $request->user()->id)->get();
        $pets = $pets->map(function ($pet) {
            return $pet->fields();
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $pets
        ]);
    }

    public function storeOrUpdate(Request $request)
    {

        $validated = Validator::make($request->all(), [
            'pet_id' => 'nullable|integer|exists:user_pets,id',
            'name'   => 'sometimes|string|max:255',
            'type'   => 'sometimes|string|max:255',
            'breed'  => 'sometimes|string|max:255',
            'gender' => 'sometimes|string|max:50|in:male,female',
            'gender_castration' => 'sometimes|string|max:50',
            'dob'    => 'sometimes|date',
            'photo'  => 'nullable|image',
        ]);

        if ($validated->fails()) {
            return response()->json([
                "status" => "error",
                "message" => "Invalid request."
            ], 422);
        }

        $data = $request->only([
            'name',
            'type',
            'breed',
            'gender',
            'gender_castration',
            'dob'
        ]);

        $pet = null;
        if ($request->pet_id) {
            $pet = UserPet::where('user_id', $request->user()->id)->find($request->pet_id);

            if (!$pet) {
                return response()->json([
                    'message' => 'Pet not found.'
                ], 404);
            }

            if ($request->hasFile('photo') && $pet->photo) {
                $this->delete_file($pet->photo);
            }
        }

        if ($request->hasFile('photo')) {
            $data['photo'] = $this->uploadFile("pets", $request->file('photo'));
        }

        $pet = UserPet::updateOrCreate(
            [
                'id' => $request->pet_id,
                'user_id' => $request->user()->id
            ],
            array_merge($data, ['user_id' => $request->user()->id])
        );

        return response()->json([
            'message' => 'Pet profile saved successfully.',
            'pet' => $pet
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $pet = $request->user()->pets()->where('id', $id)->first();
        if (!$pet) {
            return response()->json([
                'message' => 'Pet not found'
            ], 404);
        }

        return response()->json($pet?->fields(), 200);
    }

    public function destroy(Request $request, UserPet $pet)
    {
        if ($pet->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->delete_file($pet->photo);
        $pet->delete();

        return response()->json([
            'message' => 'Pet deleted successfully'
        ], 200);
    }
}
