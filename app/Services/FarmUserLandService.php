<?php

namespace App\Services;

use App\Models\FarmUserLand;
use App\Models\User;

class FarmUserLandService
{
    // 土地等级
    public static $LEVEL = [
        1 => [
            'short_name' => '普',
            'name' => '普通土地',
            'ability_exp' => 0,
            'ability_reward' => 0,
            'ability_speed' => 0,
            'price' => [
                4 => 5000,
                5 => 20000,
                6 => 50000,
                7 => 70000,
                8 => 90000,
                9 => 110000,
            ],
            'level' => [
                4 => 5,
                5 => 7,
                6 => 9,
                7 => 11,
                8 => 13,
                9 => 15,
            ]
        ],
        2 => [
            'short_name' => '红',
            'name' => '红土地',
            'ability_exp' => 0,
            'ability_reward' => 10,
            'ability_speed' => 5,
            'price' => [
                1 => 100000,
                2 => 150000,
                3 => 200000,
                4 => 250000,
                5 => 300000,
                6 => 350000,
                7 => 400000,
                8 => 450000,
                9 => 500000
            ],
            'level' => [
                1 => 10,
                2 => 13,
                3 => 16,
                4 => 19,
                5 => 22,
                6 => 25,
                7 => 28,
                8 => 31,
                9 => 34,
            ]
        ],
        3 => [
            'short_name' => '金',
            'name' => '金土地',
            'ability_exp' => 0,
            'ability_reward' => 10,
            'ability_speed' => 5,
            'price' => [
                1 => 100000,
                2 => 150000,
                3 => 200000,
                4 => 250000,
                5 => 300000,
                6 => 350000,
                7 => 400000,
                8 => 450000,
                9 => 500000,
            ],
            'level' => [
                1 => 20,
                2 => 24,
                3 => 28,
                4 => 32,
                5 => 36,
                6 => 40,
                7 => 44,
                8 => 48,
                9 => 50,
            ]
        ]
    ];

    public static $DEFAULT_LAND_COUNT = 3; // 默认土地数量
    public static $MAX_LAND_COUNT = 9; // 最大土地数量


    // 获取用户土地
    static public function getLandList(User $user)
    {
        $list = FarmUserLand::with('handbook')->where('user_id', $user->id)->get();

        if ($list->isEmpty()) {
            // 没土地默认创建3块
            $lands = [];
            for ($i = 0; $i < self::$MAX_LAND_COUNT; $i++) {
                $lands[] = [
                    'user_id' => $user->id,
                    'level_id' => 1,
                    'status' => $i < self::$DEFAULT_LAND_COUNT ? 0 : 9
                ];
            }
            FarmUserLand::insert($lands);
            $list = FarmUserLand::with('handbook')->where('user_id', $user->id)->get();
        }

        foreach ($list as &$value) {
            $level = self::$LEVEL[$value['level_id']];
            $value['level'] = [
                'name' => $level['name'],
                'short_name' => $level['short_name'],
                'ability_exp' => $level['ability_exp'],
                'ability_reward' => $level['ability_reward'],
                'ability_speed' => $level['ability_speed']
            ];
        }

        return $list;
    }
}
