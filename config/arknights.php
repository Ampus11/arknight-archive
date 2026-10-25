<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Operator Level Caps
    |--------------------------------------------------------------------------
    |
    | Maximum level per elite stage (index 0 = E0, 1 = E1, 2 = E2) for each
    | rarity. A null value means that elite stage is not reachable.
    |
    */

    'max_levels' => [
        1 => [30, null, null],
        2 => [30, null, null],
        3 => [40, 55, null],
        4 => [45, 60, 70],
        5 => [50, 70, 80],
        6 => [50, 80, 90],
    ],

    /*
    |--------------------------------------------------------------------------
    | Elite Promotion LMD Costs
    |--------------------------------------------------------------------------
    */

    'promotion_lmd' => [
        3 => [1 => 10000],
        4 => [1 => 15000, 2 => 60000],
        5 => [1 => 20000, 2 => 120000],
        6 => [1 => 30000, 2 => 180000],
    ],

    /*
    |--------------------------------------------------------------------------
    | EXP Battle Records
    |--------------------------------------------------------------------------
    */

    'battle_records' => [
        ['key' => 'strategic', 'name' => 'Strategic Battle Record', 'exp' => 2000, 'emoji' => '📜'],
        ['key' => 'tactical', 'name' => 'Tactical Battle Record', 'exp' => 1000, 'emoji' => '📘'],
        ['key' => 'frontline', 'name' => 'Frontline Battle Record', 'exp' => 400, 'emoji' => '📗'],
        ['key' => 'drill', 'name' => 'Drill Battle Record', 'exp' => 200, 'emoji' => '📙'],
    ],

];
