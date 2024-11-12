<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->not->toBeUsed();

test('dtos are final')
    ->expect('Rechtlogisch\WirtschaftsId\Dto')
    ->toBeFinal();

test('use strict mode')
    ->expect('Rechtlogisch\WirtschaftsId')
    ->toUseStrictTypes();
