<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TapService
{
    protected $baseUrl = 'https://api.tap.company/v2/charges/';
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = 'sk_test_XKokBfNWv6FIYuTMg5sLPjhJ'; // Replace with your actual API key
    }

    public function createCharge($data)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl, $data);

        return $response->json();
    }
}
