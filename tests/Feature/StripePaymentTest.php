<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Stripe\ApiRequestor;
use Stripe\HttpClient\ClientInterface;
use Tests\TestCase;

class StripePaymentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['services.stripe.webhook_secret' => 'whsec_pruebas']);
        Schema::create('tramite', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_tramite');
            $table->unsignedBigInteger('id_usuario_solicitante');
            $table->unsignedBigInteger('id_tipo_tramite')->nullable();
        });
        Schema::create('tipo_tramite', function (Blueprint $table) {
            $table->id();
            $table->decimal('costo_bs', 10, 2);
        });
        Schema::create('bitacora_tramite', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tramite');
            $table->unsignedBigInteger('id_usuario');
            $table->string('accion');
            $table->text('detalle');
            $table->string('ip_origen')->nullable();
            $table->timestamp('fecha_evento');
        });
        $migration = require database_path('migrations/2026_11_22_205753_create_pago_tramite_table.php');
        $migration->up();

        DB::table('tipo_tramite')->insert(['id' => 1, 'costo_bs' => 50.75]);
        DB::table('tramite')->insert([
            'id' => 1,
            'codigo_tramite' => 'TR-1',
            'id_usuario_solicitante' => 7,
            'id_tipo_tramite' => 1,
        ]);
        DB::table('pago_tramite')->insert([
            'id_tramite' => 1,
            'payment_number' => 'PAGO-TR-1',
            'transaction_id' => 'cs_test_arancel',
            'monto' => 50,
            'moneda' => 'BOB',
            'metodo_pago' => 'STRIPE',
        ]);
    }

    public function test_stripe_webhook_credits_the_matching_payment_once(): void
    {
        $this->stripeWebhook(['payment_status' => 'unpaid'])->assertOk();
        $this->stripeWebhook(['id' => 'cs_test_otro'])->assertOk();
        $this->stripeWebhook(['amount_total' => 100])->assertConflict();
        $this->stripeWebhook([], false)->assertBadRequest();
        $this->assertDatabaseHas('pago_tramite', ['id_tramite' => 1, 'estado' => 'PENDIENTE']);

        $this->stripeWebhook()->assertOk();
        $this->stripeWebhook()->assertOk();

        $this->assertDatabaseHas('pago_tramite', ['id_tramite' => 1, 'estado' => 'COMPLETADO']);
        $this->assertDatabaseCount('bitacora_tramite', 1);
    }

    public function test_a_different_student_cannot_start_checkout(): void
    {
        $this->withSession(['usuario' => (object) ['id_usuario' => 8]])
            ->postJson('/pagos/tramite/1/stripe')
            ->assertForbidden();
    }

    public function test_only_staff_can_validate_an_arancel_in_ventanilla(): void
    {
        DB::table('pago_tramite')->delete();

        $this->withSession(['usuario' => (object) ['id_usuario' => 7, 'rol' => 'ALUMNO']])
            ->postJson('/api/pagos/tramite/1/confirmar')->assertForbidden();
        $this->withSession(['usuario' => (object) ['id_usuario' => 2, 'rol' => 'ENCARGADO']])
            ->postJson('/api/pagos/tramite/1/confirmar')->assertOk()
            ->assertJsonPath('tramite.pago.metodo_pago', 'MANUAL_VENTANILLA');
        $this->postJson('/api/pagos/tramite/1/confirmar')->assertOk();

        $this->assertDatabaseHas('pago_tramite', ['estado' => 'COMPLETADO', 'metodo_pago' => 'MANUAL_VENTANILLA']);
        $this->assertDatabaseCount('bitacora_tramite', 1);
    }

    public function test_expired_and_failed_sessions_are_recorded_without_crediting_them(): void
    {
        $this->stripeWebhook(
            ['status' => 'complete', 'payment_status' => 'unpaid'],
        )->assertOk();
        $this->assertDatabaseHas('pago_tramite', ['estado' => 'PROCESANDO', 'fecha_pago' => null]);

        $this->stripeWebhook(
            ['status' => 'complete', 'payment_status' => 'unpaid'],
            type: 'checkout.session.async_payment_failed',
        )->assertOk();
        $this->assertDatabaseHas('pago_tramite', ['estado' => 'FALLIDO', 'fecha_pago' => null]);

        DB::table('pago_tramite')->update(['transaction_id' => 'cs_test_segundo', 'estado' => 'PENDIENTE']);
        $this->stripeWebhook(['payment_status' => 'unpaid', 'status' => 'expired'], type: 'checkout.session.expired')->assertOk();
        $this->assertDatabaseHas('pago_tramite', ['estado' => 'PENDIENTE']);
        $this->stripeWebhook(
            ['id' => 'cs_test_segundo', 'payment_status' => 'unpaid', 'status' => 'expired'],
            type: 'checkout.session.expired',
        )->assertOk();
        $this->assertDatabaseHas('pago_tramite', ['estado' => 'EXPIRADO', 'fecha_pago' => null]);
    }

    public function test_card_decline_is_retryable_and_the_student_can_close_the_session(): void
    {
        config(['services.stripe.secret' => 'sk_test_pruebas']);
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->exactly(3))->method('request')->willReturnCallback(function ($method, $endpoint) {
            $this->assertStringEndsWith(
                $method === 'get' ? '/cs_test_arancel' : '/cs_test_arancel/expire',
                $endpoint
            );

            return [json_encode([
                'object' => 'checkout.session',
                'id' => 'cs_test_arancel',
                'status' => $method === 'get' ? 'open' : 'expired',
                'payment_status' => 'unpaid',
                'payment_intent' => [
                    'object' => 'payment_intent',
                    'status' => 'requires_payment_method',
                    'last_payment_error' => ['code' => 'card_declined'],
                ],
            ], JSON_THROW_ON_ERROR), 200, []];
        });
        ApiRequestor::setHttpClient($client);

        try {
            $this->withSession(['usuario' => (object) ['id_usuario' => 7]])
                ->postJson('/api/pagos/tramite/1/verificar')
                ->assertOk()->assertJsonPath('pago.estado', 'RECHAZADO');
            $this->postJson('/pagos/tramite/1/stripe/cancelar')
                ->assertOk()->assertJsonPath('pago.estado', 'CANCELADO');
            $this->stripeWebhook(
                ['payment_status' => 'unpaid', 'status' => 'expired'],
                type: 'checkout.session.expired',
            )->assertOk();
            $this->assertDatabaseHas('pago_tramite', ['estado' => 'CANCELADO', 'fecha_pago' => null]);
        } finally {
            ApiRequestor::setHttpClient(null);
        }
    }

    public function test_checkout_uses_the_database_price_and_reuses_the_session(): void
    {
        config(['services.stripe.secret' => 'sk_test_pruebas']);
        DB::table('pago_tramite')->delete();
        $url = 'https://checkout.stripe.com/c/pay/cs_test_arancel';
        $response = [json_encode([
            'object' => 'checkout.session',
            'id' => 'cs_test_arancel',
            'status' => 'open',
            'payment_status' => 'unpaid',
            'expires_at' => now()->addDay()->timestamp,
            'url' => $url,
        ], JSON_THROW_ON_ERROR), 200, []];
        $client = $this->createMock(ClientInterface::class);
        $client->expects($this->exactly(2))->method('request')
            ->willReturnCallback(function ($method, $endpoint, $headers, $params) use ($response) {
                if ($method === 'post') {
                    $this->assertStringEndsWith('/v1/checkout/sessions', $endpoint);
                    $this->assertSame(5075, $params['line_items'][0]['price_data']['unit_amount']);
                    $this->assertSame('bob', $params['line_items'][0]['price_data']['currency']);
                } else {
                    $this->assertSame('get', $method);
                    $this->assertStringEndsWith('/v1/checkout/sessions/cs_test_arancel', $endpoint);
                }

                return $response;
            });
        ApiRequestor::setHttpClient($client);

        try {
            $this->withSession(['usuario' => (object) ['id_usuario' => 7]])
                ->postJson('/pagos/tramite/1/stripe', ['monto' => 1])
                ->assertOk()->assertJson(['url' => $url]);
            $this->postJson('/pagos/tramite/1/stripe')
                ->assertOk()->assertJson(['url' => $url]);
            $this->assertDatabaseHas('pago_tramite', [
                'transaction_id' => 'cs_test_arancel',
                'monto' => 50.75,
                'estado' => 'PENDIENTE',
            ]);
            $this->assertDatabaseCount('pago_tramite', 1);
        } finally {
            ApiRequestor::setHttpClient(null);
        }
    }

    private function stripeWebhook(array $overrides = [], bool $firmaValida = true, string $type = 'checkout.session.completed')
    {
        $payload = json_encode([
            'id' => 'evt_test_arancel',
            'object' => 'event',
            'type' => $type,
            'data' => ['object' => array_replace([
                'object' => 'checkout.session',
                'id' => 'cs_test_arancel',
                'client_reference_id' => '1',
                'mode' => 'payment',
                'payment_status' => 'paid',
                'currency' => 'bob',
                'amount_total' => 5000,
            ], $overrides)],
        ], JSON_THROW_ON_ERROR);
        $timestamp = time();
        $signature = hash_hmac('sha256', "$timestamp.$payload", $firmaValida ? 'whsec_pruebas' : 'incorrecta');

        return $this->call('POST', '/api/pagos/stripe/webhook', [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_STRIPE_SIGNATURE' => "t=$timestamp,v1=$signature",
        ], $payload);
    }
}
