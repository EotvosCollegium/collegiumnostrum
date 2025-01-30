<?php

namespace App\Policies;

use App\Models\Alumnus;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlumnusPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create entirely new drafts
     * send them to admins for review.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createNewDraft(?User $user) //might be guests too!
    {
        return true; //everyone, even guests
    }

    /**
     * Determine whether the user can create non-draft entries.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can view drafts.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewDraft(User $user)
    {
        return $user->is_admin;
    }

    /**
     * Determine whether the user can accept drafts and publish them.
     * Returns false if the given alumnus is not a draft, or
     * if the user is not an admin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function accept(User $user, Alumnus $alumnus)
    {
        return $user->is_admin && $alumnus->is_draft;
    }

    /**
     * Determine whether the user can reject drafts and publish them.
     * Now, it is the same as `accept`.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function reject(User $user, Alumnus $alumnus)
    {
        return $this->accept($user, $alumnus);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Alumnus $alumnus)
    {
        return $this->create($user) && !$alumnus->is_draft && !$alumnus->has_pair();
    }

    /**
     * Determine whether the user can create a draft for a given non-draft alumnus
     * and send them for review.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function createDraftFor(?User $user, Alumnus $alumnus) //can be guests too!
    {
        return $this->createNewDraft($user) && !$alumnus->is_draft && !$alumnus->pair_id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Alumnus $alumnus)
    {
        return $this->create($user);
    }
}
