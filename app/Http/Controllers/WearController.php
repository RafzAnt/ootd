<?php

namespace App\Http\Controllers;

use App\Models\Ootd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WearController extends Controller
{
    /** "I'm wearing this today." Picking another outfit later the same day replaces it. */
    public function store(Request $request, Ootd $ootd): RedirectResponse
    {
        Gate::authorize('update', $ootd);

        $request->user()->wearLogs()->updateOrCreate(
            ['worn_on' => today()->toDateString()],
            ['ootd_id' => $ootd->id],
        );

        $redirect = $request->input('then') === 'dashboard' ? redirect()->route('dashboard') : back();

        return $redirect->with('status', "You're wearing this today.");
    }

    public function destroy(Request $request, Ootd $ootd): RedirectResponse
    {
        Gate::authorize('update', $ootd);

        $request->user()->wearLogs()
            ->where('ootd_id', $ootd->id)
            ->where('worn_on', today()->toDateString())
            ->delete();

        return back()->with('status', 'Removed from today.');
    }
}
