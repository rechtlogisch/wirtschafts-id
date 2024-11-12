<?php

use Rechtlogisch\WirtschaftsId\Dto\ValidationResult;

it('returns null as first error when no errors set', function () {
    $dto = new ValidationResult;
    $firstError = $dto->getFirstError();

    expect($firstError)->toBeNull();
});

it('gets contains unterscheidungsmerkmal', function () {
    $dto = new ValidationResult;
    $containsUnterscheidungsmerkmal = $dto->containsUnterscheidungsmerkmal();

    expect($containsUnterscheidungsmerkmal)->toBeNull();
});

it('sets contains unterscheidungsmerkmal', function (bool $value) {
    $dto = new ValidationResult;
    $dto->setContainsUnterscheidungsmerkmal($value);
    $containsUnterscheidungsmerkmal = $dto->containsUnterscheidungsmerkmal();

    expect($containsUnterscheidungsmerkmal)->toBe($value);
})->with([
    [true],
    [false],
]);

it('throws a type error if contains containsUnterscheidungsmerkmal is not set as a boolean', function ($value) {
    $dto = new ValidationResult;
    $dto->setContainsUnterscheidungsmerkmal($value);
})
    ->throws(TypeError::class)
    ->with([
        [null],
        ['string'],
        [1],
        [1.1],
        [[]],
        [new stdClass],
    ]);
