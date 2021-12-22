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

namespace Blockchain-Ads\Adserver\Repository\Common;

use Blockchain-Ads\Adserver\Models\ExchangeRate;
use Blockchain-Ads\Common\Application\Dto\ExchangeRate as DomainExchangeRate;
use Blockchain-Ads\Common\Application\Service\Exception\ExchangeRateNotAvailableException;
use DateTime;

class EloquentExchangeRateRepository
{
    private const DATABASE_DATETIME_FORMAT = 'Y-m-d H:i:s';

    public function fetchExchangeRate(DateTime $dateTime = null, string $currency = 'USD'): DomainExchangeRate
    {
        $exchangeRate =
            ExchangeRate::where('valid_at', '<=', (null === $dateTime) ? new DateTime() : $dateTime)
                ->where('currency', $currency)
                ->orderBy('valid_at', 'DESC')
                ->limit(1)
                ->first();

        if (!$exchangeRate) {
            throw new ExchangeRateNotAvailableException();
        }

        return new DomainExchangeRate(
            DateTime::createFromFormat(self::DATABASE_DATETIME_FORMAT, $exchangeRate->valid_at),
            (float)$exchangeRate->value,
            $exchangeRate->currency
        );
    }

    public function storeExchangeRate(DomainExchangeRate $fetchedExchangeRate)
    {
        (new ExchangeRate(
            [
                'valid_at' => $fetchedExchangeRate->getDateTime(),
                'value' => $fetchedExchangeRate->getValue(),
                'currency' => $fetchedExchangeRate->getCurrency(),
            ]
        ))->save();
    }
}
