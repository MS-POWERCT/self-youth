<?php

namespace App\Services3rd;

use App\Services\ApiService;
use Illuminate\Support\Facades\Log;

/**
 * @author Administrator
 */
class BscscanService
{


    /**
     */
    public static function sendTransaction($to, $amount, $id, $asser_id) {}

    /**
     */
    public static function deleteSendTransaction($id) {}




    public static function getRPCUrl()
    {
        return "https://bsc.blockpi.network/v1/rpc/a209f4eb1955f4005208e139a88b7505f6dedff2";
    }



    // 获取最新区块高度
    public static function blockNumber()
    {
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_blockNumber",
            "params" => [],
            "id" => 1
        ];
        $response = ApiService::json_post(self::getRPCUrl(), $data);

        if (isset($response['result'])) {
            return hexdec($response['result']);
        }
        return null;
    }

    // 得到地址余额
    public static function getBalance($address)
    {
        try {
            $data = [
                "jsonrpc" => "2.0",
                "method" => "eth_getBalance",
                "params" => [$address, "latest"],
                "id" => 1
            ];
            $response = ApiService::json_post(self::getRPCUrl(), $data);

            if (isset($response['result'])) {
                // 将十六进制结果转换为十进制
                $balanceHex = $response['result'];
                $balanceWei = hexdec($balanceHex);

                // 转换为可读格式 (假设代币有18位小数)
                $balance = $balanceWei / pow(10, 18);

                return $balance;
            }

            return '0';
        } catch (\Exception $e) {
            Log::error('获取合约代币余额异常：' . $e->getMessage());
            return '0';
        }
    }
    /**
     * 获取合约代币余额
     * @param string $address 钱包地址
     * @param string $network 网络类型 (testnet/mainnet)
     * @param string $contractAddress 合约地址
     * @return string 余额 (wei)
     */
    public static function getContractBalance($address, $contractAddress)
    {
        try {
            // 构建balanceOf方法调用数据
            $methodSignature = '0x70a08231'; // balanceOf(address) 的方法签名
            $addressParam = str_pad(substr($address, 2), 64, '0', STR_PAD_LEFT); // 移除0x并补齐到64位
            $data = $methodSignature . $addressParam;

            $requestData = [
                "jsonrpc" => "2.0",
                "method" => "eth_call",
                "params" => [
                    [
                        "to" => $contractAddress,
                        "data" => $data
                    ],
                    "latest"
                ],
                "id" => 1
            ];

            $response = ApiService::json_post(self::getRPCUrl(), $requestData);

            if (isset($response['result'])) {
                // 将十六进制结果转换为十进制
                $balanceHex = $response['result'];
                $balanceWei = hexdec($balanceHex);

                // 转换为可读格式 (假设代币有18位小数)
                $balance = $balanceWei / pow(10, 18);

                return $balance;
            }

            return '0';
        } catch (\Exception $e) {
            Log::error('获取合约代币余额异常：' . $e->getMessage());
            return '0';
        }
    }



    // 或者这个hash的交易信息
    public static function getTransactionReceipt($hash)
    {
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_getTransactionReceipt",
            "params" => [$hash],
            "id" => 1
        ];

        $response = ApiService::json_post(self::getRPCUrl(), $data);

        if (isset($response['result'])) {
            return $response['result'];
        }
        return null;
    }


    // 获取本合约代币的一个高度的全部信息
    public static function getBlockByNumber($number)
    {
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_getBlockByNumber",
            "params" => ['0x' . dechex($number), true],
            "id" => 1
        ];

        $response = ApiService::json_post(self::getRPCUrl(), $data);

        if (isset($response['result'])) {
            return $response['result'];
        }
        return null;
    }


    /**
     * 获取特定合约地址中涉及特定钱包地址的最近交易日志
     *
     * @param string $contract_address 合约地址（如USDT）
     * @param string $wallet_address  要监控的钱包地址
     * @param string $from_block      起始区块（可选，默认"latest" - 最新区块）
     * @param string $to_block        结束区块（可选，默认"latest" - 最新区块）
     * @return array|null
     */
    public static function getLogs($contract_address, $wallet_address, $from_block = "latest", $to_block = "latest")
    {
        // 构造JSON-RPC请求数据
        $data = [
            "jsonrpc" => "2.0",
            "method" => "eth_getLogs",
            "params" => [
                [
                    "fromBlock" => '0x' . dechex($from_block),
                    "toBlock" => '0x' . dechex($to_block),
                    "address" => strtolower($contract_address),
                    "topics" => [
                        '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef',
                        null, // 任意发送方
                        ["0x" . str_pad(substr(strtolower($wallet_address), 2), 64, '0', STR_PAD_LEFT)] // 接收方必须是目标地址
                    ]
                ]
            ],
            "id" => 1
        ];

        // 发送请求到BNB节点的RPC接口
        $response = ApiService::json_post(self::getRPCUrl(), $data);

        if (isset($response['result'])) {
            return $response['result'];
        }
        return null;
    }
}
