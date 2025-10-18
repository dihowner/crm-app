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
    protected $costPerMessage;
    protected $apiUrl;

    public function __construct()
    {
        $this->provider = AppSetting::getValue('sms_provider', 'none');
        $this->apiKey = AppSetting::getValue('sms_api_key', '');
        $this->apiSecret = AppSetting::getValue('sms_api_secret', '');
        $this->senderId = AppSetting::getValue('sms_sender_id', '');
        $this->costPerMessage = (float) AppSetting::getValue('sms_cost_per_message', '0.50');
        $this->apiUrl = AppSetting::getValue('sms_api_url', '');
    }

    /**
     * Send SMS to a single recipient
     */
    public function sendSms(string $phone, string $message, string $customerName = null): array
    {
        $phone = $this->formatPhoneNumber($phone);

        switch ($this->provider) {
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
                    'cost' => $this->costPerMessage,
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Twilio API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Twilio SMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
                        'cost' => $this->costPerMessage,
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'Nexmo error: ' . ($messageData['error-text'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => 0
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'Nexmo API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Nexmo SMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
                    'cost' => $this->costPerMessage,
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Africa\'s Talking API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Africa\'s Talking SMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
                        'cost' => $this->costPerMessage,
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'TextLocal error: ' . ($data['errors'][0]['message'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => 0
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'TextLocal API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('TextLocal SMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
                        'cost' => $this->costPerMessage,
                        'provider_response' => $data
                    ];
                } else {
                    return [
                        'success' => false,
                        'error' => 'SmartSMS error: ' . ($data['message'] ?? 'Unknown error'),
                        'status' => 'failed',
                        'cost' => 0
                    ];
                }
            } else {
                return [
                    'success' => false,
                    'error' => 'SmartSMS API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('SmartSMS Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
                    'cost' => $this->costPerMessage,
                    'provider_response' => $data
                ];
            } else {
                return [
                    'success' => false,
                    'error' => 'Custom provider API error: ' . $response->body(),
                    'status' => 'failed',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            Log::error('Custom SMS Provider Error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed',
                'cost' => 0
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
