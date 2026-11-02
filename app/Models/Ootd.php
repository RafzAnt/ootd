<?php

namespace App\Models;

use App\Services\OutfitImageStorage;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['image_path', 'description', 'occasion'])]
class Ootd extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleted(function (Ootd $ootd) {
            app(OutfitImageStorage::class)->delete($ootd->image_path);
        });
    }

    protected function casts(): array
    {
        return ['is_favorite' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wearLogs(): HasMany
    {
        return $this->hasMany(WearLog::class);
    }

    /**
     * Adds `wear_logs_count` and `last_worn_on` (a Y-m-d string or null).
     */
    public function scopeWithWearStats(Builder $query): void
    {
        $query->withCount('wearLogs')->withMax('wearLogs as last_worn_on', 'worn_on');
    }

    /**
     * Relative to the current request (not APP_URL), so photos still load when
     * the app is opened from another host or port, e.g. a phone on the same network.
     */
    public function imageUrl(): string
    {
        return app(OutfitImageStorage::class)->url($this->image_path);
    }

    public function occasionLabel(): ?string
    {
        return config('ootd.occasions')[$this->occasion] ?? null;
    }

    /** Needs the model to be loaded with withWearStats(); returns null otherwise. */
    public function wearSummary(): ?string
    {
        if (! array_key_exists('wear_logs_count', $this->attributes)) {
            return null;
        }

        if ($this->wear_logs_count === 0) {
            return 'Not worn yet';
        }

        $last = Carbon::parse($this->last_worn_on);
        $when = $last->isToday() ? 'today' : ($last->isYesterday() ? 'yesterday' : $last->diffForHumans());

        return "Worn {$this->wear_logs_count}× · last worn {$when}";
    }

    public function altText(): string
    {
        return $this->description ?: 'Outfit uploaded '.$this->created_at->toFormattedDateString();
    }
}
