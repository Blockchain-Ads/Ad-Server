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
use Blockchain-Ads\Common\Exception\RuntimeException;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /** @dataProvider provider */
    public function test(string $url, string $idn): void
    {
        $object = new Url($url);

        self::assertSame($url, $object->utf8());
        self::assertSame($idn, $object->toString());
    }

    /** @dataProvider failureProvider */
    public function testFailure($url): void
    {
        $this->expectException(RuntimeException::class);

        new Url($url);
    }

    public function provider(): array
    {
        return [
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
    }

    public function failureProvider()
    {
        return [
            ['AdServer.https%3A%2F%2Fblockchain-ads.com'],
            ['https%3A%2F%2Fblockchain-ads.com'],
            ['blockchain-ads.com'],
        ];
    }

    public function testToString(): void
    {
        $string = 'https://example.com';
        $url = new Url($string);

        self::assertEquals($string, (string)$url);
    }
}
