<?php

namespace App\Services\AI\Gemini;

use App\Exceptions\Integration\AIWorkException;
use App\Services\AI\BaseAI;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClientService extends BaseAI
{
    public function getDefaultModel(): string
    {
        return 'gemini-2.0-flash';
    }

    /**
     * @param mixed $data
     * @throws AIWorkException
     * @throws ConnectionException
     */
    public function sendRequest(mixed $data): mixed
    {
        $apiKey = $this->getApiKey();

        $response = Http::withoutVerifying()
            ->withHeaders(['Content-Type: application/json'])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->getModel()}:generateContent?key=$apiKey", $data);

        Log::debug('gemini error - ', [
            'clienterror' => $response->clientError(), 'errorobj' => $response->json()['error']
        ]);

        if ($response->clientError()) {
            throw new AIWorkException(code: $response->status());
        } else {
            return $response->json();
        }
    }

    protected function getConfigApiKey(): string
    {
        return (string)config('services.gemini.api_key');
    }
}