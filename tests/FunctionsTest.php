<?php

use Rechtlogisch\WirtschaftsId\Dto\ValidationResult;

it('validates a wirtschafts-id with the global validateWirtschaftsId() function', function (string $wirtschaftsId) {
    $result = validateWirtschaftsId($wirtschaftsId);

    expect($result)->toBeInstanceOf(ValidationResult::class)
        ->and($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with('valid');

it('validates a wirtschafts-id with the global isValidWirtschaftsId() function', function (string $wirtschaftsId) {
    $result = isWirtschaftsIdValid($wirtschaftsId);

    expect($result)->toBeTrue();
})->with('valid');
