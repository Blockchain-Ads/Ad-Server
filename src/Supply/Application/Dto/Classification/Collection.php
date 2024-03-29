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

namespace Blockchain-Ads\Supply\Application\Dto\Classification;

use Blockchain-Ads\Common\Domain\Adapter\ArrayCollection;
use Blockchain-Ads\Supply\Domain\ValueObject\Classification;

class Collection extends ArrayCollection
{
    public function addClassification(string $bannerId, string $classifier, array $keywords): void
    {
        $elements = $this->get($bannerId) ?? [];
        $elements[] = new Classification($classifier, $keywords);

        $this->set($bannerId, $elements);
    }

    public function addEmptyClassification(string $bannerId): void
    {
        $this->set($bannerId, []);
    }

    public function findByBannerId(string $bannerId): ?array
    {
        return $this->get($bannerId);
    }
}
