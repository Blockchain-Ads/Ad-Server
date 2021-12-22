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

namespace Blockchain-Ads\Adserver\Services\Publisher;

use Blockchain-Ads\Adserver\Http\Requests\Campaign\TargetingProcessor;
use Blockchain-Ads\Common\Application\Service\ConfigurationRepository;
use Blockchain-Ads\Common\Exception\InvalidArgumentException;

class SiteCategoriesValidator
{
    /** @var ConfigurationRepository */
    private $configurationRepository;

    /** @var TargetingProcessor */
    private $targetingProcessor;

    public function __construct(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    public function processCategories($categories): array
    {
        if (!$categories) {
            throw new InvalidArgumentException('Field `categories` is required.');
        }
        if (!is_array($categories)) {
            throw new InvalidArgumentException('Field `categories` must be an array.');
        }

        if (!$this->targetingProcessor) {
            $this->targetingProcessor = new TargetingProcessor($this->configurationRepository->fetchTargetingOptions());
        }
        $targeting = $this->targetingProcessor->processTargeting(['site' => ['category' => $categories]]);

        if (!$targeting) {
            throw new InvalidArgumentException('Field categories[] must match targeting taxonomy');
        }

        return $targeting['site']['category'];
    }
}
