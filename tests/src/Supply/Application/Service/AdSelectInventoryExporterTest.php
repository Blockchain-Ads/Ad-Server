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

namespace Blockchain-Ads\Tests\Supply\Application\Service;

use Blockchain-Ads\Common\Domain\ValueObject\Uuid;
use Blockchain-Ads\Supply\Application\Service\AdSelect;
use Blockchain-Ads\Supply\Application\Service\AdSelectInventoryExporter;
use Blockchain-Ads\Supply\Domain\Model\Campaign;
use Blockchain-Ads\Supply\Domain\Model\CampaignCollection;
use Blockchain-Ads\Supply\Domain\Repository\CampaignRepository;
use Blockchain-Ads\Supply\Domain\ValueObject\Budget;
use Blockchain-Ads\Supply\Domain\ValueObject\CampaignDate;
use Blockchain-Ads\Supply\Domain\ValueObject\SourceCampaign;
use Blockchain-Ads\Supply\Domain\ValueObject\Status;
use DateTime;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class AdSelectInventoryExporterTest extends TestCase
{
    public function testWhenNoBannersForGivenCampaign(): void
    {
        $campaignId = Uuid::v4();
        $campaign = new Campaign(
            $campaignId,
            UUid::fromString('4a27f6a938254573abe47810a0b03748'),
            'http://example.com',
            new CampaignDate(new DateTime(), (new DateTime())->modify('+1 hour'), new DateTime(), new DateTime()),
            [],
            new Budget(1000000000000, null, 200000000000),
            new SourceCampaign('localhost', '0000-00000000-0001', '0.1', new DateTime(), new DateTime()),
            Status::processing(),
            [],
            []
        );

        $client = $this->createMock(AdSelect::class);
        $client
            ->expects($this->once())
            ->method('exportInventory');

        $logger = new NullLogger();
        $service = new AdSelectInventoryExporter($client, $this->createMock(CampaignRepository::class), $logger);
        $service->export(new CampaignCollection($campaign), new CampaignCollection());
    }
}
