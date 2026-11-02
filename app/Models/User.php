<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'reminders_enabled', 'reminder_time'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function ootds(): HasMany
    {
        return $this->hasMany(Ootd::class);
    }

    public function wearLogs(): HasMany
    {
        return $this->hasMany(WearLog::class);
    }

    public function outfitPlans(): HasMany
    {
        return $this->hasMany(OutfitPlan::class);
    }

    /**
     * The outfit the user said they are wearing today, if any.
     */
    public function wornToday(): ?Ootd
    {
        return $this->wearLogs()->where('worn_on', today()->toDateString())->first()?->ootd;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'reminders_enabled' => 'boolean',
            'last_reminder_on' => 'date:Y-m-d',
        ];
    }
}
