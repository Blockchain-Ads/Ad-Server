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

namespace Blockchain-Ads\Adserver\Console\Commands;

use Blockchain-Ads\Ads;
use Blockchain-Ads\Ads\AdsClient;

class AdsMe extends BaseCommand
{
    protected $signature = 'ads:me';

    protected $description = 'Prints adserver blockchain account balance';

    public function handle(AdsClient $adsClient)
    {
        if (!$this->lock()) {
            $this->info('Command ' . $this->signature . ' already running');

            return;
        }

        $this->info('Start command ' . $this->signature);

        $me = $adsClient->getMe();
        $this->info(Ads\Util\AdsConverter::clicksToAds($me->getAccount()->getBalance()));
    }
}
