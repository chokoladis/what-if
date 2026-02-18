<?php

namespace App\Services\AI\Gemini;

use App\DTO\Errors\CommonError;
use App\Exceptions\AIWorkException;
use App\Services\AI\BaseAI;
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

        $curl = curl_init('https://generativelanguage.googleapis.com/v1beta/models/' . $this->getModel() . ':generateContent?key=' . $apiKey);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $jsonData = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        Log::debug('gemini res - ' . $response);

        if (is_string($response)) {
            return $this->getResponse($response);
        } else {
            throw new \Exception($error);
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