<?php

use App\Models\User;
use App\Services\AuthorizationService;

class TransactionControllerTest extends TestCase
{
    use Laravel\Lumen\Testing\DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        app()->bind(AuthorizationService::class, function() { // not a service provider but the target of service provider
            return new AuthorizationServiceMock();
        });
    }

    public function testCommonUserCanTransferToCommonUser()
    {
        $payer = User::factory()->create();
        $payer->wallet->deposit(300);

        $payee = User::factory()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '100.00',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $request->assertResponseStatus(200);

        $request->seeInDatabase('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 200,
        ]);

        $request->seeInDatabase('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 100,
        ]);
    }

    public function testCommonUserCanTransferToShopkeeper()
    {
        $payer = User::factory()->create();
        $payer->wallet->deposit(300);

        $payee = User::factory()->shopkeeper()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '100.00',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $request->assertResponseStatus(200);

        $request->seeInDatabase('wallets', [
            'id' => $payer->wallet->id,
            'balance' => 200,
        ]);

        $request->seeInDatabase('wallets', [
            'id' => $payee->wallet->id,
            'balance' => 100,
        ]);
    }

    public function testShopkeeperShouldNotTransfer()
    {
        $payer = User::factory()->shopkeeper()->create();
        $payer->wallet->deposit(300);

        $payee = User::factory()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '100.00',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $request->assertResponseStatus(422);

        $request->seeJson(['message' => 'Tipo de usuário inválido']);
    }

    public function testPayerShouldNotTransferToInvalidPayee()
    {
        $payer = User::factory()->shopkeeper()->create();
        $payer->wallet->deposit(300);

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => 9999999999999,
            'amount' => '100.00',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $request->assertResponseStatus(422);
    }

    public function testPayerShouldHaveEnoughBalanceToTrasfer()
    {
        $payer = User::factory()->create();
        $payer->wallet->deposit(50);

        $payee = User::factory()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '100.00',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $request->assertResponseStatus(422);

        $request->seeJson(['message' => 'Saldo insuficiente para a transferência']);
    }

    public function testTransferAmountShouldBeHigherThanZero()
    {
        $payer = User::factory()->shopkeeper()->create();
        $payer->wallet->deposit(50);

        $payee = User::factory()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '0',
        ];

        $request = $this->post(route('transaction.create'), $payload);

        $payload2 = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '-50',
        ];

        $request2 = $this->post(route('transaction.create'), $payload2);

        $request->assertResponseStatus(422);
        $request2->assertResponseStatus(422);
    }
}

class AuthorizationServiceMock extends AuthorizationService {
    public function check()
    {
        return true;
    }
}
