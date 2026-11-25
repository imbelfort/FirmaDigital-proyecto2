<?php

namespace App\Features\Pagos;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PagoFacilService
{
    protected string $baseUrl;
    protected ?string $tokenService;
    protected ?string $tokenSecret;

    public function __construct()
    {
        $this->baseUrl = env('PAGOFACIL_BASE_URL', 'https://masterqr.pagofacil.com.bo/api/services/v2');
        $this->tokenService = env('PAGOFACIL_TOKEN_SERVICE');
        $this->tokenSecret = env('PAGOFACIL_TOKEN_SECRET');
    }

    /**
     * Verifica si las credenciales de PagoFácil están configuradas en .env
     */
    public function hasCredentials(): bool
    {
        return !empty($this->tokenService) && !empty($this->tokenSecret);
    }

    /**
     * Paso 1: Autenticación /login
     * Retorna array con ['token' => ?string, 'error' => ?string]
     */
    public function login(): array
    {
        if (!$this->hasCredentials()) {
            return ['token' => null, 'error' => 'Credenciales no configuradas en .env'];
        }

        try {
            $response = Http::withHeaders([
                    'tcTokenService' => $this->tokenService,
                    'tcTokenSecret' => $this->tokenSecret,
                    'Response-Language' => 'es',
                ])
                ->timeout(12)
                ->post("{$this->baseUrl}/login");

            $data = $response->json();
            if ($response->successful()) {
                if (($data['error'] ?? 1) === 0 && !empty($data['values']['accessToken'])) {
                    return ['token' => $data['values']['accessToken'], 'error' => null];
                }
            }

            $msg = $data['message'] ?? 'Código HTTP ' . $response->status() . ': Token o secreto inválido.';
            return ['token' => null, 'error' => $msg];
        } catch (\Exception $e) {
            Log::warning('PagoFacil login exception: ' . $e->getMessage());
            return ['token' => null, 'error' => 'Error de conexión con la pasarela: ' . $e->getMessage()];
        }
    }

    /**
     * Paso 2: Listar servicios habilitados
     */
    public function listEnabledServices(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)
                ->withHeaders(['Response-Language' => 'es'])
                ->timeout(10)
                ->post("{$this->baseUrl}/list-enabled-services");

            if ($response->successful()) {
                $data = $response->json();
                return $data['values'] ?? [];
            }
        } catch (\Exception $e) {
            Log::warning('PagoFacil listEnabledServices exception: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Paso 3: Generar QR para un trámite
     */
    public function generateQr(array $params): array
    {
        if (!$this->hasCredentials()) {
            return [
                'success' => false,
                'message' => 'Credenciales de PagoFácil no configuradas. Por favor agregue PAGOFACIL_TOKEN_SERVICE y PAGOFACIL_TOKEN_SECRET en el archivo .env',
            ];
        }

        $auth = $this->login();
        $token = $auth['token'];

        if (!$token) {
            return [
                'success' => false,
                'message' => 'Respuesta oficial de PagoFácil: ' . ($auth['error'] ?? 'Fallo de autenticación con PagoFácil.'),
            ];
        }

        // Obtener el primer método de pago habilitado si no se especifica
        $paymentMethodId = $params['payment_method_id'] ?? null;
        if (!$paymentMethodId) {
            $services = $this->listEnabledServices($token);
            if (!empty($services) && isset($services[0]['paymentMethodId'])) {
                $paymentMethodId = $services[0]['paymentMethodId'];
            } else {
                $paymentMethodId = 1; // Fallback estándar
            }
        }

        try {
            $callbackUrl = url('/api/pagos/callback');
            $payload = [
                'paymentMethod' => $paymentMethodId,
                'clientName' => $params['client_name'],
                'documentType' => 1,
                'documentId' => $params['document_id'],
                'phoneNumber' => $params['phone_number'] ?? '70000000',
                'email' => $params['email'],
                'paymentNumber' => $params['payment_number'],
                'amount' => (float)$params['amount'],
                'currency' => 2, // BOB
                'clientCode' => $params['client_code'],
                // Se envían ambas variantes indicadas en la sección 8 y 5 de la guía para compatibilidad garantizada
                'callbackUrl' => $callbackUrl,
                'tcUrlCallBack' => $callbackUrl,
                'orderDetail' => [
                    [
                        'serial' => 1,
                        'product' => $params['concept'],
                        'quantity' => 1,
                        'price' => (float)$params['amount'],
                        'discount' => 0,
                        'total' => (float)$params['amount'],
                    ]
                ]
            ];

            $response = Http::withToken($token)
                ->withHeaders(['Response-Language' => 'es'])
                ->timeout(15)
                ->post("{$this->baseUrl}/generate-qr", $payload);

            if ($response->successful()) {
                $json = $response->json();
                if (($json['error'] ?? 1) === 0 && !empty($json['values'])) {
                    return [
                        'success' => true,
                        'transaction_id' => (string)($json['values']['transactionId'] ?? ''),
                        'qr_base64' => $json['values']['qrBase64'] ?? null,
                        'checkout_url' => $json['values']['checkoutUrl'] ?? null,
                        'deep_link' => $json['values']['deepLink'] ?? null,
                        'expiration_date' => $json['values']['expirationDate'] ?? now()->addDay()->toDateTimeString(),
                    ];
                }

                return [
                    'success' => false,
                    'message' => 'Respuesta de PagoFácil: ' . ($json['message'] ?? 'Error desconocido al generar QR'),
                ];
            }

            return [
                'success' => false,
                'message' => "Error HTTP {$response->status()} devuelto por la pasarela PagoFácil.",
            ];
        } catch (\Exception $e) {
            Log::error('PagoFacil generateQr live API exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error de conexión con la pasarela PagoFácil: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Paso 4: Consultar estado de transacción
     */
    public function queryTransaction(string $paymentNumber): array
    {
        if (!$this->hasCredentials()) {
            return [
                'error' => 1,
                'status' => 500,
                'message' => 'Credenciales de PagoFácil no configuradas en el entorno.',
            ];
        }

        $token = $this->login()['token'];
        if (!$token) {
            return [
                'error' => 1,
                'status' => 401,
                'message' => 'Error de autenticación con PagoFácil: Credenciales inválidas o expiradas.',
            ];
        }

        try {
            $res = Http::withToken($token)
                ->withHeaders(['Response-Language' => 'es'])
                ->timeout(10)
                ->post("{$this->baseUrl}/query-transaction", [
                    'companyTransactionId' => $paymentNumber,
                ]);

            if ($res->successful()) {
                return $res->json();
            }

            return [
                'error' => 1,
                'status' => $res->status(),
                'message' => 'No se pudo consultar la transacción en PagoFácil.',
            ];
        } catch (\Exception $e) {
            Log::warning('PagoFacil queryTransaction exception: ' . $e->getMessage());
            return [
                'error' => 1,
                'status' => 500,
                'message' => 'Error de comunicación con PagoFácil: ' . $e->getMessage(),
            ];
        }
    }
}
