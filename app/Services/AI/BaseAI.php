<?php

namespace App\Services\AI;

use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AiApiInterface;

abstract class BaseAI implements AiApiInterface
{
    private string $model;

    function __construct()
    {
        $this->prepareSettings();
    }

    protected function prepareSettings()
    {
        if (empty($this->model)) {
            $this->setModel($this->getDefaultModel());
        }
    }

    private function setModel(string $model)
    {
        $this->model = $model;
    }

    abstract protected function getDefaultModel();

    function isSetOn(): bool
    {
        return false;
    }

    protected function getModel()
    {
        return $this->model;
    }

    protected function getApiKey()
    {
        $apiKey = $this->getConfigApiKey();

        if (empty($apiKey)) {
            throw new AIWorkException(__('entities.integrations.api_key_not_found'));
        }

        return $apiKey;
    }

    abstract protected function getConfigApiKey();

    abstract protected function sendCurl(array $data);
}