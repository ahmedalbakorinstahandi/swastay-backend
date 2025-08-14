<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DigiBankarPaymentService
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://digibankar.com/public/v2/payments';
        $this->apiKey = config('services.digibankar.key');
    }

    protected function headers(): array
    {
        return [
            'x-api-key' => $this->apiKey,
            'Accept'    => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function createRequest(array $data): array
    {
        $response = Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/request/create", $data);

        // if (!$response->successful()) {
        // }
        Log::error('DigiBankar Payment Service Error:', [
            'status' => $response->status(),
            'body' => $response->body(),
        ]);
        abort($response->status(), $response->body());

        Log::info('DigiBankar Payment Service Response:', [
            'status' => $response->status(), 
            'body' => $response->body(),
        ]);

        return $response->json();
    }

    public function find(string $term): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/request/find", [
                'term' => $term
            ])->json();
    }

    public function cancel(int $requestId): array
    {
        return Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/request/cancel", [
                'requestId' => $requestId
            ])->json();
    }

    public function list(array $filters = []): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/request/list", $filters)
            ->json();
    }
}
