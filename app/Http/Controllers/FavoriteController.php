<?php

namespace App\Http\Controllers;

use App\Models\Ootd;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FavoriteController extends Controller
{
    public function __invoke(Request $request, Ootd $ootd): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $ootd);

        $ootd->is_favorite = ! $ootd->is_favorite;
        $ootd->save();

        if ($request->expectsJson()) {
            return response()->json(['favorite' => $ootd->is_favorite]);
        }

        return back()->with('status', $ootd->is_favorite ? 'Added to favorites.' : 'Removed from favorites.');
    }
}
