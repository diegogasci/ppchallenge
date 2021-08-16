<?php

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

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

        $request->assertResponseStatus(Response::HTTP_OK);
    }

    public function testUserIsDeletedSuccessfully()
    {
        $user = User::factory()->create();

        $request = $this->delete(route('user.delete', ['userId' => $user->id]));

        $request->assertResponseStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUserIsUpdatedSuccessfully()
    {
        $user = User::factory()->create();
        $newEmail = 'anotheremail@gmail.com';

        $payload = [
            'email' => $newEmail,
        ];

        $request = $this->patch(route('user.update', ['userId' => $user->id]), $payload);

        $request->assertResponseStatus(Response::HTTP_OK);

        $request->seeInDatabase('users', [
            'id' => $user->id,
            'email' => $newEmail,
        ]);
    }

    public function testUserIsShownCorrectly()
    {
        $user = User::factory()->create();

        $request = $this->get(route('user.show', ['userId' => $user->id]));

        $request->assertResponseStatus(Response::HTTP_OK);

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
