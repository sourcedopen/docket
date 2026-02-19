<?php

arch('app does not use debug helpers')
    ->expect('App')
    ->not->toUse(['die', 'dd', 'dump', 'var_dump', 'print_r', 'ray']);

arch('models extend Eloquent Model')
    ->expect('App\Models')
    ->toExtend('Illuminate\Database\Eloquent\Model')
    ->ignoring('App\Models\User');

arch('models use soft deletes')
    ->expect('App\Models')
    ->toUseTrait('Illuminate\Database\Eloquent\SoftDeletes')
    ->ignoring('App\Models\User');

arch('enums are string backed')
    ->expect('App\Enums')
    ->toBeStringBackedEnums();

arch('controllers have Controller suffix')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller');

arch('controllers extend base Controller')
    ->expect('App\Http\Controllers')
    ->toExtend('App\Http\Controllers\Controller')
    ->ignoring('App\Http\Controllers\Controller');
