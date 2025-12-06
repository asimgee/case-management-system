<?php

namespace App\Policies;

use App\Models\Case;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasePolicy
{
    use HandlesAuthorization;

    public function view(User $user, Case_Model $casemodel)
    {
        return $user->id === $casemodel->user_id;
    }

    public function update(User $user, Case_Model $casemodel)
    {
        return $user->id === $casemodel->user_id;
    }

    public function delete(User $user, Case_Model $casemodel)
    {
        return $user->id === $casemodel->user_id;
    }
}