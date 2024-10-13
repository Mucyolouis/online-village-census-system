<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ApprovalsPolicy
{
    use HandlesAuthorization;

    public function view(User $user): bool
    {
        return $user->is_approved && $user->hasAnyRole(['cov', 'super_admin']);
    }
    public function viewAny(User $user): bool
    {
        return $user->is_approved && $user->hasAnyRole(['cov', 'super_admin']);
    }
}