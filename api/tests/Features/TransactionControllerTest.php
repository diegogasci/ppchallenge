<?php

use App\Models\User;
use Illuminate\Http\Response;
use App\Events\TransactionSuccess;
use Illuminate\Support\Facades\Event;
use App\Services\AuthorizationService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use App\Listeners\SendTransactionNotification;

class TransactionControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        app()->bind(AuthorizationService::class, function() {
            return new AuthorizationServiceMock();
        });

        app()->bind(SendTransactionNotification::class, function() {
            return new SendTransactionNotificationMock();
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

        $request->assertResponseStatus(Response::HTTP_OK);

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

        $request->assertResponseStatus(Response::HTTP_OK);

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

        $request->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

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

        $request->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
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

        $request->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

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

        $request->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $request2->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testSuccessfulTransferCorrectlyDispatchEvent()
    {
        $payer = User::factory()->create();
        $payer->wallet->deposit(300);

        $payee = User::factory()->create();

        $payload = [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'amount' => '100.00',
        ];

        Event::fake();

        $this->post(route('transaction.create'), $payload);
        $transaction = $this->response->getData()->transaction;

        Event::assertDispatched(function (TransactionSuccess $event) use ($transaction) {
            return $event->transaction->id === $transaction->id;
        });
    }


}

/**
 * AuthorizationServiceMock mocking the external authorization check response
 */
class AuthorizationServiceMock extends AuthorizationService {
    public function check()
    {
        return true;
    }
}

/**
 * AuthorizationServiceMock mocking the external authorization check response
 */
class SendTransactionNotificationMock extends SendTransactionNotification {
    public function handle(TransactionSuccess $event)
    {
        $transaction = $event->transaction;
    }
}

