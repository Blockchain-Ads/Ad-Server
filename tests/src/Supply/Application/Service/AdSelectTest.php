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

namespace Blockchain-Ads\Tests\Supply\Application\Service;

use Blockchain-Ads\Adserver\Models\NetworkBanner;
use Blockchain-Ads\Adserver\Models\NetworkCampaign;
use Blockchain-Ads\Adserver\Models\Site;
use Blockchain-Ads\Adserver\Models\User;
use Blockchain-Ads\Adserver\Models\Zone;
use Blockchain-Ads\Adserver\Tests\TestCase;
use Blockchain-Ads\Mock\Client\DummyAdSelectClient;
use Blockchain-Ads\Supply\Application\Dto\ImpressionContext;
use Blockchain-Ads\Supply\Application\Service\AdSelect;
use Blockchain-Ads\Supply\Domain\ValueObject\Status;

class AdSelectTest extends TestCase
{
    public function testFindBanners(): void
    {
        $this->app->bind(
            AdSelect::class,
            function () {
                return new DummyAdSelectClient();
            }
        );

        $user = factory(User::class)->create();
        $site = factory(Site::class)->create(['user_id' => $user->id]);
        $zone = factory(Zone::class)->create(['site_id' => $site->id]);

        $campaign =
            factory(NetworkCampaign::class)->create(
                ['status' => Status::STATUS_ACTIVE, 'publisher_id' => $user->uuid]
            );
        $banner = factory(NetworkBanner::class)->create(
            [
                'network_campaign_id' => $campaign->id,
                'status' => Status::STATUS_ACTIVE,
                'size' => $zone->size,
            ]
        );

        $zones = [['size' => $zone->size, 'zone' => $zone->uuid]];
        $bannerChecksum = $banner->checksum;

        $finder = $this->app->make(AdSelect::class);
        $banners = $finder->findBanners($zones, new ImpressionContext([], [], []));

        self::assertGreaterThan(0, count($banners));
        $this->assertEquals($bannerChecksum, $banners[0]['creative_sha1']);
    }
}
