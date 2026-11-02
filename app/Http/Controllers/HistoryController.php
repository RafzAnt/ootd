<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $logs = $user->wearLogs()->with('ootd')->latest('worn_on')->paginate(24);

        return view('history', [
            'logs' => $logs,
            'hasOutfits' => $user->ootds()->exists(),
            'months' => $logs->getCollection()->groupBy(fn ($log) => $log->worn_on->format('F Y')),
            'mostWorn' => $user->ootds()->withWearStats()
                ->whereHas('wearLogs')
                ->orderByDesc('wear_logs_count')
                ->limit(3)
                ->get(),
        ]);
    }
}
