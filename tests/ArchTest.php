<?php

test('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'echo', 'print_r'])
    ->not->toBeUsed()
    ->group('arch');

test('dtos are final')
    ->expect('Rechtlogisch\WirtschaftsId\Dto')
    ->toBeFinal()
    ->group('arch');

test('use strict mode')
    ->expect('Rechtlogisch\WirtschaftsId')
    ->toUseStrictTypes()
    ->group('arch');
