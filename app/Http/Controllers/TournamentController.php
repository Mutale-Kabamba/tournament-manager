<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    /**
     * Display the main tournament manager view.
     */
    public function index(): View
    {
        $tournament = Tournament::active();

        return view('tournament', [
            'tournament' => $tournament,
            'initialState' => $tournament->state,
        ]);
    }

    /**
     * Get the latest tournament state for live updates.
     */
    public function getState(): JsonResponse
    {
        $tournament = Tournament::active();

        return response()->json([
            'success' => true,
            'title' => $tournament->title,
            'state' => $tournament->state,
            'updated_at' => $tournament->updated_at?->toIso8601String(),
            'timestamp' => $tournament->updated_at?->timestamp ?? time(),
        ]);
    }

    /**
     * Verify the administrator PIN.
     */
    public function verifyPin(Request $request): JsonResponse
    {
        $request->validate([
            'pin' => 'required|string',
        ]);

        $tournament = Tournament::active();
        $expectedPin = $tournament->admin_pin ?: ($tournament->state['adminPin'] ?? '1234');

        if ($request->input('pin') === (string) $expectedPin) {
            session(['tournament_admin_unlocked' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Admin PIN verified successfully.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Incorrect PIN code.',
        ], 401);
    }

    /**
     * Save the entire tournament state.
     */
    public function updateState(Request $request): JsonResponse
    {
        $tournament = Tournament::active();
        $expectedPin = $tournament->admin_pin ?: ($tournament->state['adminPin'] ?? '1234');

        $providedPin = $request->header('X-Admin-PIN') ?? $request->input('pin');

        if (!session('tournament_admin_unlocked') && (string) $providedPin !== (string) $expectedPin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Valid Admin PIN required to modify tournament data.',
            ], 403);
        }

        $payload = $request->input('state');
        if (!is_array($payload)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid state format. Array expected.',
            ], 422);
        }

        // Keep adminPin consistent
        $payload['adminPin'] = $expectedPin;
        $tournament->state = $payload;

        if ($request->filled('title')) {
            $tournament->title = $request->input('title');
        }

        $tournament->save();

        return response()->json([
            'success' => true,
            'message' => 'Tournament saved successfully.',
            'state' => $tournament->state,
            'updated_at' => $tournament->updated_at?->toIso8601String(),
        ]);
    }

    /**
     * Change the admin PIN code.
     */
    public function changePin(Request $request): JsonResponse
    {
        $request->validate([
            'current_pin' => 'required|string',
            'new_pin' => 'required|string|min:4|max:16',
        ]);

        $tournament = Tournament::active();
        $expectedPin = $tournament->admin_pin ?: ($tournament->state['adminPin'] ?? '1234');

        if ($request->input('current_pin') !== (string) $expectedPin) {
            return response()->json([
                'success' => false,
                'message' => 'Current PIN is incorrect.',
            ], 401);
        }

        $newPin = $request->input('new_pin');
        $tournament->admin_pin = $newPin;

        $state = $tournament->state;
        if (is_array($state)) {
            $state['adminPin'] = $newPin;
            $tournament->state = $state;
        }

        $tournament->save();
        session(['tournament_admin_unlocked' => true]);

        return response()->json([
            'success' => true,
            'message' => 'PIN code updated successfully.',
        ]);
    }

    /**
     * Reset tournament to original handwritten roster defaults.
     */
    public function reset(Request $request): JsonResponse
    {
        $tournament = Tournament::active();
        $expectedPin = $tournament->admin_pin ?: ($tournament->state['adminPin'] ?? '1234');
        $providedPin = $request->header('X-Admin-PIN') ?? $request->input('pin');

        if (!session('tournament_admin_unlocked') && (string) $providedPin !== (string) $expectedPin) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Admin PIN required to reset.',
            ], 403);
        }

        $freshState = Tournament::defaultState();
        $freshState['adminPin'] = $expectedPin;

        $tournament->state = $freshState;
        $tournament->save();

        return response()->json([
            'success' => true,
            'message' => 'Tournament reset to defaults.',
            'state' => $tournament->state,
        ]);
    }
}
