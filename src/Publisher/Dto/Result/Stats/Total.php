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

namespace Blockchain-Ads\Publisher\Dto\Result\Stats;

class Total
{
    /** @var Calculation */
    private $calculation;

    /** @var int|null */
    private $siteId;

    /** @var string|null */
    private $siteName;

    public function __construct(Calculation $calculation, ?int $siteId = null, ?string $siteName = null)
    {
        $this->calculation = $calculation;
        $this->siteId = $siteId;
        $this->siteName = $siteName;
    }

    public function toArray(): array
    {
        $data = $this->calculation->toArray();

        if (null !== $this->siteId && null !== $this->siteName) {
            $data['siteId'] = $this->siteId;
            $data['siteName'] = $this->siteName;
        }

        return $data;
    }
}
