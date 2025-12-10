<?php

declare(strict_types=1);

namespace App\Helpers;

final class CountryCodeConverter
{
    private const COUNTRY_MAP = [
        'United States' => 'US',
        'US' => 'US',
        'USA' => 'US',
        'Canada' => 'CA',
        'United Kingdom' => 'GB',
        'UK' => 'GB',
        'Australia' => 'AU',
        'Germany' => 'DE',
        'France' => 'FR',
        'Italy' => 'IT',
        'Spain' => 'ES',
        'Netherlands' => 'NL',
        'Belgium' => 'BE',
        'Switzerland' => 'CH',
        'Austria' => 'AT',
        'Poland' => 'PL',
        'Sweden' => 'SE',
        'Norway' => 'NO',
        'Denmark' => 'DK',
        'Finland' => 'FI',
        'Ireland' => 'IE',
        'Portugal' => 'PT',
        'Greece' => 'GR',
        'Czech Republic' => 'CZ',
        'Hungary' => 'HU',
        'Romania' => 'RO',
        'Bulgaria' => 'BG',
        'Croatia' => 'HR',
        'Slovakia' => 'SK',
        'Slovenia' => 'SI',
        'Lithuania' => 'LT',
        'Latvia' => 'LV',
        'Estonia' => 'EE',
        'Mexico' => 'MX',
        'Brazil' => 'BR',
        'Argentina' => 'AR',
        'Chile' => 'CL',
        'Colombia' => 'CO',
        'Peru' => 'PE',
        'Venezuela' => 'VE',
        'Japan' => 'JP',
        'China' => 'CN',
        'South Korea' => 'KR',
        'India' => 'IN',
        'Singapore' => 'SG',
        'Malaysia' => 'MY',
        'Thailand' => 'TH',
        'Indonesia' => 'ID',
        'Philippines' => 'PH',
        'Vietnam' => 'VN',
        'New Zealand' => 'NZ',
        'South Africa' => 'ZA',
        'Israel' => 'IL',
        'Turkey' => 'TR',
        'Russia' => 'RU',
        'Ukraine' => 'UA',
    ];

    public static function convert(?string $countryName): ?string
    {
        if (in_array($countryName, [null, '', '0'], true)) {
            return null;
        }

        // If already a 2-letter code, return as is
        if (mb_strlen($countryName) === 2 && ctype_upper($countryName)) {
            return $countryName;
        }

        // Try exact match
        if (isset(self::COUNTRY_MAP[$countryName])) {
            return self::COUNTRY_MAP[$countryName];
        }

        // Try case-insensitive match
        foreach (self::COUNTRY_MAP as $name => $code) {
            if (strcasecmp($name, $countryName) === 0) {
                return $code;
            }
        }

        // Return original if no match found
        return $countryName;
    }
}
