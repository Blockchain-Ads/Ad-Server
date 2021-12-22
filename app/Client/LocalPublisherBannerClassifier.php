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

namespace Blockchain-Ads\Adserver\Client;

use Blockchain-Ads\Adserver\Models\NetworkBanner;
use Blockchain-Ads\Classify\Domain\Model\Classification;
use Blockchain-Ads\Supply\Application\Dto\Classification\Collection;
use Blockchain-Ads\Supply\Application\Service\BannerClassifier;
use Blockchain-Ads\Classify\Application\Service\ClassifierInterface;
use Blockchain-Ads\Classify\Application\Exception\BannerNotVerifiedException;

class LocalPublisherBannerClassifier implements BannerClassifier
{
    private $classifier;

    public function __construct(ClassifierInterface $classifier)
    {
        $this->classifier = $classifier;
    }

    public function fetchBannersClassification(array $bannerIds): Collection
    {
        $collection = new Collection();
        $publicIdsToInternalIdsMap = NetworkBanner::findIdsByUuids($bannerIds);

        foreach ($publicIdsToInternalIdsMap as $publicId => $internalId) {
            try {
                $classificationCollection = $this->classifier->fetch($internalId);

                $namespaceToKeywordsMap = [];
                /** @var Classification $classification */
                foreach ($classificationCollection as $classification) {
                    $namespaceToKeywordsMap[$classification->getNamespace()][] = $classification->keyword();
                }

                if (empty($namespaceToKeywordsMap)) {
                    $collection->addEmptyClassification($publicId);

                    continue;
                }

                foreach ($namespaceToKeywordsMap as $namespace => $keywords) {
                    $collection->addClassification($publicId, $namespace, $keywords);
                }
            } catch (BannerNotVerifiedException $exception) {
                $collection->addEmptyClassification($publicId);
            }
        }

        return $collection;
    }
}
