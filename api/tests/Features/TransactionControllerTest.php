<?php

use App\Models\User;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

class TransactionControllerTest extends TestCase
{
    use Laravel\Lumen\Testing\DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], 'Hello, World'),
            new \Exception('Error Communicating with Server')
        ]);

        $handlerStack = HandlerStack::create($mock);
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

        $request->assertResponseStatus(401);
        $request2->assertResponseStatus(401);

        $request->seeJson(['message' => 'Valor de transferência inválido']);
        $request2->seeJson(['message' => 'Valor de transferência inválido']);
    }
}
