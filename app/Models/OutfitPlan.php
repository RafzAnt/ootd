<?php

namespace App\Models;

use Database\Factories\OutfitPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'ootd_id', 'planned_for'])]
class OutfitPlan extends Model
{
    /** @use HasFactory<OutfitPlanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['planned_for' => 'date:Y-m-d'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ootd(): BelongsTo
    {
        return $this->belongsTo(Ootd::class);
    }
}
