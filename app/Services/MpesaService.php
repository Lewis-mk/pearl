<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MpesaService
{
    protected string $env;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $passkey;
    protected string $shortcode;
    protected string $callbackUrl;

    public function __construct()
    {
        $this->env = env('DARAJA_ENVIRONMENT', 'sandbox');
        $this->consumerKey = env('DARAJA_CONSUMER_KEY', 'sandbox_key');
        $this->consumerSecret = env('DARAJA_CONSUMER_SECRET', 'sandbox_secret');
        $this->passkey = env('DARAJA_PASSKEY', 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919');
        $this->shortcode = env('DARAJA_BUSINESS_SHORTCODE', '174379');
        $this->callbackUrl = env('DARAJA_CALLBACK_URL', url('/api/mpesa/callback'));
    }

    /**
     * Format phone number to 2547XXXXXXXX
     */
    public static function formatPhoneNumber(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($clean, '0')) {
            return '254' . substr($clean, 1);
        }
        if (str_starts_with($clean, '7') || str_starts_with($clean, '1')) {
            return '254' . $clean;
        }
        if (str_starts_with($clean, '254')) {
            return $clean;
        }
        return $clean;
    }

    /**
     * Initiate STK Push
     */
    public function initiateStkPush(User $student, Enrollment $enrollment, float $amount, string $phone, string $purpose = 'installment'): array
    {
        $formattedPhone = self::formatPhoneNumber($phone);
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);
        $receiptNumber = ReceiptService::generateReceiptNumber();
        $accountReference = $student->admission_number ?? ('PTI-' . $student->id);

        // Create pending payment record
        $payment = Payment::create([
            'receipt_number' => $receiptNumber,
            'user_id' => $student->id,
            'enrollment_id' => $enrollment->id,
            'amount' => $amount,
            'payment_method' => 'mpesa_stk',
            'status' => 'pending',
            'purpose' => $purpose,
            'phone_number' => $formattedPhone,
            'notes' => "STK Push initiated for {$purpose} on {$enrollment->course->title}",
        ]);

        // In sandbox or production mode:
        if ($this->consumerKey !== 'sandbox_consumer_key_here' && $this->consumerKey !== 'sandbox_key') {
            try {
                $token = $this->generateAccessToken();
                $url = $this->env === 'production'
                    ? 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest'
                    : 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest';

                $response = Http::withToken($token)->post($url, [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) $amount,
                    'PartyA' => $formattedPhone,
                    'PartyB' => $this->shortcode,
                    'PhoneNumber' => $formattedPhone,
                    'CallBackURL' => $this->callbackUrl,
                    'AccountReference' => substr($accountReference, 0, 12),
                    'TransactionDesc' => "Pearl Institute {$purpose}",
                ]);

                $resData = $response->json();
                $payment->update([
                    'merchant_request_id' => $resData['MerchantRequestID'] ?? null,
                    'checkout_request_id' => $resData['CheckoutRequestID'] ?? null,
                    'mpesa_raw_response' => json_encode($resData),
                ]);

                return [
                    'success' => true,
                    'payment' => $payment,
                    'message' => 'STK push prompt sent to your phone. Enter your M-Pesa PIN to complete payment.',
                    'checkout_request_id' => $resData['CheckoutRequestID'] ?? null,
                ];
            } catch (\Exception $e) {
                Log::error("M-Pesa STK Push error: " . $e->getMessage());
            }
        }

        // Sandbox simulated STK push fallback for instant testing:
        $simulatedTransId = 'SND' . strtoupper(substr(md5(uniqid()), 0, 8));
        $payment->update([
            'checkout_request_id' => 'ws_CO_' . time() . '_' . rand(1000, 9999),
            'mpesa_raw_response' => json_encode(['Simulated' => true, 'ResponseCode' => '0']),
        ]);

        return [
            'success' => true,
            'payment' => $payment,
            'simulated' => true,
            'message' => "STK Push simulated on sandbox for {$formattedPhone}. You can complete this payment instantly.",
        ];
    }

    /**
     * Complete and verify an M-Pesa payment (Callback or Simulated confirmation)
     */
    public function completePayment(Payment $payment, string $mpesaReceiptNumber, array $rawResponse = []): Payment
    {
        $payment->update([
            'status' => 'completed',
            'mpesa_receipt_number' => $mpesaReceiptNumber,
            'mpesa_raw_response' => json_encode($rawResponse),
        ]);

        if ($payment->enrollment) {
            $payment->enrollment->updateFinancials();
            
            // If enrollment was pending approval and deposit is paid, keep or update status
            if ($payment->enrollment->status === 'pending_approval' && $payment->enrollment->fee_paid >= $payment->enrollment->course->deposit_required) {
                // Enrollment deposit fulfilled
            }
        }

        ActivityLog::log(
            'payment.mpesa_received',
            "Received M-Pesa payment of KES {$payment->amount} (Receipt: {$payment->receipt_number}, TransID: {$mpesaReceiptNumber})",
            $payment,
            ['receipt_number' => $payment->receipt_number, 'amount' => $payment->amount, 'trans_id' => $mpesaReceiptNumber]
        );

        return $payment;
    }

    /**
     * Record cash payment with unified ledger and sequential receipt
     */
    public function recordCashPayment(
        User $collectedBy,
        ?User $student,
        ?Enrollment $enrollment,
        float $amount,
        string $purpose = 'installment',
        ?string $notes = null
    ): Payment {
        $receiptNumber = ReceiptService::generateReceiptNumber();

        $payment = Payment::create([
            'receipt_number' => $receiptNumber,
            'user_id' => $student?->id,
            'enrollment_id' => $enrollment?->id,
            'amount' => $amount,
            'payment_method' => 'cash',
            'status' => 'completed',
            'purpose' => $purpose,
            'collected_by_user_id' => $collectedBy->id,
            'notes' => $notes ?? "Cash collected by {$collectedBy->name} ({$collectedBy->roles->pluck('display_name')->implode(', ')})",
        ]);

        if ($enrollment) {
            $enrollment->updateFinancials();
        }

        ActivityLog::log(
            'payment.cash_collected',
            "Collected cash payment of KES {$amount} from " . ($student?->name ?? 'Walk-in Customer') . " (Receipt: {$receiptNumber})",
            $payment,
            ['receipt_number' => $receiptNumber, 'amount' => $amount, 'collector_id' => $collectedBy->id]
        );

        return $payment;
    }

    /**
     * Record manual/offline M-Pesa confirmation (Fallback during outage)
     */
    public function recordManualMpesaPayment(
        User $recordedBy,
        ?User $student,
        ?Enrollment $enrollment,
        float $amount,
        string $mpesaReceiptNumber,
        string $phone,
        string $purpose = 'installment',
        ?string $notes = null
    ): Payment {
        $receiptNumber = ReceiptService::generateReceiptNumber();

        $payment = Payment::create([
            'receipt_number' => $receiptNumber,
            'user_id' => $student?->id,
            'enrollment_id' => $enrollment?->id,
            'amount' => $amount,
            'payment_method' => 'mpesa_manual',
            'status' => 'completed',
            'purpose' => $purpose,
            'mpesa_receipt_number' => strtoupper(trim($mpesaReceiptNumber)),
            'phone_number' => self::formatPhoneNumber($phone),
            'collected_by_user_id' => $recordedBy->id,
            'notes' => $notes ?? "Manual M-Pesa verified by {$recordedBy->name} (Outage fallback)",
        ]);

        if ($enrollment) {
            $enrollment->updateFinancials();
        }

        ActivityLog::log(
            'payment.mpesa_manual_recorded',
            "Recorded manual M-Pesa payment of KES {$amount} TransID {$mpesaReceiptNumber} (Receipt: {$receiptNumber})",
            $payment,
            ['receipt_number' => $receiptNumber, 'amount' => $amount, 'mpesa_code' => $mpesaReceiptNumber]
        );

        return $payment;
    }

    protected function generateAccessToken(): string
    {
        $url = $this->env === 'production'
            ? 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'
            : 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';

        $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)->get($url);
        return $response->json()['access_token'] ?? '';
    }
}
