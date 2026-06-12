<?php

return [
    'wallet_bridge_status' => [
        'CREATED' => '待Link',
        'AUDIT' => 'Link中',
        'AUDITED' => 'Link中',
        'SENT' => 'Link成功',
        'SUCCEEDED' => 'Link成功',
        'FAILED' => '待Link',
        'CANCELED' => '待Link'
    ],
    'user' => [
        'status' => [
            0 => '正常',
            1 => '停用',
            2 => '冻结',
            3 => '确定冻结',
            4 => '演员',
            9 => '确定正常',
        ],
        'status_help' => [
            0 => '正常操作系统中任何开放功能',
            1 => '该账户无法登录APP',
            2 => '该账户可以登录，但是该帐号无法操作(提现、兑换、等)',
            3 => '该账户可以登录，但是该帐号无法操作(提现、兑换、等)',
            4 => '该账户可以登录，但是该帐号无法操作(提现、兑换、等)',
            9 => '这些类型的用户是确定了的正常用户,不会被扫描为薅羊毛用户',
        ]
    ],

    'mark_user' => [
        'mark_type' => [
            0 => '未标记',
            1 => '想去/计划',
            2 => '已完成/已去/已看',
        ],
    ],

    'wallet_asset' => [
        'module_code' => [
            'BUY_NODE' => '兑换节点',
            'BUY_NFT' => '兑换NFT',
            'NODE_DIRECT_REWARD' => 'node直接奖励',
            'NODE_INDIRECT_REWARD' => 'node间推奖励',
            'NFT_DIRECT_REWARD' => 'nft直接奖励',
            'NFT_INDIRECT_REWARD' => 'nft间推奖励',
            'DEPOSIT' => '充值',
            'WITHDRAW' => '提现',
            'WITHDRAW_CANCELED' => '提现退回',
            'ADMIN' => '系统充值',
        ],
    ],
    'wallet_deposit' => [
        'status' => [
            'CREATED' => '请求交易',
            'LACKED' => '额度不足',
            'SUCCESS' => '交易成功',
            'FAILED' => '交易失败',
        ]
    ],
    'wallet_withdraw' => [
        'status' => [
            'CREATED' => '待上链',
            'AUDITED' => '上链中',
            'SENT' => '区块确认中',
            'SUCCEEDED' => '上链成功',
            'FAILED' => '上链失败',
            'CANCELED' => '上链失败',
        ]
    ],
    'mark_item' => [
        'status' => [
            0 => '取消',
            1 => '计划',
            2 => '完成',
        ],
    ],
];
