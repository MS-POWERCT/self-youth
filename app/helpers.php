<?php


// 得到接口缓存的key
use Illuminate\Support\Facades\Log;

if (!function_exists('getApiCacheKey')) {
    function getApiCacheKey($name)
    {
        return 'route:' . $name;
    }
}

// 得到redis配置的prefix长度
if (!function_exists('getRedisPrefixLen')) {
    function getRedisPrefixLen()
    {
        return strlen(config('database.redis.options.prefix'));
    }
}

// 得到colors
if (!function_exists('getRandColors')) {
    function getRandColors(int $size = 10)
    {
        $colors = [];
        for ($i = 0; $i < $size; $i++) {
            $colors[(string)$i] = sprintf('#%06X', mt_rand(0, 0xFFFFFF)); // 强制键转为字符串
        }
        return $colors;
    }
}

if (!function_exists('decryptData')) {

    //  WNxTC7CKB5lvwOH9WGHbeHqB8XQ++PnqBSoaUgx+55htMXgpIkXsX652Hgo1r6uLkTkpw3RNSD+8Iv7ZLq2GKZAGBaYk0ec2J17J2cU/YXXuZVhOCnqbfKEPPdeReIiJ663439613631323430376566346466373822
    //  2K2hxqhpha/XihNRNJ46r7I++0p++uSiviTd+QUrH1G+HlUgJelcYdfgS3IPonSu6nVGVOcksx4JRu++7qXy7ZZT6x+qzs9+lXA9tVnhPMg=313233343536373830303030303030303030303030303030303030303030303040

    function decryptData($encryptedData)
    {
        // 1. 提取密钥长度（最后2个字符）
        $keyLenHex = substr($encryptedData, -2);
        $realLen = hexdec($keyLenHex);  // 转换为十进制
        // 2. 提取密钥的HEX编码部分
        $keyStartPos = strlen($encryptedData) - 2 - $realLen;
        $keyHex = substr($encryptedData, $keyStartPos, $realLen);

        // 3. HEX转原始密钥
        $rawKey = hex2bin($keyHex);
        if ($rawKey === false) {
            throw new Exception("Invalid HEX key");
        }

        // 4. 提取Base64密文
        $base64Cipher = substr($encryptedData, 0, $keyStartPos);

        // 5. Base64解码
        $cipherText = base64_decode($base64Cipher);
        if ($cipherText === false) {
            throw new Exception("Base64 decode failed");
        }

        // 6. 固定IV
        $iv = config('app.aes_key');
        // 7. 使用AES-CBC解密（支持自动PKCS7填充）
        $decrypted = openssl_decrypt(
            $cipherText,
            config('app.cipher'),
            $rawKey,
            OPENSSL_RAW_DATA,
            $iv
        );

        // 8. 验证解密结果
        if ($decrypted === false) {
            throw new Exception("Decryption failed: " . openssl_error_string());
        }

        return $decrypted;
    }
}

if (!function_exists('floor_2')) {
    function floor_2($num)
    {
        $num = (float)$num;
        return floor(round($num * 100, 10)) / 100;
    }
}
if (!function_exists('floor_4')) {
    function floor_4($num)
    {
        $num = (float)$num;
        return floor(round($num * 10000, 10)) / 10000;
    }
}
if (!function_exists('trim_decimal')) {
    function trim_decimal($num)
    {
        // 确保是字符串类型处理
        $str = (string)$num;

        // 只有包含小数点时才进行去除操作
        if (strpos($str, '.') !== false) {
            $str = rtrim(rtrim($str, '0'), '.');
        }

        return $str;
    }
}

if (!function_exists('safe_divide')) {
    function safe_divide($dividend, $divisor, $default = 0)
    {
        // 双重检查：数值性和非零性
        if (!is_numeric($divisor) || (float)$divisor == 0) {
            return $default;
        }

        // 确保被除数也是数值
        $dividend = is_numeric($dividend) ? (float)$dividend : $default;

        return $dividend / (float)$divisor;
    }
}
if (!function_exists('getDaysDiff')) {
    function getDaysDiff($dateTime)
    {
        // 处理时间参数
        if (!$dateTime instanceof DateTime) {
            $dateTime = new DateTime($dateTime);
        }

        $now = new DateTime();
        $interval = $now->diff($dateTime);
        return (int)$interval->days;
    }
}
