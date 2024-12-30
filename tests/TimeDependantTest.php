<?php

use Rechtlogisch\WirtschaftsId\WirtschaftsId;

it('returns a hint for unterscheidungsmerkmal different than 00001 before year 2026', function (string $wirtschaftsId) {
    $result = (new WirtschaftsId($wirtschaftsId))->validate();

    expect($result->isValid())->toBeTrue()
        ->and($result->containsUnterscheidungsmerkmal())->toBeTrue()
        ->and($result->getErrors())->toBeEmpty()
        ->and($result->getHints())->not->toBeEmpty()
        ->and($result->getHints())->toHaveKey(Rechtlogisch\WirtschaftsId\Exceptions\UnterscheidungsmerkmalShouldBe00001BeforeYear2026::class);
})->with([
    'DE123456788-00002',
    'DE123456788-99999',
])->skip(date('Y') >= 2026, 'This test is only relevant before year 2026.');
