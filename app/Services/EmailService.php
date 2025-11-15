<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailService
{
    protected $driver;
    protected $fromAddress;
    protected $fromName;

    public function __construct()
    {
        // Get email configuration from app_settings
        $emailConfig = json_decode(AppSetting::getValue('email_config', '{"driver":"smtp","from_address":"","from_name":""}'), true);

        $this->driver = $emailConfig['driver'] ?? 'smtp';
        $this->fromAddress = $emailConfig['from_address'] ?? '';
        $this->fromName = $emailConfig['from_name'] ?? AppSetting::getValue('app_name', config('app.name'));
    }

    /**
     * Send email to a single recipient
     */
    public function sendEmail(string $to, string $subject, string $body, array $options = []): array
    {
        try {
            // Validate email configuration
            if (empty($this->fromAddress)) {
                return [
                    'success' => false,
                    'error' => 'Email from address is not configured. Please configure it in App Settings.',
                    'status' => 'failed'
                ];
            }

            // Prepare email data
            $emailData = [
                'subject' => $subject,
                'body' => $body,
                'from_address' => $this->fromAddress,
                'from_name' => $this->fromName,
            ];

            // Merge any additional options (e.g., attachments, CC, BCC)
            if (isset($options['cc'])) {
                $emailData['cc'] = $options['cc'];
            }
            if (isset($options['bcc'])) {
                $emailData['bcc'] = $options['bcc'];
            }
            if (isset($options['attachments'])) {
                $emailData['attachments'] = $options['attachments'];
            }

            // Send email using Laravel's Mail facade
            Mail::send('emails.simple', $emailData, function ($message) use ($to, $emailData) {
                $message->from($emailData['from_address'], $emailData['from_name'])
                        ->to($to)
                        ->subject($emailData['subject']);

                // Add CC if provided
                if (isset($emailData['cc'])) {
                    $message->cc($emailData['cc']);
                }

                // Add BCC if provided
                if (isset($emailData['bcc'])) {
                    $message->bcc($emailData['bcc']);
                }

                // Add attachments if provided
                if (isset($emailData['attachments'])) {
                    foreach ($emailData['attachments'] as $attachment) {
                        if (is_string($attachment)) {
                            $message->attach($attachment);
                        } elseif (is_array($attachment)) {
                            $message->attach($attachment['path'], [
                                'as' => $attachment['name'] ?? null,
                                'mime' => $attachment['mime'] ?? null,
                            ]);
                        }
                    }
                }
            });

            return [
                'success' => true,
                'status' => 'sent',
                'message' => 'Email sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('Email sending error: ' . $e->getMessage(), [
                'to' => $to,
                'subject' => $subject,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Send HTML email
     */
    public function sendHtmlEmail(string $to, string $subject, string $htmlBody, array $options = []): array
    {
        try {
            if (empty($this->fromAddress)) {
                return [
                    'success' => false,
                    'error' => 'Email from address is not configured. Please configure it in App Settings.',
                    'status' => 'failed'
                ];
            }

            Mail::html($htmlBody, function ($message) use ($to, $subject, $options) {
                $message->from($this->fromAddress, $this->fromName)
                        ->to($to)
                        ->subject($subject);

                if (isset($options['cc'])) {
                    $message->cc($options['cc']);
                }
                if (isset($options['bcc'])) {
                    $message->bcc($options['bcc']);
                }
            });

            return [
                'success' => true,
                'status' => 'sent',
                'message' => 'Email sent successfully'
            ];

        } catch (\Exception $e) {
            Log::error('HTML Email sending error: ' . $e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Get the configured email driver
     */
    public function getDriver(): string
    {
        return $this->driver;
    }

    /**
     * Check if email service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->fromAddress) && !empty($this->driver);
    }

    /**
     * Send order confirmation email to customer
     */
    public function sendOrderConfirmation($order): array
    {
        if (!$order->customer->email) {
            return [
                'success' => false,
                'error' => 'Customer does not have an email address',
                'status' => 'skipped'
            ];
        }

        try {
            $subject = 'Order Confirmation - ' . $order->order_number;
            $htmlBody = view('emails.order-confirmation', compact('order'))->render();

            return $this->sendHtmlEmail($order->customer->email, $subject, $htmlBody);
        } catch (\Exception $e) {
            Log::error('Order confirmation email error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Send delivery notification email to customer
     */
    public function sendDeliveryNotification($order): array
    {
        if (!$order->customer->email) {
            return [
                'success' => false,
                'error' => 'Customer does not have an email address',
                'status' => 'skipped'
            ];
        }

        try {
            $subject = 'Your Order is Out for Delivery - ' . $order->order_number;
            $htmlBody = view('emails.delivery-notification', compact('order'))->render();

            return $this->sendHtmlEmail($order->customer->email, $subject, $htmlBody);
        } catch (\Exception $e) {
            Log::error('Delivery notification email error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Send payment confirmation email to customer
     */
    public function sendPaymentConfirmation($order, $paymentRecord): array
    {
        if (!$order->customer->email) {
            return [
                'success' => false,
                'error' => 'Customer does not have an email address',
                'status' => 'skipped'
            ];
        }

        try {
            $subject = 'Payment Confirmation - ' . $order->order_number;
            $htmlBody = view('emails.payment-confirmation', compact('order', 'paymentRecord'))->render();

            return $this->sendHtmlEmail($order->customer->email, $subject, $htmlBody);
        } catch (\Exception $e) {
            Log::error('Payment confirmation email error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Send callback reminder email to CSR
     */
    public function sendCallbackReminder($order): array
    {
        if (!$order->assignedUser || !$order->assignedUser->email) {
            return [
                'success' => false,
                'error' => 'Assigned CSR does not have an email address',
                'status' => 'skipped'
            ];
        }

        if (!$order->callback_reminder) {
            return [
                'success' => false,
                'error' => 'No callback reminder time set',
                'status' => 'skipped'
            ];
        }

        try {
            $subject = 'Callback Reminder - ' . $order->order_number . ' - ' . $order->customer->name;
            $htmlBody = view('emails.callback-reminder', compact('order'))->render();

            return $this->sendHtmlEmail($order->assignedUser->email, $subject, $htmlBody);
        } catch (\Exception $e) {
            Log::error('Callback reminder email error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }

    /**
     * Send welcome email to new user with login credentials
     */
    public function sendUserWelcomeEmail($user, $plainPassword): array
    {
        if (!$user->email) {
            return [
                'success' => false,
                'error' => 'User does not have an email address',
                'status' => 'skipped'
            ];
        }

        try {
            $subject = 'Welcome to ' . AppSetting::getValue('app_name', 'AfroWellness') . ' - Your Account Details';
            $htmlBody = view('emails.user-welcome', compact('user', 'plainPassword'))->render();

            return $this->sendHtmlEmail($user->email, $subject, $htmlBody);
        } catch (\Exception $e) {
            Log::error('User welcome email error: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'status' => 'failed'
            ];
        }
    }
}

