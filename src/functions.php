<?php

declare(strict_types=1);

use Rechtlogisch\WirtschaftsId\Dto\ValidationResult;
use Rechtlogisch\WirtschaftsId\WirtschaftsId;

function validateWirtschaftsId(string $input): ValidationResult
{
    return (new WirtschaftsId($input))->validate();
}

function isWirtschaftsIdValid(string $input): ?bool
{
    return (new WirtschaftsId($input))->validate()->isValid();
}
