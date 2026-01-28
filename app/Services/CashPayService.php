<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CashPayService
{
    /**
     * Récupère et stocke en cache le token OAuth
     */
    private function getAccessToken(): string
    {
        return Cache::remember('semoa_token', 3500, function () {
            $res = Http::post(config('services.cashpay.url') . '/auth', [
                'username' => config('services.cashpay.username'),
                'password' => config('services.cashpay.password'),
                'client_id' => config('services.cashpay.client_id'),
                'client_secret' => config('services.cashpay.client_secret'),
            ])->throw()->json();

            return $res['access_token'];
        });
    }

    /**
     * Prépare les headers signés pour les requêtes CashPay
     */
    private function signedHeaders(): array
    {
        $login = config('services.cashpay.login');
        $apiKey = config('services.cashpay.apikey');
        $apiReference = config('services.cashpay.apireference');
        $salt = random_int(100000, 999999999);
        $apiSecure = hash('sha256', $login . $apiKey . $salt);

        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
            'login' => $login,
            'apireference' => $apiReference,
            'salt' => $salt,
            'apisecure' => $apiSecure,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Crée une commande CashPay
     */
    public function createOrder(int $amount, string $phone, int $gatewayId)
    {
        $payload = [
            "amount" => $amount,
            // "currency" => "XOF",
            "client" => ["phone" => $phone],
            "gateway_id" => $gatewayId,
            "callback_url" => route('semoa.callback'),
        ];

        return Http::withHeaders($this->signedHeaders())
            ->post(config('services.cashpay.url') . '/orders', $payload)
            ->throw()
            ->json();
    }

    /**
     * Récupère les gateways disponibles
     */
    public function getGateways()
    {
        return Http::withHeaders($this->signedHeaders())
            ->get(config('services.cashpay.url') . '/gateways')
            ->throw()
            ->json();
    }
}
