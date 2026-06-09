<?php

namespace App\Services\AI;

use App\DTO\Errors\CommonError;
use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AI\AIClientContract;

abstract class BaseAI implements AIClientContract
{
    private string $model;

    function __construct()
    {
        $this->prepareSettings();
    }

    protected function prepareSettings(): void
    {
        if (empty($this->model)) {
            $this->setModel($this->getDefaultModel());
        }
    }

    private function setModel(string $model): void
    {
        $this->model = $model;
    }

    abstract protected function getDefaultModel(): string;

    public function isSetOn(): bool
    {
        return false;
    }

    protected function getModel(): string
    {
        return $this->model;
    }

    protected function getApiKey(): string
    {
        $apiKey = $this->getConfigApiKey();

        if (empty($apiKey)) {
            throw new AIWorkException(__('entities.integrations.api_key_not_found'));
        }

        return $apiKey;
    }

    abstract protected function getConfigApiKey(): string;
    abstract public function sendRequest(mixed $data): mixed;
}