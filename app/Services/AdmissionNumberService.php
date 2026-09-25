<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdmissionNumberService
{
    /**
     * Generate a sequential, unique admission number: PTI/YYYY/XXXXX
     */
    public static function generateNextAdmissionNumber(): string
    {
        return DB::transaction(function () {
            $currentYear = date('Y');
            $prefix = "PTI/{$currentYear}/";

            // Find highest current sequence number for this year
            $latestStudent = User::where('admission_number', 'like', "{$prefix}%")
                ->orderByDesc('admission_number')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($latestStudent && preg_match('/PTI\/\d{4}\/(\d+)/', $latestStudent->admission_number, $matches)) {
                $nextSequence = (int)$matches[1] + 1;
            }

            return sprintf('PTI/%s/%05d', $currentYear, $nextSequence);
        });
    }
}
