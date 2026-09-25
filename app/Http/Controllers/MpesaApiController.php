<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Enrollment;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\MpesaService;
use App\Services\ReceiptService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MpesaApiController extends Controller
{
    /**
     * STK Push Callback Handler (Daraja Webhook)
     */
    public function stkCallback(Request $request, MpesaService $mpesaService)
    {
        $callbackData = $request->json()->all();
        Log::info('Daraja STK Push Callback Received: ' . json_encode($callbackData));

        $stkCallback = $callbackData['Body']['stkCallback'] ?? null;
        if (!$stkCallback) {
            return response()->json(['ResultCode' => 1, 'ResultDesc' => 'Invalid Callback Body'], 400);
        }

        $merchantRequestId = $stkCallback['MerchantRequestID'] ?? null;
        $checkoutRequestId = $stkCallback['CheckoutRequestID'] ?? null;
        $resultCode = $stkCallback['ResultCode'] ?? -1;
        $resultDesc = $stkCallback['ResultDesc'] ?? '';

        $payment = Payment::where('checkout_request_id', $checkoutRequestId)->first();
        if (!$payment) {
            Log::warning("M-Pesa STK Callback: Payment with CheckoutRequestID {$checkoutRequestId} not found.");
            return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
        }

        if ($resultCode === 0) {
            // Payment Successful
            $metadataItems = $stkCallback['CallbackMetadata']['Item'] ?? [];
            $mpesaReceiptNumber = '';
            $phoneNumber = '';
            $amount = $payment->amount;

            foreach ($metadataItems as $item) {
                if ($item['Name'] === 'MpesaReceiptNumber') {
                    $mpesaReceiptNumber = $item['Value'];
                }
                if ($item['Name'] === 'PhoneNumber') {
                    $phoneNumber = (string)$item['Value'];
                }
                if ($item['Name'] === 'Amount') {
                    $amount = (float)$item['Value'];
                }
            }

            $payment->amount = $amount;
            $mpesaService->completePayment($payment, $mpesaReceiptNumber, $callbackData);

            if ($payment->student) {
                NotificationService::notifyPaymentReceived(
                    $payment->student,
                    $payment->receipt_number,
                    $payment->amount,
                    $payment->enrollment?->fee_balance ?? 0.00
                );
            }
        } else {
            // Payment Failed or Cancelled
            $payment->update([
                'status' => 'failed',
                'mpesa_raw_response' => json_encode($callbackData),
                'notes' => "M-Pesa payment cancelled or failed: {$resultDesc}",
            ]);

            ActivityLog::log(
                'payment.mpesa_failed',
                "M-Pesa STK payment failed for {$payment->receipt_number}: {$resultDesc}",
                $payment
            );
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback processed successfully']);
    }

    /**
     * C2B Validation Endpoint (Safaricom calls this to check if account is valid before taking money)
     */
    public function c2bValidation(Request $request)
    {
        $payload = $request->all();
        $accountRef = trim($payload['BillRefNumber'] ?? '');

        // Check if student exists with admission number or user ID
        $student = User::where('admission_number', $accountRef)
            ->orWhere('id', str_replace('PTI-', '', $accountRef))
            ->first();

        if ($student) {
            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        }

        // Accept anyway and reconcile in confirmation
        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * C2B Confirmation Endpoint (Safaricom calls this when payment completes via Paybill)
     */
    public function c2bConfirmation(Request $request, MpesaService $mpesaService)
    {
        $payload = $request->all();
        Log::info('Daraja C2B Confirmation Payload: ' . json_encode($payload));

        $transId = $payload['TransID'] ?? ('C2B' . strtoupper(substr(md5(uniqid()), 0, 8)));
        $transAmount = (float)($payload['TransAmount'] ?? 0);
        $billRef = trim($payload['BillRefNumber'] ?? '');
        $msisdn = (string)($payload['MSISDN'] ?? '');

        // Find matching student
        $student = User::where('admission_number', $billRef)
            ->orWhere('phone', $msisdn)
            ->orWhere('phone', MpesaService::formatPhoneNumber($msisdn))
            ->first();

        $enrollment = $student ? Enrollment::where('student_id', $student->id)->where('status', 'active')->first() : null;
        $receiptNumber = ReceiptService::generateReceiptNumber();

        $payment = Payment::create([
            'receipt_number' => $receiptNumber,
            'user_id' => $student?->id,
            'enrollment_id' => $enrollment?->id,
            'amount' => $transAmount,
            'payment_method' => 'mpesa_c2b',
            'status' => 'completed',
            'purpose' => $enrollment ? 'installment' : 'printshop',
            'mpesa_receipt_number' => $transId,
            'phone_number' => $msisdn,
            'mpesa_raw_response' => json_encode($payload),
            'notes' => "C2B Paybill payment from {$msisdn} (Ref: {$billRef})",
        ]);

        if ($enrollment) {
            $enrollment->updateFinancials();
        }

        if ($student) {
            NotificationService::notifyPaymentReceived(
                $student,
                $receiptNumber,
                $transAmount,
                $enrollment?->fee_balance ?? 0.00
            );
        }

        ActivityLog::log(
            'payment.c2b_received',
            "Received C2B Paybill payment of KES {$transAmount} (TransID: {$transId}, Receipt: {$receiptNumber})",
            $payment
        );

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Confirmation received successfully']);
    }
}
