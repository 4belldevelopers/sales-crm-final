<?php

namespace App\Policies;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * TeamInvitationPolicy
 *
 * Enforces strict multi-tenant authorization for team invitation lifecycle actions.
 */
class TeamInvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view team invitations.
     */
    public function viewAny(User $user): bool
    {
        return method_exists($user, 'cans') ? $user->cans('view_team') : true;
    }

    /**
     * Determine whether the user can create invitations.
     */
    public function create(User $user): bool
    {
        return method_exists($user, 'cans') ? $user->cans('add_team') : true;
    }

    /**
     * Determine whether the user can resend or revoke a specific invitation.
     */
    public function manage(User $user, TeamInvitation $invitation): bool
    {
        // Enforce strict tenant isolation (Rule 9)
        if ((int) $user->company_id !== (int) $invitation->company_id) {
            return false;
        }

        return method_exists($user, 'cans') ? ($user->cans('edit_team') || $user->cans('delete_team')) : true;
    }
}
