<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ootd_id', 'worn_on'])]
class WearLog extends Model
{
    protected function casts(): array
    {
        return ['worn_on' => 'date:Y-m-d'];
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
