<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected $provider;
    protected $apiKey;
    protected $apiSecret;
    protected $senderId;
    protected $apiUrl;

    public function __construct()
    {
        // Get SMS configuration from app_settings
        $smsConfig = json_decode(AppSetting::getValue('sms_config', '{"api_url":"http://sms.paynex.org/api/sendsms.php","auth_token":"","sender_id":"PEAKSYSTEMS","product_id":"dndroute"}'), true);

        // Legacy settings (for backward compatibility)
        $this->apiKey = AppSetting::getValue('sms_api_key', '');
        $this->apiSecret = AppSetting::getValue('sms_api_secret', '');
        $this->senderId = $smsConfig['sender_id'] ?? AppSetting::getValue('sms_sender_id', 'PEAKSYSTEMS');
        $this->apiUrl = $smsConfig['api_url'] ?? AppSetting::getValue('sms_api_url', 'http://sms.paynex.org/api/sendsms.php');
        $this->authToken = $smsConfig['auth_token'] ?? '';
        $this->productId = $smsConfig['product_id'] ?? 'dndroute';

        // Auto-detect provider: if Paynex config exists and has auth_token, use paynex; otherwise use configured provider or none
        if (!empty($this->authToken) && !empty($this->apiUrl) && str_contains($this->apiUrl, 'paynex')) {
            $this->provider = 'paynex';
        } else {
            $this->provider = AppSetting::getValue('sms_provider', 'none');
        }
    }

    protected $authToken;
    protected $productId;

    /**
     * Send SMS to a single recipient
     */
    public function sendSms(string $phone, string $message, string $customerName = null): array
    {
        $phone = $this->formatPhoneNumber($phone);

        switch ($this->provider) {
            case 'paynex':
                return $this->sendViaPaynex($phone, $message);
            case 'twilio':
                return $this->sendViaTwilio($phone, $message);
            case 'nexmo':
                return $this->sendViaNexmo($phone, $message);
            case 'africastalking':
                return $this->sendViaAfricasTalking($phone, $message);
            case 'textlocal':
                return $this->sendViaTextLocal($phone, $message);
            case 'smartsms':
                return $this->sendViaSmartSms($phone, $message);
            case 'custom':
                return $this->sendViaCustom($phone, $message);
            case 'none':
            default:
                return $this->sendTestMode($phone, $message, $customerName);
        }
    }

    /**
     * Send SMS via Twilio
     */
    protected function sendViaTwilio(string $phone, string $message): array
    {
        try {
            $response = Http::asForm()->withBasicAuth($this->apiKey, $this->apiSecret)
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$this->apiKey}/Messages.json", [
                    'From' => $this->senderId,
                    'To' => $phone,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['sid'] ?? null,
                    'status' => 'sent',
                    'cost' => $this->extractCostFromResponse($data),
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Twilio API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via Nexmo (Vonage)
     */
    protected function sendViaNexmo(string $phone, string $message): array
    {
        try {
            $response = Http::post('https://rest.nexmo.com/sms/json', [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'from' => $this->senderId,
                'to' => $phone,
                'text' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $messageData = $data['messages'][0] ?? null;

                if ($messageData && $messageData['status'] === '0') {
                    return [
                        'success' => true,
                        'message_id' => $messageData['message-id'] ?? null,
                        'status' => 'sent',
                        'cost' => $this->extractCostFromResponse($data),
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Nexmo error: ' . ($messageData['error-text'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => null
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Nexmo API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Nexmo SMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via Africa's Talking
     */
    protected function sendViaAfricasTalking(string $phone, string $message): array
    {
        try {
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->post('https://api.africastalking.com/version1/messaging', [
                'username' => $this->senderId,
                'to' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['SMSMessageData']['Recipients'][0]['messageId'] ?? null,
                    'status' => 'sent',
                    'cost' => $this->extractCostFromResponse($data),
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Africa\'s Talking API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Africa\'s Talking SMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via TextLocal
     */
    protected function sendViaTextLocal(string $phone, string $message): array
    {
        try {
            $response = Http::post('https://api.textlocal.in/send/', [
                'apikey' => $this->apiKey,
                'sender' => $this->senderId,
                'numbers' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'message_id' => $data['batch_id'] ?? null,
                        'status' => 'sent',
                        'cost' => $this->extractCostFromResponse($data),
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'TextLocal error: ' . ($data['errors'][0]['message'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => null
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'TextLocal API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('TextLocal SMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via SmartSMS
     */
    protected function sendViaSmartSms(string $phone, string $message): array
    {
        try {
            $response = Http::post('https://api.smartsms.com.ng/api/send', [
                'token' => $this->apiKey,
                'sender' => $this->senderId,
                'to' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['status'] === 'success') {
                    return [
                        'success' => true,
                        'message_id' => $data['message_id'] ?? null,
                        'status' => 'sent',
                        'cost' => $this->extractCostFromResponse($data),
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'SmartSMS error: ' . ($data['message'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => null
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'SmartSMS API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('SmartSMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via Paynex API
     */
    protected function sendViaPaynex(string $phone, string $message): array
    {
        try {
            // Format phone number - remove + and country code prefix if present
            $recipient = preg_replace('/^\+234/', '0', $phone); // Convert +234 to 0
            $recipient = ltrim($recipient, '+'); // Remove any remaining +

            // Validate auth token is set
            if (empty($this->authToken)) {
                return [
                    'success' => false,
                    'error' => 'SMS Authorization token is not configured. Please configure it in App Settings.',
                    'status' => 'failed',
                    'cost' => null
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->authToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, [
                'senderid' => $this->senderId,
                'recipient' => $recipient,
                'message' => $message,
                'product_id' => $this->productId,
            ]);

            // Check HTTP status and response body
            $responseBody = $response->body();
            $data = $response->json();

            Log::info('Paynex SMS API Response', [
                'status_code' => $response->status(),
                'response_body' => $responseBody,
                'recipient' => $recipient
            ]);

            if ($response->successful()) {
                // Paynex API response structure
                $apiSuccess = $data['status'] ?? $data['success'] ?? false;

                // Check if status is explicitly true (Paynex uses boolean true/false)
                if ($apiSuccess === true || $apiSuccess === 'true' || (is_string($data['message'] ?? '') && str_contains(strtolower($data['message']), 'success'))) {
                    return [
                        'success' => true,
                        'message_id' => $data['message_id'] ?? $data['id'] ?? $data['sms_id'] ?? $data['batch_id'] ?? null,
                        'status' => 'sent',
                        'cost' => $this->extractCostFromResponse($data), // Will extract from 'amount' field
                        'provider_response' => $data
                    ];
                } else {
                    // API returned 200 but with error in response
                    $errorMessage = $data['message'] ?? $data['error'] ?? 'Unknown error';
                    return [
                        'success' => false,
                        'error' => 'Paynex API error: ' . $errorMessage,
                        'status' => 'failed',
                        'cost' => null,
                        'provider_response' => $data
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Paynex API error (HTTP ' . $response->status() . '): ' . $responseBody,
                    'status' => 'failed',
                    'cost' => null,
                    'provider_response' => $data ?? []
                ];
            }
        } catch (\Exception $e) {
            Log::error('Paynex SMS Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Send SMS via Custom Provider
     */
    protected function sendViaCustom(string $phone, string $message): array
    {
        try {
            $response = Http::post($this->apiUrl, [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'sender' => $this->senderId,
                'to' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message_id' => $data['message_id'] ?? $data['id'] ?? null,
                    'status' => 'sent',
                    'cost' => $this->extractCostFromResponse($data),
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Custom provider API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => null
                ];
            }
        } catch (\Exception $e) {
            Log::error('Custom SMS Provider Error: ' . $e->getMessage());
            return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'status' => 'failed',
                    'cost' => null
            ];
        }
    }

    /**
     * Test mode - simulate SMS sending
     */
    protected function sendTestMode(string $phone, string $message, string $customerName = null): array
    {
        Log::info("TEST MODE SMS: To {$phone}, Message: {$message}");

        return [
            'success' => true,
            'message_id' => 'TEST_' . uniqid(),
            'status' => 'sent',
            'cost' => 0,
            'provider_response' => [
                'test_mode' => true,
                'message' => 'SMS sent in test mode - no actual SMS was sent'
            ]
        ];
    }

    /**
     * Extract cost from API response
     * Tries common cost field names from various SMS providers
     */
    protected function extractCostFromResponse(array $data): ?float
    {
        // Try common cost field names
        $costFields = ['cost', 'price', 'amount', 'charge', 'credit', 'credits', 'balance_used', 'balance_deducted'];

        foreach ($costFields as $field) {
            if (isset($data[$field])) {
                $cost = (float) $data[$field];
                if ($cost > 0) {
                    return $cost;
                }
            }
        }

        // Try nested structures (e.g., data.message.cost)
        if (isset($data['message']) && is_array($data['message'])) {
            foreach ($costFields as $field) {
                if (isset($data['message'][$field])) {
                    $cost = (float) $data['message'][$field];
                    if ($cost > 0) {
                        return $cost;
                    }
                }
            }
        }

        // If no cost found, return null (not 0, as 0 might indicate actual cost)
        return null;
    }

    /**
     * Format phone number for international format
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add country code if missing (assuming Nigeria +234)
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            $phone = '234' . substr($phone, 1);
        } elseif (strlen($phone) === 11 && substr($phone, 0, 1) === '0') {
            $phone = '234' . substr($phone, 1);
        } elseif (strlen($phone) === 10 && substr($phone, 0, 1) !== '0') {
            $phone = '234' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Get the configured SMS provider
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * Check if SMS provider is configured
     */
    public function isConfigured(): bool
    {
        return $this->provider !== 'none' && !empty($this->apiKey);
    }
}
