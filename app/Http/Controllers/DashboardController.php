<?php

namespace App\Http\Controllers;

use App\Models\Ootd;
use App\Models\User;
use App\Services\OutfitPicker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const SUGGESTION_KEY = 'dashboard.suggestion';

    /** Color slots in the chart palette (see --series-N in resources/css/app.css). */
    private const MAX_SERIES = 8;

    public function __invoke(Request $request, OutfitPicker $picker): View|RedirectResponse
    {
        $user = $request->user();

        if ($request->boolean('shuffle')) {
            $this->suggest($request, $user, $picker, shuffle: true);

            return redirect()->route('dashboard');
        }

        $today = $user->wornToday();
        $total = $user->ootds()->count();
        $suggestion = ! $today && $total > 0 ? $this->suggest($request, $user, $picker) : null;

        // Already featured in the hero card, so don't repeat them below.
        $exclude = array_filter([$today?->id, $suggestion?->id]);

        $hour = now()->hour;

        return view('dashboard.index', [
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening'),
            'today' => $today,
            'suggestion' => $suggestion,
            'total' => $total,
            'favorites' => $user->ootds()->where('is_favorite', true)->count(),
            'neverWorn' => $user->ootds()->whereDoesntHave('wearLogs')->count(),
            'month' => $this->month($user),
            'mix' => $this->occasionMix($user, $total),
            // Never worn first, then the ones worn longest ago.
            'dustOff' => $user->ootds()
                ->withWearStats()
                ->when($exclude, fn ($query) => $query->whereKeyNot($exclude))
                ->orderByRaw('last_worn_on is not null')
                ->orderBy('last_worn_on')
                ->limit(4)
                ->get(),
            'recent' => $user->ootds()->latest()->limit(6)->get(),
        ]);
    }

    /**
     * Today's suggestion. Remembered for the day so refreshing the page doesn't
     * reshuffle; "Shuffle" picks a different one.
     */
    private function suggest(Request $request, User $user, OutfitPicker $picker, bool $shuffle = false): ?Ootd
    {
        $stored = $request->session()->get(self::SUGGESTION_KEY);
        $storedId = ($stored['date'] ?? null) === today()->toDateString() ? ($stored['id'] ?? null) : null;

        if ($storedId && ! $shuffle) {
            $ootd = $user->ootds()->withWearStats()->find($storedId);

            if ($ootd) {
                return $ootd;
            }
        }

        // With a single outfit there is nothing else to shuffle to.
        $ootd = $picker->pick($user, excludeId: $shuffle ? $storedId : null) ?? $picker->pick($user);

        $request->session()->put(self::SUGGESTION_KEY, ['date' => today()->toDateString(), 'id' => $ootd?->id]);

        return $ootd;
    }

    /**
     * @return array{name: string, day: int, days: int, worn: array<int, int>}
     */
    private function month(User $user): array
    {
        $worn = $user->wearLogs()
            ->where('worn_on', '>=', today()->startOfMonth()->toDateString())
            ->pluck('worn_on')
            ->map(fn ($date) => Carbon::parse($date)->day)
            ->all();

        return [
            'name' => today()->format('F'),
            'day' => today()->day,
            'days' => today()->daysInMonth,
            'worn' => $worn,
        ];
    }

    /**
     * How the wardrobe splits across occasions, in the config's order so an
     * occasion keeps its color no matter how many outfits it has.
     *
     * @return array<int, array{label: string, count: int, percent: int, slot: int|string}>
     */
    private function occasionMix(User $user, int $total): array
    {
        if ($total === 0) {
            return [];
        }

        $counts = $user->ootds()->selectRaw('occasion, count(*) as total')->groupBy('occasion')->pluck('total', 'occasion');

        $rows = [];
        $slot = 0;

        foreach (config('ootd.occasions') as $key => $label) {
            $slot++;
            $count = (int) ($counts[$key] ?? 0);

            if ($count > 0) {
                $rows[] = ['label' => $label, 'count' => $count, 'slot' => $slot <= self::MAX_SERIES ? $slot : 'other'];
            }
        }

        $tagged = array_sum(array_column($rows, 'count'));

        if ($tagged < $total) {
            $rows[] = ['label' => 'No occasion', 'count' => $total - $tagged, 'slot' => 'other'];
        }

        return array_map(fn ($row) => $row + ['percent' => (int) round($row['count'] / $total * 100)], $rows);
    }
}
