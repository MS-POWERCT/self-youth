<?php

namespace App\Services;

/**
 * 创意昵称生成，如「沉默的马铃薯」「快乐香蕉大王」
 */
class CreativeNameService
{
    public const PATTERN_DE = 'de';       // a + 的 + b
    public const PATTERN_TRIPLE = 'triple'; // a + b + c

    /** @var string[] */
    private static array $adjectives = [
        '沉默', '快乐', '忧郁', '暴躁', '温柔', '高冷', '迷糊', '倔强',
        '佛系', '慵懒', '傲娇', '社恐', '躺平', '摆烂', '迷路', '失忆',
        '摸鱼', '熬夜', '膨胀', '抽象', '内卷', '发电', '摆烂', '社牛',
        '脆弱', '坚强', '神秘', '闪亮', '孤独', '自由', '倔强', '天真',
    ];

    /** @var string[] */
    private static array $nouns = [
        '马铃薯', '香蕉', '西瓜', '番茄', '苦瓜', '汉堡', '奶茶', '螺蛳粉',
        '榴莲', '柠檬', '熊猫', '企鹅', '柴犬', '柯基', '仓鼠', '仙人掌',
        '向日葵', '云朵', '星星', '月亮', '拖鞋', '袜子', '键盘', '鼠标',
        '充电器', '回形针', '章鱼', '海豚', '胡萝卜', '西兰花', '吐司', '曲奇',
    ];

    /** @var string[] 三段式后缀 */
    private static array $suffixes = [
        '今天', '大王', '选手', '爱好者', '研究员', '观察员', '冠军', '专家',
        '小队长', '练习生', '守护者', '收藏家', '探险家', '代言人', '体验官',
        '指挥官', '飞行员', '驾驶员', '管理员', '鉴赏家',
    ];

    /**
     * 随机生成一个昵称（随机 de 或 triple 格式）
     */
    public static function generate(): string
    {
        return random_int(0, 1) === 0
            ? self::generateDe()
            : self::generateTriple();
    }

    /**
     * a + 的 + b，如「沉默的马铃薯」
     */
    public static function generateDe(?array $adjectives = null, ?array $nouns = null): string
    {
        $a = self::pick($adjectives ?? self::$adjectives);
        $b = self::pick($nouns ?? self::$nouns);

        return $a . '的' . $b;
    }

    /**
     * a + b + c，如「快乐香蕉大王」
     */
    public static function generateTriple(
        ?array $first = null,
        ?array $second = null,
        ?array $third = null
    ): string {
        $a = self::pick($first ?? self::$adjectives);
        $b = self::pick($second ?? self::$nouns);
        $c = self::pick($third ?? self::$suffixes);

        return $a . $b . $c;
    }

    /**
     * 按指定格式生成
     *
     * @param string $pattern self::PATTERN_DE | self::PATTERN_TRIPLE
     */
    public static function generateByPattern(string $pattern): string
    {
        return match ($pattern) {
            self::PATTERN_DE => self::generateDe(),
            self::PATTERN_TRIPLE => self::generateTriple(),
            default => self::generate(),
        };
    }

    /**
     * 批量生成
     *
     * @param string|null $pattern null 表示两种格式随机
     * @return string[]
     */
    public static function generateBatch(int $count = 10, ?string $pattern = null): array
    {
        $names = [];
        for ($i = 0; $i < $count; $i++) {
            $names[] = $pattern === null
                ? self::generate()
                : self::generateByPattern($pattern);
        }

        return $names;
    }

    private static function pick(array $items): string
    {
        return $items[array_rand($items)];
    }
}
