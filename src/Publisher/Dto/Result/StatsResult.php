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

namespace Blockchain-Ads\Publisher\Dto\Result;

use Blockchain-Ads\Publisher\Dto\Result\Stats\Total;
use Blockchain-Ads\Publisher\Dto\Result\Stats\DataCollection;

class StatsResult
{
    private $total;
    private $data;

    public function __construct(Total $total, DataCollection $data)
    {
        $this->total = $total;
        $this->data = $data;
    }

    public function getTotal(): array
    {
        return $this->total->toArray();
    }

    public function getData(): array
    {
        return $this->data->toArray();
    }
}
