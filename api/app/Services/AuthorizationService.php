<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AuthorizationService
{
    public function check()
    {
        $request = Http::get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
        $response = json_decode((string) $request->getBody());

        dump($response->message);

        if ($response->message !== 'Autorizado') {
            return false;
        }

        return true;
    }
}
