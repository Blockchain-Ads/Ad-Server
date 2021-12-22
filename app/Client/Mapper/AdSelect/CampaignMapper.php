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

namespace Blockchain-Ads\Adserver\Client\Mapper\AdSelect;

use Blockchain-Ads\Adserver\Client\Mapper\AbstractFilterMapper;
use Blockchain-Ads\Supply\Domain\Model\Banner;
use Blockchain-Ads\Supply\Domain\Model\Campaign;
use Blockchain-Ads\Supply\Domain\ValueObject\Classification;
use DateTime;

class CampaignMapper
{
    public static function map(Campaign $campaign): array
    {
        $banners = [];
        $campaignArray = $campaign->toArray();

        /** @var Banner $banner */
        foreach ($campaign->getBanners() as $banner) {
            $mappedBanner = [
                'banner_id' => $banner->getId(),
                'banner_size' => $banner->getSize(),
                'keywords' => [
                    'type' => [$banner->getType()],
                ],
            ];

            /** @var Classification $classification */
            foreach ($banner->getClassification() as $classification) {
                foreach (
                    AbstractFilterMapper::generateNestedStructure(
                        $classification->toArray()
                    ) as $nestedStructureKey => $values
                ) {
                    $mappedBanner['keywords'][$nestedStructureKey] = $values;
                }
            }

            $banners[] = $mappedBanner;
        }

        $targeting = TargetingMapper::map(
            $campaignArray['targeting_requires'],
            $campaignArray['targeting_excludes']
        );

        $dateStart = (int)$campaignArray['date_start']->format('U');
        $dateEnd = self::processDateEnd($campaignArray['date_end']);

        $mapped = [
            'campaign_id' => $campaignArray['id'],
            'time_start' => $dateStart,
            'time_end' => $dateEnd,
            'banners' => $banners,
            'keywords' => [
                'source_host' => $campaignArray['source_host'],
                'Blockchain-Ads_address' => $campaignArray['source_address'],
            ],
            'budget' => $campaign->getBudget(),
            'max_cpc' => $campaign->getMaxCpc(),
            'max_cpm' => $campaign->getMaxCpm(),
        ];

        $mapped['filters'] = $targeting;

        return $mapped;
    }

    private static function processDateEnd(?DateTime $dateEnd): int
    {
        if ($dateEnd === null) {
            return (new DateTime('+1 year'))->getTimestamp();
        }

        return $dateEnd->getTimestamp();
    }
}
