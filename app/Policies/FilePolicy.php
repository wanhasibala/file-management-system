<?php

namespace App\Policies;

use App\Models\File;
use App\Models\User;

class FilePolicy
{
    /**
     * Determine whether the user can view any files.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the file.
     */
    public function view(User $user, File $file): bool
    {
        return true;
    }

    /**
     * Determine whether the user can download the file.
     */
    public function download(User $user, File $file): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create/upload files.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the file.
     */
    public function update(User $user, File $file): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the file.
     */
    public function delete(User $user, File $file): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the file.
     */
    public function restore(User $user, File $file): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the file.
     */
    public function forceDelete(User $user, File $file): bool
    {
        return $user->isAdmin();
    }
}
