<?php

namespace App\Services;

use App\Models\User;

interface StaffMailProvisionerInterface
{
    /**
     * Provision an official staff mailbox (e.g. name@pearlinstitute.com)
     */
    public function provisionStaffAccount(User $staffUser, string $desiredUsername): array;

    /**
     * Deactivate or suspend a staff mailbox
     */
    public function deactivateStaffAccount(User $staffUser): bool;
}
