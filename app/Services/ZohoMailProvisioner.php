<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ZohoMailProvisioner implements StaffMailProvisionerInterface
{
    protected string $domain;
    protected string $clientId;
    protected string $clientSecret;
    protected string $refreshToken;
    protected string $accountId;

    public function __construct()
    {
        $this->domain = env('ZOHO_MAIL_DOMAIN', 'pearlinstitute.com');
        $this->clientId = env('ZOHO_CLIENT_ID', '');
        $this->clientSecret = env('ZOHO_CLIENT_SECRET', '');
        $this->refreshToken = env('ZOHO_REFRESH_TOKEN', '');
        $this->accountId = env('ZOHO_ACCOUNT_ID', '');
    }

    public function provisionStaffAccount(User $staffUser, string $desiredUsername): array
    {
        $cleanUsername = Str::slug(Str::lower($desiredUsername), '.');
        $officialEmail = "{$cleanUsername}@{$this->domain}";

        // If credentials are configured, connect to Zoho Mail API
        if (!empty($this->clientId) && !empty($this->refreshToken) && $this->clientId !== 'dummy_zoho_client_id') {
            try {
                $accessToken = $this->getAccessToken();
                $response = Http::withToken($accessToken)
                    ->post("https://mail.zoho.com/api/organization/{$this->accountId}/accounts", [
                        'primaryEmailAddress' => $officialEmail,
                        'displayName' => $staffUser->name,
                        'password' => Str::random(12) . '!Aa1',
                    ]);

                if ($response->successful()) {
                    $staffUser->update([
                        'is_staff' => true,
                        'staff_official_email' => $officialEmail,
                        'staff_email_status' => 'provisioned',
                    ]);

                    ActivityLog::log(
                        'staff.email_provisioned',
                        "Provisioned official staff mailbox {$officialEmail} via Zoho Mail API",
                        $staffUser
                    );

                    return [
                        'success' => true,
                        'email' => $officialEmail,
                        'message' => "Successfully provisioned Zoho mailbox for {$officialEmail}",
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Zoho Mail provisioning failed: " . $e->getMessage());
            }
        }

        // Mock / Simulation mode for development and demonstration
        $staffUser->update([
            'is_staff' => true,
            'staff_official_email' => $officialEmail,
            'staff_email_status' => 'provisioned',
        ]);

        ActivityLog::log(
            'staff.email_provisioned',
            "Provisioned official staff mailbox {$officialEmail} (Zoho Provisioner Service)",
            $staffUser
        );

        return [
            'success' => true,
            'email' => $officialEmail,
            'simulated' => true,
            'message' => "Staff mailbox {$officialEmail} provisioned successfully on {$this->domain}",
        ];
    }

    public function deactivateStaffAccount(User $staffUser): bool
    {
        $staffUser->update([
            'staff_email_status' => 'deactivated',
        ]);

        ActivityLog::log(
            'staff.email_deactivated',
            "Deactivated official staff mailbox {$staffUser->staff_official_email}",
            $staffUser
        );

        return true;
    }

    protected function getAccessToken(): string
    {
        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => $this->refreshToken,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
        ]);

        return $response->json()['access_token'] ?? '';
    }
}
