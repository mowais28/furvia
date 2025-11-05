<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserAvailabilityController extends Controller
{

    /**
     * Get all slots for the authenticated user (optionally filter by week/month)
     */
    public function index(Request $request)
    {
        $availabilities = UserAvailability::where('user_id', Auth::id())
            ->when($request->month, fn($q) => $q->where('month', $request->month))
            ->when($request->week, fn($q) => $q->where('week', $request->week))
            ->orderByRaw("FIELD(day, 'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')")
            ->get()
            ->groupBy('day')
            ->map(fn($daySlots) => $daySlots->values());

        return response()->json([
            'status' => 'success',
            'data' => $availabilities
        ]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();

        $validated = Validator::make($request->all(), [
            'id' => ['nullable', 'exists:user_availabilities,id'],
            'day' => ['required', 'string'],
            'month' => ['required', 'string'],
            'week' => ['required', 'integer'],
            'slot_label' => ['required', 'string', 'max:50'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validated->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validated->errors()->first(),
            ], 422);
        }

        $data = $validated->validated();
        $data['user_id'] = $user->id;

        if ($data['start_time'] && $data['end_time']) {
            $conflict = UserAvailability::where('user_id', $user->id)
                ->where('day', $data['day'])
                ->where('week', $data['week'])
                ->where('month', $data['month'])
                ->where(function ($q) use ($data) {
                    $q->where(function ($sub) use ($data) {
                        // Existing slot if take with new slot
                        $sub->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                            ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
                    })
                        ->orWhere(function ($sub) use ($data) {
                            // New slot completely inside existing one
                            $sub->where('start_time', '<=', $data['start_time'])
                                ->where('end_time', '>=', $data['end_time']);
                        });
                })
                ->when(isset($data['id']), fn($q) => $q->where('id', '!=', $data['id']))
                ->first();

            if ($conflict) {
                return response()->json([
                    'status' => 'error',
                    'message' => "This time slot overlaps with an existing one ({$conflict->slot_label}: {$conflict->start_time} - {$conflict->end_time}).",
                ], 409);
            }
        }

        $availability = UserAvailability::updateOrCreate(
            ['id' => $data['id'] ?? null],
            $data
        );

        return response()->json([
            'status' => 'success',
            'message' => isset($data['id'])
                ? 'Availability slot updated successfully.'
                : 'Availability slot added successfully.',
            'data' => $availability,
        ]);
    }

    /**
     * Delete a slot
     */
    public function destroy($id)
    {
        $availability = UserAvailability::where('user_id', Auth::id())->find($id);

        if (!$availability) {
            return response()->json([
                'status' => 'error',
                'message' => 'Availability not found.',
            ], 404);
        }

        $availability->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Availability deleted successfully.',
        ]);
    }
}
