<?php

namespace App\Services;

use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

/**
 * Verifies Google / Apple ID tokens (JWTs) issued to the mobile app by
 * validating their signature against the provider's published JWKS and
 * checking the issuer / audience / expiry claims.
 *
 * Returns a normalized identity: ['provider_id', 'email', 'name'].
 */
class SocialTokenVerifier
{
    private const PROVIDERS = [
        'google' => [
            'jwks' => 'https://www.googleapis.com/oauth2/v3/certs',
            'issuers' => ['https://accounts.google.com', 'accounts.google.com'],
            'config' => 'services.google.client_id',
        ],
        'apple' => [
            'jwks' => 'https://appleid.apple.com/auth/keys',
            'issuers' => ['https://appleid.apple.com'],
            'config' => 'services.apple.client_id',
        ],
    ];

    /**
     * @return array{provider_id: string, email: ?string, name: ?string}
     *
     * @throws ValidationException
     */
    public function verify(string $provider, string $idToken): array
    {
        $meta = self::PROVIDERS[$provider] ?? null;

        if ($meta === null) {
            $this->fail('مزوّد الدخول غير مدعوم.');
        }

        $allowedAudiences = array_filter(array_map(
            'trim',
            explode(',', (string) config($meta['config']))
        ));

        if (empty($allowedAudiences)) {
            $this->fail('لم تتم تهيئة مزوّد الدخول على الخادم.');
        }

        try {
            $keys = $this->jwks($provider, $meta['jwks']);
            $payload = JWT::decode($idToken, $keys);
        } catch (\Throwable $e) {
            $this->fail('تعذّر التحقق من رمز الدخول.');
        }

        if (! in_array($payload->iss ?? null, $meta['issuers'], true)) {
            $this->fail('جهة إصدار الرمز غير موثوقة.');
        }

        if (! in_array($payload->aud ?? null, $allowedAudiences, true)) {
            $this->fail('رمز الدخول غير مخصص لهذا التطبيق.');
        }

        return [
            'provider_id' => (string) $payload->sub,
            'email' => $payload->email ?? null,
            'name' => $payload->name ?? ($payload->email ?? null),
        ];
    }

    /**
     * Fetch and cache the provider's JSON Web Key Set.
     *
     * @return array<string, \Firebase\JWT\Key>
     */
    private function jwks(string $provider, string $url): array
    {
        $raw = Cache::remember("social_jwks:{$provider}", now()->addHours(6), function () use ($url) {
            $response = Http::timeout(10)->get($url);
            $response->throw();

            return $response->json();
        });

        return JWK::parseKeySet($raw);
    }

    /**
     * @throws ValidationException
     */
    private function fail(string $message): never
    {
        throw ValidationException::withMessages(['id_token' => $message]);
    }
}
