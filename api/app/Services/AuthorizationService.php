<?php

namespace App\Services;
use GuzzleHttp\Client as GuzzleHttp;

class AuthorizationService
{
    protected $guzzle;

    public function __construct(GuzzleHttp $guzzle)
    {
        $this->guzzle = $guzzle;
    }

    public function check()
    {
        $request = $this->guzzle->get('https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6');
        $response = json_decode((string) $request->getBody());

        if ($response->message !== 'Autorizado') {
            return false;
        }

        return true;
    }
}
