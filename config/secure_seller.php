<?php

declare(strict_types=1);

/**
 * @return array{
 *     api_key: string,
 *     base_url: string
 * }
 */
return [
'api_key' => env('SECURE_SELLER_API_KEY'),
'base_url' => env('SECURE_SELLER_API_URL'),
];
