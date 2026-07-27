<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    protected WhatsAppService $whatsapp;

    public function __construct(WhatsAppService $whatsapp)
    {
        $this->whatsapp = $whatsapp;
    }

    /**
     * Webhook verification for Meta WhatsApp API.
     */
    public function verify(Request $request)
    {
        $verifyToken = config('services.whatsapp.verify_token');

        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        if ($mode && $token) {
            if ($mode === 'subscribe' && $token === $verifyToken) {
                Log::info('WhatsApp webhook verified successfully.');
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }
            Log::warning('WhatsApp webhook verification failed. Incorrect token.');
            return response('Forbidden', 403);
        }

        return response('Bad Request', 400);
    }

    /**
     * Handle inbound messages from Meta WhatsApp API.
     */
    public function handle(Request $request)
    {
        $data = $request->all();
        
        try {
            $processed = $this->whatsapp->processWebhook($data);
            if ($processed) {
                return response('EVENT_RECEIVED', 200);
            }
        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp Webhook: ' . $e->getMessage());
        }

        // Always return 200 to Meta to acknowledge receipt and prevent webhook retries
        return response('EVENT_RECEIVED', 200);
    }
}
