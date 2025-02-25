<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Services\SlotService;

class SlotController extends Controller
{
    protected $slotService;

    public function __construct(SlotService $slotService)
    {
        $this->slotService = $slotService;
    }

    public function index()
    {
        return Inertia::render('Slots', [
            'balance' => Auth::user()->balance
        ]);
    }

    public function spin(Request $request)
    {
        $user = Auth::user();
        $bet = $request->validate(['bet' => 'required|numeric|min:1'])['bet'];

        try {
            $result = $this->slotService->spin($user, $bet);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
