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

namespace Blockchain-Ads\Adserver\Tests\Models;

use Blockchain-Ads\Adserver\Models\SupplyBlacklistedDomain;
use Blockchain-Ads\Adserver\Tests\TestCase;

class SupplyBlacklistedDomainTest extends TestCase
{
    public function testBlacklistedExampleCom(): void
    {
        SupplyBlacklistedDomain::register('example.com');

        $this->assertFalse(SupplyBlacklistedDomain::isDomainBlacklisted('blockchain-ads.com'));
        $this->assertFalse(SupplyBlacklistedDomain::isDomainBlacklisted('dot.com'));

        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('example.com'));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('one.example.com'));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('www.one.example.com'));

        SupplyBlacklistedDomain::register('blockchain-ads.com');

        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('blockchain-ads.com'));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('all.blockchain-ads.com'));
    }

    public function testBlacklistedSpecial(): void
    {
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted(''));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('localhost'));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('127.0.0.1'));
        $this->assertTrue(SupplyBlacklistedDomain::isDomainBlacklisted('fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'));
    }

    public function testBlacklistTwice(): void
    {
        SupplyBlacklistedDomain::register('example.com');
        SupplyBlacklistedDomain::register('example.com');

        $this->assertCount(1, SupplyBlacklistedDomain::all());
    }
}
