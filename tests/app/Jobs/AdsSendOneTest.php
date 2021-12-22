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

namespace Blockchain-Ads\Adserver\Tests\Jobs;

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Adserver\Jobs\AdsSendOne;
use Blockchain-Ads\Adserver\Models\User;
use Blockchain-Ads\Adserver\Models\UserLedgerEntry;
use Blockchain-Ads\Adserver\Tests\TestCase;

use function factory;

class AdsSendOneTest extends TestCase
{
    public function testNegativeBalance(): void
    {
        $mockAdsClient = $this->createMock(AdsClient::class);

        $amount = 10000;
        $addressTo = $addressFrom = '0001-00000000-9B6F';

        $user = factory(User::class)->create();

        $userLedgerEntry = UserLedgerEntry::construct(
            $user->id,
            -$amount,
            UserLedgerEntry::STATUS_PENDING,
            UserLedgerEntry::TYPE_WITHDRAWAL
        )->addressed(
            $addressFrom,
            $addressTo
        );
        $userLedgerEntry->save();

        $job = new AdsSendOne($userLedgerEntry, $addressTo, $amount);
        /** @var AdsClient $mockAdsClient */
        $job->handle($mockAdsClient);

        $userLedgerEntries = UserLedgerEntry::all();
        $this->assertCount(1, $userLedgerEntries);
        $this->assertEquals(UserLedgerEntry::STATUS_REJECTED, $userLedgerEntries->get(0)->status);
    }
}
