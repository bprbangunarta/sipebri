<?php

namespace App\Policies;

use App\Models\LoanApplication;
use App\Models\User;

class LoanApplicationPolicy
{
    /**
     * The creator sees their own files; people who work on files at a later stage
     * (scheduling, survey and analysis) see them too.
     */
    public function view(User $user, LoanApplication $loan): bool
    {
        if ($loan->created_by === $user->id) {
            return true;
        }

        return $user->hasAnyPermission(['scheduling.view', 'surveys.view', 'analysis.view']);
    }

    /**
     * Only the creator changes a file, and only while it is still a draft.
     */
    public function modify(User $user, LoanApplication $loan): bool
    {
        return $loan->created_by === $user->id && $loan->isDraft() && $user->can('loan-applications.manage');
    }
}
