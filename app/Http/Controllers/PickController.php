<?php

namespace App\Http\Controllers;

use App\Services\OutfitPicker;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PickController extends Controller
{
    public function __invoke(Request $request, OutfitPicker $picker): View
    {
        $user = $request->user();

        $occasion = $request->string('occasion')->toString();
        $occasion = array_key_exists($occasion, config('ootd.occasions')) ? $occasion : null;
        $skip = $request->integer('skip') ?: null;

        // With only one matching outfit there is nothing else to shuffle to.
        $ootd = $picker->pick($user, $occasion, $skip) ?? $picker->pick($user, $occasion);

        return view('pick', [
            'ootd' => $ootd,
            'occasion' => $occasion,
            'occasions' => collect(config('ootd.occasions'))
                ->only($user->ootds()->whereNotNull('occasion')->distinct()->pluck('occasion')->all()),
            'total' => $user->ootds()->count(),
            'wornToday' => $user->wornToday(),
        ]);
    }
}
