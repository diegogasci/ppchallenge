<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    protected $userRespository;

    public function __construct()
    {
        $this->userRespository = new UserRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->userRespository->createUser([
            'name' => 'Usuário Comum 1',
            'email' => 'usuario1@gmail.com',
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'document_type' => 0,
            'document' => 92323733001,
            'password' => Hash::make(123456),
            'remember_token' => Str::random(10),
        ]);

        $this->userRespository->createUser([
            'name' => 'Usuário Comum 2',
            'email' => 'usuario2@gmail.com',
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'document_type' => 0,
            'document' => 76085400012,
            'password' => Hash::make(123456),
            'remember_token' => Str::random(10),
        ]);

        $this->userRespository->createUser([
            'name' => 'Lojista 1',
            'email' => 'lojista1@gmail.com',
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'document_type' => 1,
            'document' => 60995975000129,
            'password' => Hash::make(123456),
            'remember_token' => Str::random(10),
        ]);

        $this->userRespository->createUser([
            'name' => 'Lojista 2',
            'email' => 'lojista2@gmail.com',
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'document_type' => 1,
            'document' => 89877523000124,
            'password' => Hash::make(123456),
            'remember_token' => Str::random(10),
        ]);
    }
}
