<?php

return [

    'wallet_bridge_status' => [
        'CREATED' => 'Waiting for Link',
        'AUDIT' => 'In the link',
        'AUDITED' => 'In the link',
        'SENT' => 'Link successful',
        'SUCCEEDED' => 'Link successful',
        'FAILED' => 'Waiting for Link',
        'CANCELED' => 'Waiting for Link'
    ],
    'user' => [
        'status' => [
            0 => 'Normal',
            1 => 'Disabled',
            2 => 'Frozen',
            3 => 'Determined Frozen',
            4 => 'Actor',
            9 => 'Determined Normal',
        ],
        'status_help' => [
            0 => 'Normal operation system any open function',
            1 => 'This account cannot login APP',
            2 => 'This account can login, but this account cannot operate (withdrawal, exchange, etc.)',
            3 => 'This account can login, but this account cannot operate (withdrawal, exchange, etc.)',
            4 => 'This account can login, but this account cannot operate (withdrawal, exchange, etc.)',
            9 => 'These types of users are determined normal users, will not be scanned as薅羊毛 users',
        ]
    ],
    'nft_order' => [
        'status' => [
            0 => 'Invalid',
            1 => 'Holding',
            2 => 'Transferred',
        ],
        'source' => [
            'user' => 'User',
            'admin' => 'Admin',
            'transfer' => 'Transfer',
        ],
    ],
    'wallet_asset' => [
        'module_code' => [
            'BUY_NODE' => 'Buy node',
            'BUY_NFT' => 'Buy NFT',
            'NODE_DIRECT_REWARD' => 'Direct reward',
            'NODE_INDIRECT_REWARD' => 'Indirect reward',
            'DEPOSIT' => 'Deposit',
            'WITHDRAW' => 'Withdrawal',
            'WITHDRAW_CANCELED' => 'Withdrawal refund',
            'ADMIN' => 'System deposit',
        ],
    ],
    'wallet_deposit' => [
        'status' => [
            'CREATED' => 'Deposit Request',
            'LACKED' => 'Deposit lack',
            'SUCCESS' => 'Deposit Successful',
            'FAILED' => 'Deposit Failed',
        ]
    ],
    'wallet_withdraw' => [
        'status' => [
            'CREATED'   => 'Pending Link',
            'AUDITED'   => 'Linking',
            'SENT'      => 'Link Successful',
            'SUCCEEDED' => 'Link Successful',
            'FAILED'    => 'Pending Link',
            'CANCELED'  => 'Pending Link',
        ]
    ],
];
