<?php

namespace App\Services;

use App\Models\Communication;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class SmsService
{
    /**
     * Send an SMS to the specified phone number using settings stored in the database.
     */
    public function send(string $phone, string $message, ?int $applicationId = null, ?int $sentById = null): array
    {
        // Fetch credentials from DB system_settings (with env fallback)
        $url = SystemSetting::get('sms_hostpinnacle_url', config('services.hostpinnacle.url', 'https://smsportal.hostpinnacle.co.ke/SMSApi/send'));
        $apiKey = SystemSetting::get('sms_hostpinnacle_api_key', config('services.hostpinnacle.api_key'));
        $partnerId = SystemSetting::get('sms_hostpinnacle_partner_id', config('services.hostpinnacle.username'));
        $senderId = SystemSetting::get('sms_hostpinnacle_sender_id', config('services.hostpinnacle.sender_id', 'MUNCHIFY'));

        // Fallback to logged-in user if not provided
        $sentById = $sentById ?? (Auth::check() ? Auth::id() : null);

        // Normalize phone number
        $normalizedPhone = $this->normalizeForSms($phone);

        Log::info("SMS sending to {$normalizedPhone}: {$message}");

        $status = 'sent';
        $responseDetails = [];

        // Check if credentials exist in DB
        if (!empty($apiKey) && !empty($partnerId) && !empty($senderId)) {
            try {
                $response = Http::post($url, [
                    'username' => $partnerId,
                    'userid' => $partnerId,
                    'apikey' => $apiKey,
                    'password' => $apiKey,
                    'sender' => $senderId,
                    'mobile' => $normalizedPhone,
                    'message' => $message,
                    'msg' => $message,
                ]);

                if ($response->successful()) {
                    $responseDetails = $response->json() ?? ['raw' => $response->body()];
                    if (isset($responseDetails['status']) && strtolower($responseDetails['status']) === 'error') {
                        $status = 'failed';
                    } else {
                        $status = 'delivered';
                    }
                } else {
                    $status = 'failed';
                    $responseDetails = ['error' => $response->body()];
                    Log::error("Hostpinnacle SMS API failed: " . $response->body());
                }
            } catch (\Exception $e) {
                $status = 'failed';
                $responseDetails = ['error' => $e->getMessage()];
                Log::error("Hostpinnacle SMS exception: " . $e->getMessage());
            }
        } else {
            // Simulated Success when API credentials are not set in database
            $responseDetails = ['info' => 'Simulated SMS send. Configure Hostpinnacle credentials in Workspace Settings -> Gateway Credentials.'];
            $status = 'delivered';
        }

        // Log to DB
        if ($applicationId) {
            Communication::create([
                'application_id' => $applicationId,
                'channel' => 'sms',
                'direction' => 'outbound',
                'message' => $message,
                'sent_by' => $sentById,
                'status' => $status,
                'sent_at' => Carbon::now(),
            ]);
        }

        return [
            'success' => $status === 'delivered',
            'status' => $status,
            'details' => $responseDetails,
        ];
    }

    /**
     * Alias for send() method.
     */
    public function sendSms(string $phone, string $message, ?int $applicationId = null, ?int $sentById = null): bool
    {
        $result = $this->send($phone, $message, $applicationId, $sentById);
        return $result['success'];
    }

    /**
     * Ensure phone is in format e.g. 2547XXXXXXXX
     */
    protected function normalizeForSms(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            return '254' . substr($phone, 1);
        }
        
        if (str_starts_with($phone, '7') && strlen($phone) === 9) {
            return '254' . $phone;
        }

        return $phone;
    }
}
