<?php

namespace App\Services\AI\OpenAI;

use App\Services\AI\BaseAI;
use Illuminate\Support\Facades\Log;

abstract class BaseService extends BaseAI
{
    public function getDefaultModel()
    {
        return 'gpt-4.1';
    }

    protected function getConfigApiKey()
    {
        return config('services.openai.api_key');
    }

    protected function sendCurl(array $data)
    {
        $apiKey = $this->getApiKey();

        $curl = curl_init('https://api.openai.com/v1/responses');
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($curl, CURLOPT_HEADER, false);

        $jsonData = $this->prepareData($data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        //            only for testing

        Log::debug('openai res - ' . $response);
        Log::debug('$error - ' . $error);

        if (is_string($response)) {
            $jsonResult = json_decode($response, true);
            if (!empty($jsonResult['error'])) {
                throw new \Exception($jsonResult['error']['message']);
            } else {
                return true;
            }
        } else {
            throw new \Exception($error);
        }
    }

    protected function prepareData(array $data)
    {
        return json_encode([
            'model' => $this->getModel(),
            'input' => $data['input']
        ], JSON_UNESCAPED_UNICODE);
    }

    protected function request($data)
    {
        if (empty($data['input'])) {
            return [false, new \Error(__('entities.integrations.data_empty'), 'data_empty')];
        }

        return $this->sendCurl($data);
    }

}