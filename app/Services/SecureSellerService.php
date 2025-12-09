<?php

declare(strict_types=1);

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

final class SecureSellerService
{
    public function __construct(
        private string $apiKey = '',
        private string $baseUrl = '',
    ) {
        $apiKey = config('secure_seller.api_key');
        $baseUrl = config('secure_seller.base_url');
        assert(is_string($apiKey));
        assert(is_string($baseUrl));

        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    /** @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function getBrands(): array
    {
        $response = $this->makeRequest('getBrands');

        /** @var array<int, array<string, mixed>> */
        $data = $response['data'] ?? [];

        return $data;
    }

    /** @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function getExpenseTypes(): array
    {
        $response = $this->makeRequest('getExpenseTypes');

        /** @var array<int, array<string, mixed>> */
        $data = $response['data'] ?? [];

        return $data;
    }

    /** @return array<int, array<string, mixed>>
     * @throws Exception
     */
    public function getProducts(): array
    {
        $response = $this->makeRequest('getProducts');

        /** @var array<int, array<string, mixed>> */
        $data = $response['data'] ?? [];

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function makeRequest(string $endpoint, array $data = []): array
    {
        try {
            /** @var Response $response */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'api-key' => $this->apiKey,
            ])->post(sprintf('%s/%s', $this->baseUrl, $endpoint), $data);

            if ($response->failed()) {
                throw new RuntimeException('API request failed with status: '.$response->status());
            }

            return $this->parseResponse($response->body());

        } catch (Exception $exception) {
            Log::error(sprintf('SecureSeller API Error [%s]: ', $endpoint).$exception->getMessage());
            throw $exception;
        }
    }

    /** @return array<string, mixed>
     * @throws Throwable
     */
    private function parseResponse(string $body): array
    {
        // Видаляємо HTTP headers якщо вони є
        if (str_contains($body, "\r\n\r\n")) {
            $parts = explode("\r\n\r\n", $body, 2);
            $body = $parts[1] ?? $body;
        }

        // Trim зайві пробіли та нові рядки
        $body = mb_trim($body);

        // Парсимо JSON
        $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        throw_unless(is_array($data), Exception::class, 'Invalid API response format');

        /** @var array<string, mixed> $data */
        return $data;
    }
}
