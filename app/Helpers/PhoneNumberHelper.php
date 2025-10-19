<?php

namespace App\Helpers;

class PhoneNumberHelper
{
    /**
     * Convert phone number to country code format
     *
     * @param string|null $phoneNumber
     * @return string|null
     */
    public static function convertToCountryCode($phoneNumber)
    {
        if (empty($phoneNumber)) {
            return null;
        }

        // Remove all non-numeric characters
        $cleanNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        // If already starts with country code, return as is
        if (str_starts_with($cleanNumber, '234')) {
            return '+' . $cleanNumber;
        }

        // If starts with 0, replace with +234
        if (str_starts_with($cleanNumber, '0')) {
            return '+234' . substr($cleanNumber, 1);
        }

        // If starts with 234 but no +, add +
        if (str_starts_with($cleanNumber, '234')) {
            return '+' . $cleanNumber;
        }

        // If it's a 10-digit number (Nigerian mobile), add +234
        if (strlen($cleanNumber) === 10) {
            return '+234' . $cleanNumber;
        }

        // If it's an 11-digit number starting with 0, replace 0 with +234
        if (strlen($cleanNumber) === 11 && str_starts_with($cleanNumber, '0')) {
            return '+234' . substr($cleanNumber, 1);
        }

        // If it's a 13-digit number starting with 234, add +
        if (strlen($cleanNumber) === 13 && str_starts_with($cleanNumber, '234')) {
            return '+' . $cleanNumber;
        }

        // For any other format, return as is with + if not present
        if (!str_starts_with($phoneNumber, '+')) {
            return '+' . $cleanNumber;
        }

        return $phoneNumber;
    }
}
