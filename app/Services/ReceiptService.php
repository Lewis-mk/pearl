<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ReceiptService
{
    /**
     * Generate a sequential, immutable receipt number: PRL-RCP-YYYYMM-XXXXX
     */
    public static function generateReceiptNumber(): string
    {
        return DB::transaction(function () {
            $yearMonth = date('Ym');
            $prefix = "PRL-RCP-{$yearMonth}-";

            $latestPayment = Payment::where('receipt_number', 'like', "{$prefix}%")
                ->orderByDesc('receipt_number')
                ->lockForUpdate()
                ->first();

            $nextSequence = 1;
            if ($latestPayment && preg_match('/PRL-RCP-\d{6}-(\d+)/', $latestPayment->receipt_number, $matches)) {
                $nextSequence = (int)$matches[1] + 1;
            }

            return sprintf('PRL-RCP-%s-%05d', $yearMonth, $nextSequence);
        });
    }
}
