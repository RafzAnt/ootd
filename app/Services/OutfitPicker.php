<?php

namespace App\Services;

use App\Models\Ootd;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Picks an outfit at random, leaning towards ones the user hasn't worn in a
 * while (and a little towards favorites) so suggestions don't repeat.
 */
class OutfitPicker
{
    private const NEVER_WORN_DAYS = 60;

    private const FAVORITE_BOOST = 1.5;

    /**
     * @param  float|null  $roll  A number in [0, 1) to use instead of a random one (for tests).
     */
    public function pick(User $user, ?string $occasion = null, ?int $excludeId = null, ?float $roll = null): ?Ootd
    {
        $candidates = $user->ootds()
            ->withWearStats()
            ->when($occasion, fn ($query) => $query->where('occasion', $occasion))
            ->when($excludeId, fn ($query) => $query->whereKeyNot($excludeId))
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $weights = $candidates->map(fn (Ootd $ootd) => $this->weight($ootd));

        $target = ($roll ?? mt_rand() / (mt_getrandmax() + 1)) * $weights->sum();

        foreach ($candidates as $index => $ootd) {
            $target -= $weights[$index];

            if ($target < 0) {
                return $ootd;
            }
        }

        return $candidates->last();
    }

    private function weight(Ootd $ootd): float
    {
        $days = $ootd->last_worn_on
            ? min(self::NEVER_WORN_DAYS, Carbon::parse($ootd->last_worn_on)->startOfDay()->diffInDays(today()))
            : self::NEVER_WORN_DAYS;

        return (1 + max(0, $days)) * ($ootd->is_favorite ? self::FAVORITE_BOOST : 1);
    }
}
