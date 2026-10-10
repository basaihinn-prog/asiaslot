<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

final class LaravelBackendService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('services.laravel_backend.url'), '/');
    }

    public function get(string $endpoint, array $query = [], ?string $token = null): Response
    {
        return $this->client($token)->get($this->url($endpoint), $query);
    }

    public function post(string $endpoint, array $payload = [], ?string $token = null): Response
    {
        return $this->client($token)->post($this->url($endpoint), $payload);
    }

    public function delete(string $endpoint, array $payload = [], ?string $token = null): Response
    {
        return $this->client($token)->delete($this->url($endpoint), $payload);
    }

    private function client(?string $token): PendingRequest
    {
        $request = Http::acceptJson()
            ->asJson()
            ->connectTimeout((int) config('services.laravel_backend.connect_timeout', 5))
            ->timeout((int) config('services.laravel_backend.timeout', 15));

        if ($token) {
            $request = $request->withToken($token);
        }

        $apiKey = trim((string) config('services.laravel_backend.api_key'));
        if ($apiKey !== '') {
            $request = $request->withHeaders(['X-API-Key' => $apiKey]);
        }

        return $request;
    }

    private function url(string $endpoint): string
    {
        if ($this->baseUrl === '') {
            throw new \RuntimeException('LARAVEL_BACKEND_URL is not configured.');
        }

        return $this->baseUrl . '/' . ltrim($endpoint, '/');
    }
}
