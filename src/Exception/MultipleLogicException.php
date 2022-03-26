<?php

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Throwable;

class MultipleLogicException extends \LogicException
{
    protected array $errors = [];

    #[Pure]
    public function __construct($errors = [], $code = 0, Throwable $previous = null)
    {
        $this->errors = $errors;
        parent::__construct("One or more errors occurred...", $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}