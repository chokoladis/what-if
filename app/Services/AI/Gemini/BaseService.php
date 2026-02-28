<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Exceptions\AIWorkException;
use App\Services\AI\BaseAI;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BaseService extends BaseAI
{
    public function getDefaultModel()
    {
        return 'gemini-2.0-flash';
    }

    protected function getConfigApiKey()
    {
        return config('services.gemini.api_key');
    }

    public function sendCurl(array $data)
    {
        $apiKey = $this->getApiKey();

        $response = Http::withoutVerifying()
            ->withHeaders([
                'Content-Type: application/json'
            ])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->getModel()}:generateContent?key=$apiKey", $data);

        Log::debug('gemini res - ' . $response);
        Log::debug('gemini error - ', [$response->status(), $response->json(), $response->body()]);

        if ($response->clientError()) {
            $response->throw();
        } else {
            return $this->getResponse($response->body());
        }
    }

    protected function request($data)
    {
        return $this->sendCurl($data);
    }

    protected function getResponse(string $response)
    {
        $responseContent = json_decode($response, true);

        if ($responseContent['error']){
            Log::error(__CLASS__, [$responseContent['error']]);
            throw new AIWorkException(__CLASS__.', error status - '.$responseContent['error']['status']);
        }

        $content = current($responseContent['candidates'])['content'];
        $firstPart = current($content['parts']);
        $jsonResult = $firstPart['text'];

        if (stripos($jsonResult, ';') === false) {
            Log::debug(__CLASS__.', incorrect format', [$jsonResult]);
            throw new AIWorkException(__CLASS__.', incorrect format');
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
}