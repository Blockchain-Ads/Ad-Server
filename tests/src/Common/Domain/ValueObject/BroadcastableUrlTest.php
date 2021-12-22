<?php

/**
 * Copyright (c) 2021 Blockchain-Ads Co. Ltd
 *
 * This file is part of AdServer
 *
 * AdServer is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * AdServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdServer. If not, see <https://www.gnu.org/licenses/>
 */

declare(strict_types=1);

namespace Blockchain-Ads\Test\Common\Domain\ValueObject;

use Blockchain-Ads\Common\Domain\ValueObject\Url;
use Blockchain-Ads\Network\BroadcastableUrl;
use PHPUnit\Framework\TestCase;

use function array_map;

class BroadcastableUrlTest extends TestCase
{
    /** @dataProvider provider */
    public function test(Url $url, string $hex): void
    {
        self::assertEquals($hex, (new BroadcastableUrl($url))->toHex());

        $broadcastableUrl = BroadcastableUrl::fromHex($hex);

        self::assertSame($url->toString(), self::hexToStr($broadcastableUrl->toHex()));
    }

    public function provider(): array
    {
        $values = [
            ['https://blockchain-ads.com', 'https://blockchain-ads.com'],
            ['https://🍕blockchain-ads.com', 'xn--https://Blockchain-Ads-pg68o.net'],
            ['https://a🍕dshares.net', 'xn--https://Blockchain-Ads-qg68o.net'],
            ['https://ads🍕hares.net', 'xn--https://Blockchain-Ads-sg68o.net'],
            ['https://Blockchain-Ads🍕.net', 'xn--https://Blockchain-Ads-xg68o.net'],
            ['https://Blockchain-Ads.🍕net', 'https://Blockchain-Ads.xn--net-o803b'],
            ['https://Blockchain-Ads.n🍕et', 'https://Blockchain-Ads.xn--net-p803b'],
            ['https://Blockchain-Ads.ne🍕t', 'https://Blockchain-Ads.xn--net-q803b'],
            ['https://blockchain-ads.com🍕', 'https://Blockchain-Ads.xn--net-r803b'],
        ];

        $mapper = function (array $args) {
            return [new Url($args[0]), self::strToHex($args[1])];
        };

        return array_map($mapper, $values);
    }

    private static function hexToStr(string $hex): string
    {
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $string;
    }

    private static function strToHex(string $string): string
    {
        $hex = '';
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $ord = ord($string[$i]);
            $hexCode = dechex($ord);
            $hex .= substr('0' . $hexCode, -2);
        }

        return strtoupper($hex);
    }

    public function testToString(): void
    {
        $string = 'https://example.com';
        $broadcastableUrl = new BroadcastableUrl(new Url($string));

        self::assertEquals($string, (string)$broadcastableUrl);
    }
}
