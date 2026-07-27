<?php

namespace App\Services;

use App\Models\Communication;
use App\Models\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class WhatsAppService
{
    protected ?string $url;
    protected ?string $token;
    protected ?string $phoneNumberId;
    protected ?string $verifyToken;

    public function __construct()
    {
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
        $this->token = config('services.whatsapp.token');
        $this->verifyToken = config('services.whatsapp.verify_token');
        $this->url = $this->phoneNumberId 
            ? "https://graph.facebook.com/v20.0/{$this->phoneNumberId}/messages" 
            : null;
    }

    /**
     * Send a WhatsApp message.
     */
    public function send(string $phone, string $message, ?int $applicationId = null, ?int $sentById = null): array
    {
        $sentById = $sentById ?? (Auth::check() ? Auth::id() : null);
        $normalizedPhone = $this->normalizeForWhatsApp($phone);

        Log::info("WhatsApp sending to {$normalizedPhone}: {$message}");

        $status = 'sent';
        $responseDetails = [];

        if ($this->url && $this->token) {
            try {
                $response = Http::withToken($this->token)->post($this->url, [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $normalizedPhone,
                    'type' => 'text',
                    'text' => [
                        'body' => $message
                    ]
                ]);

                if ($response->successful()) {
                    $responseDetails = $response->json();
                    $status = 'delivered';
                } else {
                    $status = 'failed';
                    $responseDetails = ['error' => $response->body()];
                    Log::error("Meta WhatsApp API failed: " . $response->body());
                }
            } catch (\Exception $e) {
                $status = 'failed';
                $responseDetails = ['error' => $e->getMessage()];
                Log::error("Meta WhatsApp exception: " . $e->getMessage());
            }
        } else {
            // Simulated Success when API credentials are not set
            $responseDetails = ['info' => 'Simulated WhatsApp send. Configure services.whatsapp in config/services.php.'];
            $status = 'delivered';
        }

        // Log to DB
        if ($applicationId) {
            Communication::create([
                'application_id' => $applicationId,
                'channel' => 'whatsapp',
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
     * Process incoming Webhook data from Meta.
     */
    public function processWebhook(array $data): bool
    {
        Log::info('WhatsApp Webhook data received: ' . json_encode($data));

        // Basic verification that this is a WhatsApp message webhook
        if (!isset($data['entry'][0]['changes'][0]['value']['messages'][0])) {
            return false;
        }

        $messageData = $data['entry'][0]['changes'][0]['value']['messages'][0];
        $fromRaw = $messageData['from']; // Format is typically "254712345678"
        $body = $messageData['text']['body'] ?? '';

        if (empty($fromRaw) || empty($body)) {
            return false;
        }

        // Search for matching application by candidate phone number
        // Check for multiple formats: international with/without +, local with leading 0
        $fromNormalized = $this->normalizeForWhatsApp($fromRaw);
        $possiblePhones = [
            $fromNormalized,
            '+' . $fromNormalized,
            '0' . substr($fromNormalized, 3) // Convert 254712345678 to 0712345678
        ];

        $application = Application::whereIn('phone', $possiblePhones)
            ->orWhere(function ($query) use ($fromNormalized) {
                // Check if stored phone ends with the last 9 digits of fromNormalized
                $query->where('phone', 'like', '%' . substr($fromNormalized, -9));
            })
            ->orderBy('id', 'desc')
            ->first();

        if ($application) {
            // Log as inbound communication
            Communication::create([
                'application_id' => $application->id,
                'channel' => 'whatsapp',
                'direction' => 'inbound',
                'message' => $body,
                'sent_by' => null, // Inbound from candidate
                'status' => 'delivered',
                'sent_at' => Carbon::now(),
            ]);

            Log::info("Inbound WhatsApp message logged for Application ID {$application->id}");
            return true;
        }

        Log::warning("No matching application found for WhatsApp sender: {$fromRaw}");
        return false;
    }

    /**
     * Ensure phone has only digits and starts with 254 (no +)
     */
    protected function normalizeForWhatsApp(string $phone): string
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
