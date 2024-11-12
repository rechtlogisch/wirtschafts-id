<?php

use Rechtlogisch\WirtschaftsId\Dto\ValidationResult;
use Rechtlogisch\WirtschaftsId\Exceptions;
use Rechtlogisch\WirtschaftsId\WirtschaftsId;

it('returns a ValidationResult on valid input', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();
    expect($result)->toBeObject(ValidationResult::class);
})->with('valid');

it('returns a ValidationResult on invalid input', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();
    expect($result)->toBeObject(ValidationResult::class);
})->with('invalid');

it('returns true for a valid wirtschafts-id without an unterscheidungsmerkmal', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->containsUnterscheidungsmerkmal())->toBeFalse()
        ->and($result->getErrors())->toBeEmpty();
})->with('valid');

it('returns true for a valid wirtschafts-id with an unterscheidungsmerkmal', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->containsUnterscheidungsmerkmal())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with('valid-with-unterscheidungsmerkmal');

it('returns false for a valid wirtschafts-id with spaces', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->not->toBeEmpty();
})->with('valid-but-with-spaces-therefore-invalid');

it('returns false for an invalid wirtschafts-id without unterscheidungsmerkmal', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->not->toBeEmpty();
})->with('invalid');

it('returns false for an invalid wirtschafts-id with unterscheidungsmerkmal', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->containsUnterscheidungsmerkmal())->toBeTrue()
        ->and($result->getErrors())->not->toBeEmpty();
})->with('invalid-with-unterscheidungsmerkmal');

it('throws a type error when nothing provided as wirtschafts-id', function () {
    /** @noinspection PhpParamsInspection */
    new WirtschaftsId; /** @phpstan-ignore-line */
})->throws(TypeError::class);

it('throws a type error when null provided as wirtschafts-id', function () {
    new WirtschaftsId(null); /** @phpstan-ignore-line */
})->throws(TypeError::class);

it('returns false when wirtschafts-id does not start with DE', function (string $input) {
    $result = (new WirtschaftsId($input))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InvalidWirtschaftsIdPrefix::class)
        ->and($result->getFirstError())->toContain('Wirtschafts-ID must start with "DE". You provided: '.mb_substr($input, 0, 2));
})->with([
    'AA',
    '123',
]);

it('returns false and a specific error message when wirtschafts-id does not start with uppercase DE', function (string $input) {
    $result = (new WirtschaftsId($input))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\WirtschaftsIdPrefixMustBeUppercase::class);
})->with([
    'de',
    'De',
    'dE',
]);

it('returns false and specific error message when empty string provided as input', function () {
    $result = (new WirtschaftsId(''))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\InputEmpty::class);
});

it('returns false and specific error message when input to short', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toContain(Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength::class);
})->with([
    'DE1',
    'DE12',
    'DE123',
    'DE1234',
    'DE12345',
    'DE123456',
    'DE1234567',
    'DE12345678',
]);

it('returns false and specific error message when input to long', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toContain(Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength::class);
})->with([
    'DE1234567890',
    'DE12345678901',
]);

it('returns false and specific error message when an wirtschafts-id contains non-digits after DE', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getFirstErrorKey())->toBe(Exceptions\WirtschaftsIdMustContainOnlyDigitsAfterDe::class);
})->with([
    'DE12345678X',
    'DEx23456789',
    'DE12_456789',
    'DE123.56789',
    'DE1234 6789',
    'DE12345,789',
    'DE123456_89',
]);

it('returns checkDigit `0` for wirtschafts-id with check digit `10`', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty();
})->with([
    'DE123456840',
]);

it('returns false and specific error for wirtschafts-id with unterscheidungsmerkmal when not exactly one separator provided', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->toHaveKey(Exceptions\WirtschaftsIdWithUnterscheidungsmerkmalMustContainExactlyOneSeparator::class);
})->with([
    'DE123456789-00-01',
    'DE12345678900001',
    'DE123456789000001',
]);

it('returns false and specific error for wirtschafts-id with unterscheidungsmerkmal when separator not twelfth character', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->toHaveKey(Exceptions\SeparatorMustBeCharacter12::class);
})->with([
    'DE12345678-900001',
    'DE1234567890-0001',
]);

it('returns false and specific error for wirtschafts-id with unterscheidungsmerkmal when before hyphen not only digits', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->toHaveKey(Exceptions\WirtschaftsIdWithUnterscheidungsmerkmalCanContainOnlyDigitsAfterDe::class);
})->with([
    'DE12345678x-00001',
]);

it('returns false and specific error for wirtschafts-id with unterscheidungsmerkmal when after hyphen not only digits', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeFalse()
        ->and($result->getErrors())->toHaveKey(Exceptions\UnterscheidungsmerkmalMustContainOnlyDigits::class);
})->with([
    'DE123456789-0000x',
]);
