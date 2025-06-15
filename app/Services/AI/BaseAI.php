<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Log;

abstract class BaseAI
{
    private string $model;

    function __construct()
    {
        $this->prepareSettings();
    }

    private function setModel(string $model)
    {
        $this->model = $model;
    }

    protected function getModel()
    {
        return $this->model;
    }

    protected function getApiKey()
    {
        $apiKey = $this->getConfigApiKey();

        if (empty($apiKey)) {
            return [false, new \Error(__('entities.integrations.api_key_not_found'))];
        }

        return [$apiKey, null];
    }

    protected function prepareSettings()
    {
        if (empty($this->model)) {
            $this->setModel($this->getDefaultModel());
        }
    }


    abstract protected function getDefaultModel();
    abstract protected function getConfigApiKey();
    abstract protected function sendCurl(array $data);
}