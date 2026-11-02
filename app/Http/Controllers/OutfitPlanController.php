<?php

namespace App\Http\Controllers;

use App\Models\OutfitPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OutfitPlanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $start = today();
        $end = today()->addDays(30);
        $plans = $user->outfitPlans()
            ->with('ootd')
            ->whereBetween('planned_for', [$start->toDateString(), $end->toDateString()])
            ->orderBy('planned_for')
            ->get()
            ->keyBy(fn (OutfitPlan $plan) => $plan->planned_for->toDateString());

        $days = collect(range(0, 6))->map(function (int $offset) use ($start, $plans): array {
            $date = $start->copy()->addDays($offset);

            return ['date' => $date, 'plan' => $plans->get($date->toDateString())];
        });

        return view('planner.index', [
            'days' => $days,
            'plans' => $plans->values(),
            'outfits' => $user->ootds()->orderByDesc('created_at')->get(),
            'maxDate' => $end->toDateString(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'ootd_id' => ['required', 'integer', Rule::exists('ootds', 'id')->where(fn ($query) => $query->where('user_id', $user->id))],
            'planned_for' => ['required', 'date_format:Y-m-d', 'after_or_equal:today', 'before_or_equal:'.today()->addDays(30)->toDateString()],
        ]);

        $user->outfitPlans()->updateOrCreate(
            ['planned_for' => $data['planned_for']],
            ['ootd_id' => $data['ootd_id']],
        );

        return redirect()->route('planner.index')->with('status', 'Outfit added to your plan.');
    }

    public function destroy(Request $request, OutfitPlan $outfitPlan): RedirectResponse
    {
        $outfitPlan = $request->user()->outfitPlans()->findOrFail($outfitPlan->id);
        Gate::authorize('delete', $outfitPlan->ootd);
        $outfitPlan->delete();

        return redirect()->route('planner.index')->with('status', 'Outfit plan removed.');
    }
}
