<?php

namespace App\Services\OpenAI;

use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    const MODEL = 'gpt-4.1';

    private string $model;

    private function setModel(string $model)
    {
        $this->model = $model;
    }

    private function getModel()
    {
        return $this->model;
    }

    protected function request(array $data)
    {
        if (empty($this->model)){
            $this->setModel(self::MODEL);
        }

        if (empty($data['input'])){
            return [false, new \Error(__('entities.openai.input_empty'), 'input_empty')];
        }

        try {
            $apiKey = config('services.openai.api_key');

            if (empty($apiKey)){
                return [false, new \Error(__('entities.openai.key_undefined'))];
            }

            $curl = curl_init('https://api.openai.com/v1/responses');
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer '.$apiKey
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

            Log::debug('openai res - '.$response);
            Log::debug('$error - '.$error);

            if (is_string($response)){
                $jsonResult = json_decode($response, true);
                if (!empty($jsonResult['error'])){
                    return [false, new \Error($jsonResult['error']['message'])];
                } else {
                    return [true, null];
                }
            } else {
                return [false, $error];
            }
        } catch (\Exception $e)
        {
            return [false, $e->getMessage()];
        }
    }

    private function prepareData(array $data)
    {
        return json_encode([
            'model' => $this->getModel(),
            'input' => $data['input']
        ], JSON_UNESCAPED_UNICODE);
    }

}