<?php

namespace App\Policies;

use App\Models\Ootd;
use App\Models\User;

class OotdPolicy
{
    public function update(User $user, Ootd $ootd): bool
    {
        return $ootd->user_id === $user->id;
    }

    public function delete(User $user, Ootd $ootd): bool
    {
        return $ootd->user_id === $user->id;
    }
}
