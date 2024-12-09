<?php

declare(strict_types=1);

namespace Rechtlogisch\WirtschaftsId\Dto;

use TypeError;

final class ValidationResult
{
    private ?bool $valid = null;

    private ?bool $containsUnterscheidungsmerkmal = null;

    /** @var string[]|null */
    private ?array $errors = null;

    /** @var string[]|null */
    private ?array $hints = null;

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(bool $valid): void
    {
        $this->valid = $valid;
    }

    public function containsUnterscheidungsmerkmal(): ?bool
    {
        return $this->containsUnterscheidungsmerkmal;
    }

    public function setContainsUnterscheidungsmerkmal(mixed $containsUnterscheidungsmerkmal): void
    {
        is_bool($containsUnterscheidungsmerkmal) ?: throw new TypeError('containsUnterscheidungsmerkmal accepts only boolean');
        $this->containsUnterscheidungsmerkmal = $containsUnterscheidungsmerkmal;
    }

    /**
     * @return string[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function getFirstErrorKey(): ?string
    {
        return (empty($this->errors))
            ? null
            : (string) array_key_first($this->errors);
    }

    /**
     * @return array<int|string, string>|null
     */
    public function getFirstError(): ?array
    {
        $errors = $this->getErrors() ?? [];
        $firstKey = array_key_first($errors);

        if ($firstKey === null) {
            return null;
        }

        return [$firstKey => $errors[$firstKey]];
    }

    public function addError(string $type, string $error): void
    {
        $this->errors[$type] = $error;
    }

    /**
     * @return string[]|null
     */
    public function getHints(): ?array
    {
        return $this->hints;
    }

    public function addHint(string $type, string $error): void
    {
        $this->hints[$type] = $error;
    }
}
