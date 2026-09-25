<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send an SMS notification via Africa's Talking (or log fallback)
     */
    public static function sendSms(string $phone, string $message): bool
    {
        $formattedPhone = MpesaService::formatPhoneNumber($phone);
        $username = env('AT_SMS_USERNAME', 'sandbox');
        $apiKey = env('AT_SMS_API_KEY', '');
        $senderId = env('AT_SMS_SENDER_ID', 'PEARL_INST');

        Log::info("SMS notification queued for +{$formattedPhone}: {$message}");

        if (!empty($apiKey) && $apiKey !== 'dummy_at_api_key') {
            try {
                $url = $username === 'sandbox'
                    ? 'https://api.sandbox.africastalking.com/version1/messaging'
                    : 'https://api.africastalking.com/version1/messaging';

                Http::withHeaders([
                    'apiKey' => $apiKey,
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])->asForm()->post($url, [
                    'username' => $username,
                    'to' => '+' . $formattedPhone,
                    'message' => $message,
                    'from' => $senderId,
                ]);
            } catch (\Exception $e) {
                Log::error("Africa's Talking SMS failed: " . $e->getMessage());
            }
        }

        return true;
    }

    /**
     * Send Class Postponement Alert to all enrolled cohort students
     */
    public static function notifyClassPostponed(User $trainer, $classSession, string $reason): void
    {
        $students = $classSession->cohort->enrollments()
            ->where('status', 'active')
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter();

        $msg = "PEARL INSTITUTE ALERT: Your class '{$classSession->title}' scheduled for " . 
               $classSession->scheduled_start->format('d M, h:i A') . 
               " has been postponed. Reason: {$reason}. Check your student portal for updates.";

        foreach ($students as $student) {
            if ($student->phone) {
                self::sendSms($student->phone, $msg);
            }
        }

        ActivityLog::log(
            'class.postponed_alert_sent',
            "Postponement SMS & portal alerts sent to {$students->count()} students for class '{$classSession->title}'",
            $classSession,
            ['reason' => $reason, 'student_count' => $students->count()]
        );
    }

    /**
     * Send Admission Approval Welcome
     */
    public static function notifyAdmissionApproved(User $student, string $admissionNumber, string $courseTitle): void
    {
        $msg = "Congratulations {$student->name}! Your admission to Pearl Training Institute for '{$courseTitle}' is approved. Your Admission No is {$admissionNumber}. Log in at pearlinstitute.com with your personal email.";
        if ($student->phone) {
            self::sendSms($student->phone, $msg);
        }
    }

    /**
     * Send Payment Receipt Notification
     */
    public static function notifyPaymentReceived(User $student, string $receiptNumber, float $amount, float $balance): void
    {
        $msg = "PEARL INSTITUTE: Received KES " . number_format($amount, 2) . ". Receipt #{$receiptNumber}. Remaining balance: KES " . number_format($balance, 2) . ". Download receipt in portal.";
        if ($student->phone) {
            self::sendSms($student->phone, $msg);
        }
    }
}
