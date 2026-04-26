<?php

namespace App\Services\AI;

use App\DTO\Errors\CommonError;
use App\Exceptions\Integration\AIWorkException;
use App\Interfaces\AiApiInterface;

abstract class BaseAI implements AiApiInterface
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

    function isSetOn(): bool
    {
        return false;
    }

    /**
     * @param array<string, mixed> $data
     * @return array<bool, ?CommonError>
     */
    abstract protected function sendRequest(array $data): array;

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
}