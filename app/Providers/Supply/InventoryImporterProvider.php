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

namespace Blockchain-Ads\Adserver\Providers\Supply;

use Blockchain-Ads\Adserver\Manager\EloquentTransactionManager;
use Blockchain-Ads\Adserver\Repository\Supply\NetworkCampaignRepository;
use Blockchain-Ads\Supply\Application\Service\BannerClassifier;
use Blockchain-Ads\Supply\Application\Service\DemandClient;
use Blockchain-Ads\Supply\Application\Service\InventoryImporter;
use Blockchain-Ads\Supply\Application\Service\MarkedCampaignsAsDeleted;
use Blockchain-Ads\Supply\Domain\Repository\CampaignRepository;
use Blockchain-Ads\Supply\Infrastructure\Service\SodiumCompatClassifyVerifier;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class InventoryImporterProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            CampaignRepository::class,
            function () {
                return new NetworkCampaignRepository();
            }
        );

        $this->app->bind(
            InventoryImporter::class,
            function (Application $app) {
                return new InventoryImporter(
                    new MarkedCampaignsAsDeleted($app->make(CampaignRepository::class)),
                    $app->make(CampaignRepository::class),
                    $app->make(DemandClient::class),
                    $app->make(BannerClassifier::class),
                    new EloquentTransactionManager()
                );
            }
        );
    }
}
