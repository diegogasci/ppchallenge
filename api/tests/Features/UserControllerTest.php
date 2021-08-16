<?php

use App\Models\User;
use Faker\Generator as Faker;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = new Faker();
    }

    public function testUserIsCreatedSuccessfully()
    {
        $user = User::factory()->make();

        $payload = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '123456',
            'document_type' => $user->document_type,
            'document' => $user->document,
        ];

        $request = $this->post(route('user.create'), $payload);

        $request->assertResponseStatus(200);
    }

    public function testUserIsDeletedSuccessfully()
    {
        $user = User::factory()->create();

        $request = $this->delete(route('user.delete', ['userId' => $user->id]));

        $request->assertResponseStatus(204);
    }

    public function testUserIsUpdatedSuccessfully()
    {
        $user = User::factory()->create();
        $newEmail = 'anotheremail@gmail.com';

        $payload = [
            'email' => $newEmail,
        ];

        $request = $this->patch(route('user.update', ['userId' => $user->id]), $payload);

        $request->assertResponseStatus(200);

        $request->seeInDatabase('users', [
            'id' => $user->id,
            'email' => $newEmail,
        ]);
    }

    public function testUserIsShownCorrectly()
    {
        $user = User::factory()->create();

        $request = $this->patch(route('user.show', ['userId' => $user->id]));

        $request->assertResponseStatus(200);

        $request->seeJsonStructure([
            'id',
            'name',
            'email',
            'email_verified_at',
            'document_type',
            'document',
            'created_at',
            'updated_at',
        ]);
    }
}
