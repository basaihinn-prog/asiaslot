<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\LaravelBackendService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

final class LaravelBackendController extends Controller
{
    public function __construct(private LaravelBackendService $backend)
    {
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        return $this->forward(fn () => $this->backend->post('/api/mobile/login', $credentials));
    }

    public function logout(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->post('/api/mobile/logout', [], $this->token($request)));
    }

    public function user(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get('/api/mobile/profile', [], $this->token($request)));
    }

    public function balance(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get('/api/mobile/balance/refresh', [], $this->token($request)));
    }

    public function categories(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get('/api/mobile/games/categories', [], $this->token($request)));
    }

    public function games(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get(
            '/api/mobile/games',
            $request->only(['page', 'limit', 'search', 'category']),
            $this->token($request)
        ));
    }

    public function providers(Request $request, ?string $category = null): JsonResponse
    {
        $response = $this->backend->get(
            '/api/mobile/games',
            array_filter([
                'page' => max(1, (int) $request->input('page', 1)),
                'limit' => 500,
                'category' => $category,
            ], fn ($value) => $value !== null && $value !== ''),
            $this->token($request)
        );

        if (!$response->successful()) {
            return $this->proxy($response);
        }

        $providers = collect($response->json('games', []))
            ->map(fn (array $game) => $this->providerFor((string) ($game['name'] ?? '')))
            ->unique('code')
            ->sortBy('name')
            ->values();

        return response()->json([
            'providers' => $providers,
            'page' => (int) ($response->json('page') ?? 1),
            'last_page' => (int) ($response->json('last_page') ?? 1),
            'source' => 'laravelBackend',
        ]);
    }

    public function gameDetail(string $provider, string $category, Request $request): JsonResponse
    {
        $response = $this->backend->get(
            '/api/mobile/games',
            ['page' => max(1, (int) $request->input('page', 1)), 'limit' => 500, 'category' => $category],
            $this->token($request)
        );

        if (!$response->successful()) {
            return $this->proxy($response);
        }

        $games = collect($response->json('games', []))
            ->filter(fn (array $game) => $this->providerFor((string) ($game['name'] ?? ''))['code'] === $provider)
            ->values();

        return response()->json([
            'games' => $games,
            'total' => $games->count(),
            'page' => (int) ($response->json('page') ?? 1),
            'last_page' => (int) ($response->json('last_page') ?? 1),
            'source' => 'laravelBackend',
        ]);
    }

    public function game(string $name, Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get(
            '/api/mobile/games/' . rawurlencode($name),
            [],
            $this->token($request)
        ));
    }

    public function launch(string $name, Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get(
            '/api/mobile/games/' . rawurlencode($name) . '/launch',
            [],
            $this->token($request)
        ));
    }

    public function transactions(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->get(
            '/api/mobile/transactions',
            $request->only(['page', 'limit', 'type', 'from', 'to']),
            $this->token($request)
        ));
    }

    public function deposit(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->post(
            '/api/mobile/deposit/initiate',
            $request->all(),
            $this->token($request)
        ));
    }

    public function withdraw(Request $request): JsonResponse
    {
        return $this->forward(fn () => $this->backend->post(
            '/api/mobile/withdraw/request',
            $request->all(),
            $this->token($request)
        ));
    }

    private function token(Request $request): string
    {
        $token = trim((string) $request->bearerToken());
        abort_if($token === '', 401, 'Bearer token is required.');

        return $token;
    }

    private function forward(callable $operation): JsonResponse
    {
        try {
            return $this->proxy($operation());
        } catch (ConnectionException $exception) {
            report($exception);
            return response()->json(['error' => 'Laravel backend is unavailable.'], 503);
        } catch (\RuntimeException $exception) {
            report($exception);
            return response()->json(['error' => 'Laravel backend integration is not configured.'], 503);
        }
    }

    private function proxy(Response $response): JsonResponse
    {
        $payload = $response->json();
        if (!is_array($payload)) {
            $payload = ['error' => $response->successful() ? 'Invalid backend response.' : 'Backend request failed.'];
        }

        return response()->json($payload, $response->status());
    }

    private function providerFor(string $name): array
    {
        $map = (array) config('services.laravel_backend.provider_suffixes', []);
        uksort($map, fn (string $a, string $b) => strlen($b) <=> strlen($a));

        foreach ($map as $suffix => $provider) {
            if ($suffix !== '' && str_ends_with($name, $suffix)) {
                return ['code' => $suffix, 'name' => $provider];
            }
        }

        return ['code' => 'UNCLASSIFIED', 'name' => 'Unclassified'];
    }
}
