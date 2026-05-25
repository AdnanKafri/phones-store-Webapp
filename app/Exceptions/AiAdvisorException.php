<?php

namespace App\Exceptions;

use Exception;

class AiAdvisorException extends Exception
{
    public function __construct(
        string $message,
        protected string $errorCode = 'AI_ADVISOR_ERROR',
        protected int $status = 422,
        protected array $errors = [],
    ) {
        parent::__construct($message);
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }

    public function status(): int
    {
        return $this->status;
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
