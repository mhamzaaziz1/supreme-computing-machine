<?php

namespace App\Services\Ops;

/**
 * wa.me links. The app never sends WhatsApp messages itself: it prepares
 * the message and the person at the screen presses send in WhatsApp.
 *
 * Local numbers (leading 0) are rewritten with the country code from
 * constants.whatsapp_country_code, 92 by default for Pakistan.
 */
class Whatsapp
{
    public static function link(?string $mobile, string $text): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $mobile);
        if ($digits === '' || strlen($digits) < 7) {
            return null;
        }

        $countryCode = (string) config('constants.whatsapp_country_code', '92');

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (str_starts_with($digits, '0')) {
            $digits = $countryCode.substr($digits, 1);
        }

        return 'https://wa.me/'.$digits.'?text='.rawurlencode($text);
    }
}
