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

namespace Blockchain-Ads\Adserver\Tests\Console\Commands;

use Blockchain-Ads\Adserver\Models\NetworkCampaign;
use Blockchain-Ads\Adserver\Tests\Console\ConsoleTestCase;
use Blockchain-Ads\Supply\Domain\Repository\CampaignRepository;
use Blockchain-Ads\Supply\Domain\ValueObject\Status;

final class InventoryImporterCommandTest extends ConsoleTestCase
{
    public function testNoHosts(): void
    {
        $this->artisan('ops:demand:inventory:import')->assertExitCode(0);
    }

    public function testNonExistentHosts(): void
    {
        factory(NetworkCampaign::class)->create(['status' => Status::STATUS_ACTIVE]);

        $campaignRepository = $this->createMock(CampaignRepository::class);
        $campaignRepository->expects($this->once())->method('markedAsDeletedBySourceAddress');
        $this->app->bind(
            CampaignRepository::class,
            function () use ($campaignRepository) {
                return $campaignRepository;
            }
        );

        $this->artisan('ops:demand:inventory:import')->assertExitCode(0);
    }
}
