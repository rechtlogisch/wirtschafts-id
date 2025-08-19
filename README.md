![Recht logisch Wirtschafts-ID banner image](rechtlogisch-wirtschafts-id-banner.png)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rechtlogisch/wirtschafts-id.svg?style=flat-square)](https://packagist.org/packages/rechtlogisch/wirtschafts-id)
[![Tests](https://github.com/rechtlogisch/wirtschafts-id/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/rechtlogisch/wirtschafts-id/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/rechtlogisch/wirtschafts-id.svg?style=flat-square)](https://packagist.org/packages/rechtlogisch/wirtschafts-id)

# wirtschafts-id

> Validates the German Wirtschafts-ID (Wirtschafts-Identifikationsnummer)

Check digit (eleventh position in normalised form) is validated based on [ISO/IEC 7064, MOD 11,10](https://www.iso.org/standard/31531.html) as documented within the meanwhile repealed "Datenträger-Verordnung über die Abgabe Zusammenfassender Meldungen – ZMDV" dated 13.05.1993 ([BGBl. I S. 736](https://www.bgbl.de/xaver/bgbl/start.xav?start=%2F%2F*%5B%40attr_id%3D%27bgbl193s0726.pdf%27%5D#__bgbl__%2F%2F*%5B%40attr_id%3D%27bgbl193s0726.pdf%27%5D__1720528216746)).

> [!NOTE]
> This package validates solely the syntax and check digit of the provided input. It does not confirm, that the provided Wirtschafts-ID was assigned to an entity. Please consult [BZSt](https://www.bzst.de/DE/Unternehmen/Identifikationsnummern/Wirtschafts-Identifikationsnummer/wirtschaftsidentifikationsnummer_node.html), if in doubt. Some insight concerning validity might be concluded based on [VIES](https://ec.europa.eu/taxation_customs/vies/).

## Installation

You can install the package via composer:

```bash
composer require rechtlogisch/wirtschafts-id
```

## Usage

```php
isWirtschaftsIdValid('DE123456788'); // => true
```

or

```php
use Rechtlogisch\WirtschaftsId\WirtschaftsId;

(new WirtschaftsId('DE123456788'))
    ->validate() // ValidationResult::class
    ->isValid(); // => true
```

## Unterscheidungsmerkmal, short: U-Merkmal

This package supports both validation of the Wirtschafts-ID with and without the Unterscheidungsmerkmal. It is optional information after the eleventh character and separator. It consists of a hyphen and a five-digit number.

> [!NOTE]
> Unterscheidungsmerkmal starts at `00001` and therefore `00000` is not valid. 

> [!TIP]
> At first all entities will receive a Wirtschafts-ID with the Unterscheidungsmerkmal `-00001`. If needed, as of 4th Quarter of 2027 each economic activity (wirtschaftliche Tätigkeit) will receive a separate Unterscheidungsmerkmal, which will be incremented by one for each economic activity and linked to a tax number of the business or the permanent establishment within the responsible tax office (cf. [bzst.de](https://www.bzst.de/DE/Unternehmen/Identifikationsnummern/Wirtschafts-Identifikationsnummer/wirtschaftsidentifikationsnummer_node.html#js-toc-entry4)).
> 
> Source: [BZSt](https://www.bzst.de/DE/Unternehmen/Identifikationsnummern/Wirtschafts-Identifikationsnummer/wirtschaftsidentifikationsnummer_node.html#js-toc-entry4)

> [!TIP]
> Based on the form/dataset you might need to provide the Unterscheidungsmerkmal or not.

### Examples

```php
isWirtschaftsIdValid('DE123456788-00001'); // => true
```

or

```php
use Rechtlogisch\WirtschaftsId\WirtschaftsId;

(new WirtschaftsId('DE123456788-00001'))
    ->validate() // ValidationResult::class
    ->isValid(); // => true
```

## Validation errors

You can get a list of errors explaining why the provided input is invalid. The `validate()` method returns a DTO with a `getErrors()` method.

> [!NOTE]
> The keys of `getErrors()` hold the stringified reference to the exception class. You can check for a particular error by comparing to the ::class constant. For example: `Rechtlogisch\WirtschaftsId\Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength::class`.

```php
validateWirtschaftsId('DE12345678')->getErrors();
// [
//   'Rechtlogisch\WirtschaftsId\Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength'
//    => 'Wirtschafts-ID must be 11 characters long. Provided Wirtschafts-ID is: 10 characters long.',
// ]
```
or

```php
use Rechtlogisch\WirtschaftsId\WirtschaftsId;

(new WirtschaftsId('DE12345678'))
    ->validate()
    ->getErrors();
// [
//   'Rechtlogisch\WirtschaftsId\Exceptions\InvalidWirtschaftsIdWithoutUnterscheidungsmerkmalLength'
//    => 'Wirtschafts-ID must be 11 characters long. Provided Wirtschafts-ID is: 10 characters long.',
// ]
```

## Plausibility hints

You can get a list of hints explaining why the provided input is not plausible. Hints do not change the validation result.  The `validate()` method returns a DTO with a `getHints()` method.

> [!NOTE]
> The keys of `getHints()` hold the stringified reference to the exception class. You can check for a particular error by comparing to the ::class constant. For example: `Rechtlogisch\WirtschaftsId\Exceptions\UnterscheidungsmerkmalShouldBe00001BeforeYear2027::class`.

```php
validateWirtschaftsId('DE123456788-00002')->getHints();
// [
//   'Rechtlogisch\WirtschaftsId\Exceptions\UnterscheidungsmerkmalShouldBe00001BeforeYear2027'
//    => 'Unterscheidungsmerkmal (after -) is typically "00001" before year 2027.',
// ]
```

> [!TIP]
> You can of course use the alternative way of validation presented in the [Usage](#usage) section. 

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/rechtlogisch/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email open-source@rechtlogisch.de instead of using the issue tracker.

## Credits

- [Krzysztof Tomasz Zembrowski](https://github.com/zembrowski)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
