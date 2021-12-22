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

namespace Blockchain-Ads\Adserver\Tests\Repository\Common;

use Blockchain-Ads\Adserver\Repository\Common\EloquentExchangeRateRepository;
use Blockchain-Ads\Adserver\Tests\TestCase;
use Blockchain-Ads\Common\Application\Dto\ExchangeRate;
use Blockchain-Ads\Common\Application\Service\Exception\ExchangeRateNotAvailableException;
use DateTime;

final class EloquentExchangeRateRepositoryTest extends TestCase
{
    public function testExchangeRateRepositoryFetchWhileEmpty(): void
    {
        $repository = new EloquentExchangeRateRepository();

        $this->expectException(ExchangeRateNotAvailableException::class);
        $repository->fetchExchangeRate();
    }

    public function testExchangeRateRepositoryStoreAndFetch(): void
    {
        $repository = new EloquentExchangeRateRepository();

        $dateTime = new DateTime();
        $dateTime->setTime((int)$dateTime->format('H'), (int)$dateTime->format('i'));

        $exchangeRate = new ExchangeRate($dateTime, 1.3, 'USD');
        $repository->storeExchangeRate($exchangeRate);
        $exchangeRateFromRepository = $repository->fetchExchangeRate();

        $this->assertEquals($exchangeRate, $exchangeRateFromRepository);
    }
}
