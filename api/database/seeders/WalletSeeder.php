<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('wallets')->insert([
            'balance' => 1000,
            'user_id' => 1,
        ]);

        \DB::table('wallets')->insert([
            'balance' => 1000,
            'user_id' => 2,
        ]);

        \DB::table('wallets')->insert([
            'balance' => 2000,
            'user_id' => 3,
        ]);

        \DB::table('wallets')->insert([
            'balance' => 2000,
            'user_id' => 4,
        ]);
    }
}
