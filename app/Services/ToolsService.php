<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\Llconfig;
use Illuminate\Support\Facades\Redis;

class ToolsService
{

    /**
     * 生成指定类型和长度的字符串
     * @param int $length 字符串长度，默认为 6
     * @param int $type (0:纯数字默认, 1:数字+字母, 2:纯字母, 3:特殊符号, 4:数字+字母+特殊符号)
     * @return string 生成的字符串
     */
    public static function getRandomStr($length = 6, $type = 0)
    {
        $characters = '';
        switch ($type) {
            case 1:
                $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                break;
            case 2:
                $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                break;
            case 3:
                $characters = '!@#$%^&*()-_+=~`[]{}|:;,.<>?';
                break;
            case 4:
                $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$%^&*()-_+=~`[]{}|:;,.<>?';
                break;
            default:
                $characters = '0123456789';
                break;
        }

        $string = '';
        while (strlen($string) < $length) {
            $string .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $string;
    }



    /**
     * 采用递归将数据列表转换成树
     *
     * @param $dataArr           数据列表
     * @param int $rootId        根节点ID
     * @param string $pkName     主键名
     * @param string $pIdName    父节点id名
     * @param string $childName  子节点名称
     * @return array
     */
    public function ListToTreeRecursive($dataArr, $rootId = 0)
    {
        $arr = [];
        foreach ($dataArr as $sorData) {
            if ($sorData['parent_id'] == $rootId) {
                $children = $this->ListToTreeRecursive($dataArr, $sorData['id']);
                if ($children) {
                    $sorData['childName_count'] = count($children);
                    $sorData['childName'] = $children;
                } else {
                    $sorData['childName_count'] = 0;
                    $sorData['childName'] = [];
                }

                $arr[] = $sorData;
            }
        }

        return $arr;
    }



    // 判断身份证是否正确
    public static function validateChineseId($idNumber)
    {
        // 验证身份证号码长度是否为18位
        if (strlen($idNumber) != 18) {
            return false;
        }

        // 验证前17位是否为数字
        if (!is_numeric(substr($idNumber, 0, 17))) {
            return false;
        }

        // 验证最后一位校验码是否正确
        $checkCode = strtoupper($idNumber[17]);
        $weights = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
        $checksum = 0;
        for ($i = 0; $i < 17; $i++) {
            $checksum += intval($idNumber[$i]) * $weights[$i];
        }
        $checkSumMapping = array(0 => '1', 1 => '0', 2 => 'X', 3 => '9', 4 => '8', 5 => '7', 6 => '6', 7 => '5', 8 => '4', 9 => '3', 10 => '2');
        if ($checkCode !== $checkSumMapping[$checksum % 11]) {
            return false;
        }

        // 验证省、市、出生日期等其他规则（省略）

        return true;
    }



    // 效验身份证
    public static function validateIdCardNumber($idCardNumber)
    {
        $pattern = '/^\d{17}[\dXx]$/'; // 正则表达式模式
        if (preg_match($pattern, $idCardNumber)) { // 使用preg_match函数进行匹配
            $weights = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); // 权重数组
            $checksums = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); // 校验码数组
            $checksum = 0;
            for ($i = 0; $i < 17; $i++) {
                $checksum += intval($idCardNumber[$i]) * $weights[$i]; // 计算加权和
            }
            $remainder = $checksum % 11; // 取模运算
            if ($idCardNumber[17] == $checksums[$remainder]) {
                return true; // 校验成功
            }
        }
        return false; // 校验失败
    }

    // 计算身份证最后一位
    public static function calculateIdCardCheckDigit($idCardFirst17Digits)
    {
        $coefficients = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2];
        $checkCodeMap = ['1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'];
        // 验证输入长度
        if (strlen($idCardFirst17Digits) != 17) {
            return '长度不正确'; // 输入长度不正确
        }
        $sum = 0;
        for ($i = 0; $i < 17; $i++) {
            $sum += intval($idCardFirst17Digits[$i]) * $coefficients[$i];
        }
        // 取模运算
        $mod = $sum % 11;
        return $checkCodeMap[$mod];
    }

    // 通过身份证号获取年龄
    public static function getAgeByIdCard($idCard)
    {
        $birthYear = substr($idCard, 6, 4);
        $birthMonth = substr($idCard, 10, 2);
        $birthDay = substr($idCard, 12, 2);
        $age = date('Y') - $birthYear;
        if (date('m') < $birthMonth || (date('m') == $birthMonth && date('d') < $birthDay)) {
            $age--;
        }
        return $age;
    }

    public static function getRealIp($ip)
    {
        if (strstr($ip, ",")) {
            $ip_arr = explode(',', $ip);
            foreach ($ip_arr as $ip) {
                $ipint = sprintf('%u', ip2long($ip)); #ip2long — 将 IPV4 的字符串互联网协议转换成长整型数字
                if (
                    $ipint >= 0 && $ipint <= 50331647 || // {"0.0.0.0","2.255.255.255"},
                    $ipint >= 167772160 && $ipint <= 184549375 || // {"10.0.0.0","10.255.255.255"},
                    $ipint >= 2130706432 && $ipint <= 2147483647 || // {"127.0.0.0","127.255.255.255"},
                    $ipint >= 2851995648 && $ipint <= 2852061183 || // {"169.254.0.0","169.254.255.255"}
                    $ipint >= 2886729728 && $ipint <= 2887778303 || // {"172.16.0.0","172.31.255.255"},
                    $ipint >= 3221225984 && $ipint <= 3221226239 || // {"192.0.2.0","192.0.2.255"},
                    $ipint >= 3232235520 && $ipint <= 3232301055 || // {"192.168.0.0","192.168.255.255"},
                    $ipint >= 4294967040 && $ipint <= 4294967295 // {"255.255.255.0","255.255.255.255"}
                ) {
                    continue;
                } else {
                    break;
                }
            }
        }
        return trim($ip);
    }



    // 获取缓存
    public static function getCache($name)
    {
        $cacheKey = 'llconfig:' . $name;
        if (!Redis::exists($cacheKey)) {
            $llconfig = Llconfig::where('name', $name)->first();
            if (!$llconfig) {
                return 0;
            }
            $value = $llconfig->value;
            Redis::set($cacheKey, $value);
        } else {
            $value = Redis::get($cacheKey);
        }
        return $value;
    }


    // 删除缓存
    public static function delCache($name)
    {
        $cacheKey = 'llconfig:' . $name;
        Redis::del($cacheKey);
    }

    public static function getLlconfigOption()
    {
        return Llconfig::where('option', 1)->pluck('value', 'name')->toArray();
    }

    // 修改llconfig
    public static function updateLlconfig($name, $value)
    {
        Llconfig::where('name', $name)->update(['value' => $value]);
        $cacheKey = 'llconfig:' . $name;
        Redis::set($cacheKey, $value);
    }



    /**
     * 将秒数转换为友好的时间格式
     * 规则：大于天显示天，只有分钟不显示小时
     *
     * @param int $seconds 秒数
     * @return string 格式化后的时间字符串
     */
    public static function formatSeconds(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        $parts = [];

        // 如果大于天，显示天
        if ($days > 0) {
            $parts[] = $days . '天';
            // 显示天的情况下，同时显示小时
            if ($hours > 0) {
                $parts[] = $hours . '小时';
            }
        } else {
            // 不显示天的情况下
            // 如果只有分钟和秒（小时为0），不显示小时
            if ($hours > 0) {
                $parts[] = $hours . '小时';
            }
        }

        // 显示分钟（如果有）
        if ($minutes > 0) {
            $parts[] = $minutes . '分钟';
        }

        // 显示秒
        $parts[] = $secs . '秒';

        return implode('', $parts);
    }


    // 获取资产名称
    public static function getAssetName(int $asset_id): string
    {
        $cacheKey = sprintf('asset_name:%s', $asset_id);

        // 尝试从缓存获取
        $name = Redis::get($cacheKey);

        if ($name !== null) {
            return $name;
        }

        // 缓存未命中，从数据库获取
        $asset = Asset::findOrFail($asset_id, ['name']);

        // 缓存资产名称
        Redis::set($cacheKey, $asset->name);

        return $asset->name;
    }
}
