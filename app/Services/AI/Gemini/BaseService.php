<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Exceptions\Integration\AIWorkException;
use App\Services\AI\BaseAI;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaseService extends BaseAI
{
    public function getDefaultModel(): string
    {
        return 'gemini-2.0-flash';
    }

    /**
     * @param mixed $data
     * @return array{bool, \App\DTO\Errors\CommonError|null}
     * @throws AIWorkException
     * @throws ConnectionException
     */
    public function sendRequest(mixed $data): array
    {
        $apiKey = $this->getApiKey();

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type: application/json'
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->getModel()}:generateContent?key=$apiKey", $data);

        Log::debug('gemini error - ', $response->json()['error']);

        if ($response->clientError()) {
            throw new AIWorkException(code: $response->status());
        } else {
            return $this->getResponse($response->json());
        }
    }

    /**
     * @param mixed $responseData
     * @return array{bool, CommonError|null}
     * @throws AIWorkException
     */
    protected function getResponse(mixed $responseData): array
    {
        if ($responseData['error']) {
            Log::error(__CLASS__, [$responseData['error']]);
            throw new AIWorkException(__CLASS__ . ', error status - ' . $responseData['error']['status']);
        }

        $content = current($responseData['candidates'])['content'];
        $firstPart = current($content['parts']);
        $jsonResult = $firstPart['text'];

        if (stripos($jsonResult, ';') === false) {
            Log::debug(__CLASS__ . ', incorrect format', [$jsonResult]);
            throw new AIWorkException(__CLASS__ . ', incorrect format');
        } else {
            [$isLegal, $error] = explode(';', $jsonResult);

            $isLegal = filter_var($isLegal, FILTER_VALIDATE_BOOLEAN);

            if (!$isLegal) {
                return [false, new CommonError($error)];
            } else {
                return [true, null];
            }
        }
    }

    protected function getConfigApiKey(): string
    {
        return (string)config('services.gemini.api_key');
    }
}