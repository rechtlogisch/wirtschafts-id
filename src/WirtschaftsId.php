<?php

declare(strict_types=1);

namespace Rechtlogisch\WirtschaftsId;

use Rechtlogisch\WirtschaftsId\Dto\ValidationResult;
use Throwable;

class WirtschaftsId
{
    /** @var array<string, int> */
    private const LENGTHS_WIRTSCHAFTS_ID = [
        'WITHOUT_UNTERSCHEIDUNGSMERKMAL' => 11,
        'WITH_UNTERSCHEIDUNGSMERKMAL' => 17,
    ];

    /** @var string */
    private const PREFIX = 'DE';

    /** @var string */
    private const SEPARATOR = '-';

    private ValidationResult $result;

    public function __construct(
        public string $input
    ) {
        $this->result = new ValidationResult;

        try {
            $this->guard();
            $this->determineFormat();
            ($this->result->containsUnterscheidungsmerkmal() === false)
                ? $this->guardWithoutUnterscheidungsmerkmal()
                : $this->guardWithUnterscheidungsmerkmal();
        } catch (Throwable $exception) {
            $exceptionType = get_class($exception);
            $this->result->setValid(false);
            $this->result->addError($exceptionType, $exception->getMessage());
        }
    }

    public function validate(): ValidationResult
    {
        if ($this->result->isValid() === false) {
            return $this->result;
        }

        $hasValidChecksum = mb_substr($this->input, 10, 1) === (string) $this->checkDigit();
        $this->result->setValid($hasValidChecksum);

        if ($hasValidChecksum === false) {
            $this->result->addError(Exceptions\InvalidCheckDigit::class, 'Check digit in the provided Wirtschafts-ID is invalid.');
        }

        return $this->result;
    }

    private function guard(): void
    {
        if (empty($this->input)) {
            throw new Exceptions\InputEmpty('Please provide a non-empty input as Wirtschafts-ID.');
        }

        if (($prefix = mb_substr($this->input, 0, 2)) !== self::PREFIX) {
            if (ctype_alpha($prefix) && ! ctype_upper($prefix)) {
                throw new Exceptions\WirtschaftsIdPrefixMustBeUppercase('Wirtschafts-ID prefix must be uppercase "'.self::PREFIX.'". You provided: '.$prefix);
            }
            throw new Exceptions\InvalidWirtschaftsIdPrefix('Wirtschafts-ID must start with "'.self::PREFIX.'". You provided: '.$prefix);
        }
    }

    private function determineFormat(): void
    {
        if (str_contains($this->input, self::SEPARATOR)) {
            $this->result->setContainsUnterscheidungsmerkmal(true);

            return;
        }

        if (mb_strlen($this->input) < self::LENGTHS_WIRTSCHAFTS_ID['WITH_UNTERSCHEIDUNGSMERKMAL'] - 1) {
            $this->result->setContainsUnterscheidungsmerkmal(false);

            return;
        }

        $this->result->setContainsUnterscheidungsmerkmal(true);
    }

    private function guardWithoutUnterscheidungsmerkmal(): void
    {
        if (($lengthInput = mb_strlen($this->input)) !== self::LENGTHS_WIRTSCHAFTS_ID['WITHOUT_UNTERSCHEIDUNGSMERKMAL']) {
            throw new Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength('Wirtschafts-ID without Unterscheidungsmerkmal must be '.self::LENGTHS_WIRTSCHAFTS_ID['WITHOUT_UNTERSCHEIDUNGSMERKMAL'].' characters long. Provided Wirtschafts-ID is: '.$lengthInput.' characters long.');
        }

        if (! ctype_digit(mb_substr($this->input, 2))) {
            throw new Exceptions\WirtschaftsIdMustContainOnlyDigitsAfterDe('Wirtschafts-ID without Unterscheidungsmerkmal must contain only digits after "'.self::PREFIX.'" prefix.');
        }
    }

    private function guardWithUnterscheidungsmerkmal(): void
    {
        if (($occurrencesSeparator = mb_substr_count($this->input, self::SEPARATOR)) !== 1) {
            throw new Exceptions\WirtschaftsIdWithUnterscheidungsmerkmalMustContainExactlyOneSeparator('Wirtschafts-ID with Unterscheidungsmerkmal must contain exactly one separator ('.self::SEPARATOR.'). Provided Wirtschafts-ID contains '.$occurrencesSeparator.' separators.');
        }

        if (($lengthInput = mb_strlen($this->input)) !== self::LENGTHS_WIRTSCHAFTS_ID['WITH_UNTERSCHEIDUNGSMERKMAL']) {
            throw new Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength('Wirtschafts-ID with Unterscheidungsmerkmal must be '.self::LENGTHS_WIRTSCHAFTS_ID['WITH_UNTERSCHEIDUNGSMERKMAL'].' characters long. Provided Wirtschafts-ID is: '.$lengthInput.' characters long.');
        }

        if (mb_substr($this->input, 11, 1) !== self::SEPARATOR) {
            $message = 'Separator ('.self::SEPARATOR.'), before Unterscheidungsmerkmal, must be the twelfth character.';

            $character = mb_strpos($this->input, self::SEPARATOR);
            if (is_int($character) === true) {
                $character++;
                $message .= ' It is character: '.$character.'.';
            }
            throw new Exceptions\SeparatorMustBeCharacter12($message);
        }

        [$beforeSeparator, $afterSeparator] = explode(self::SEPARATOR, $this->input);

        if (! ctype_digit(mb_substr($beforeSeparator, 2))) {
            throw new Exceptions\WirtschaftsIdWithUnterscheidungsmerkmalCanContainOnlyDigitsAfterDe('Wirtschafts-ID with Unterscheidungsmerkmal must contain only digits after "'.self::PREFIX.'" prefix, and before Unterscheidungsmerkmal ('.self::SEPARATOR.$afterSeparator.').');
        }

        if (! ctype_digit($afterSeparator)) {
            throw new Exceptions\UnterscheidungsmerkmalMustContainOnlyDigits('Unterscheidungsmerkmal (after '.self::SEPARATOR.') must contain only digits.');
        }

        if ($afterSeparator === '00000') {
            throw new Exceptions\UnterscheidungsmerkmalCantContainOnlyZeros('Unterscheidungsmerkmal (after '.self::SEPARATOR.') can\'t contain only zeros.');
        }

        if ($afterSeparator !== '00001' && date('Y') < 2027) {
            $this->result->addHint(Exceptions\UnterscheidungsmerkmalShouldBe00001BeforeYear2027::class, 'Unterscheidungsmerkmal (after '.self::SEPARATOR.') is typically "00001" before year 2027.');
        }
    }

    public function checkDigit(): int
    {
        $digits = mb_substr($this->input, 2, 9);

        $product = 10;
        for ($i = 0; $i < 8; $i++) {
            $sum = (int) $digits[$i] + $product;
            $sum %= 10;
            if ($sum === 0) {
                $sum = 10;
            }
            $product = (2 * $sum) % 11;
        }

        $checkDigit = 11 - $product;
        if ($checkDigit === 10) {
            $checkDigit = 0;
        }

        return $checkDigit;
    }
}
