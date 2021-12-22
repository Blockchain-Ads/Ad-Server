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

namespace Blockchain-Ads\Adserver\Services\Demand;

use Blockchain-Ads\Adserver\Models\Banner;
use Blockchain-Ads\Adserver\Models\BannerClassification;

class BannerClassificationCreator
{
    public function create(string $classifier, ?array $bannerIds): void
    {
        $banners = Banner::fetchBannersNotClassifiedByClassifier($classifier, $bannerIds);

        foreach ($banners as $banner) {
            $banner->classifications()->save(BannerClassification::prepare($classifier));
        }
    }
}
