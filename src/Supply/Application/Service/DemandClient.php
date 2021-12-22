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

namespace Blockchain-Ads\Supply\Application\Service;

use Blockchain-Ads\Common\Domain\ValueObject\AccountId;
use Blockchain-Ads\Common\UrlInterface;
use Blockchain-Ads\Supply\Application\Dto\Info;
use Blockchain-Ads\Supply\Domain\Model\CampaignCollection;

interface DemandClient
{
    public function fetchAllInventory(
        AccountId $sourceAddress,
        string $sourceHost,
        string $inventoryUrl
    ): CampaignCollection;

    public function fetchPaymentDetails(string $host, string $transactionId, int $limit, int $offset): array;

    public function fetchInfo(UrlInterface $infoUrl): Info;
}
