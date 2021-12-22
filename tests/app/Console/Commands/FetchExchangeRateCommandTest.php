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

namespace Blockchain-Ads\Adserver\Tests\Console\Commands;

use Blockchain-Ads\Adserver\Repository\Common\EloquentExchangeRateRepository;
use Blockchain-Ads\Adserver\Tests\Console\ConsoleTestCase;
use Blockchain-Ads\Common\Application\Service\Exception\ExchangeRateNotAvailableException;
use Blockchain-Ads\Common\Application\Service\ExchangeRateRepository;
use Blockchain-Ads\Mock\Client\DummyExchangeRateRepository;

final class FetchExchangeRateCommandTest extends ConsoleTestCase
{
    public function testFetchExchangeRate(): void
    {
        $this->app->bind(
            ExchangeRateRepository::class,
            function () {
                return new DummyExchangeRateRepository();
            }
        );
        $mockRepository = $this->createMock(EloquentExchangeRateRepository::class);
        $mockRepository->expects($this->once())->method('storeExchangeRate');

        $this->app->bind(
            EloquentExchangeRateRepository::class,
            function () use ($mockRepository) {
                return $mockRepository;
            }
        );

        $this->artisan('ops:exchange-rate:fetch')->assertExitCode(0);
    }

    public function testFetchExchangeRateRepositoryException(): void
    {
        $mockRepository = $this->createMock(ExchangeRateRepository::class);
        $mockRepository->expects($this->once())->method('fetchExchangeRate')->willThrowException(
            new ExchangeRateNotAvailableException()
        );

        $this->app->bind(
            ExchangeRateRepository::class,
            function () use ($mockRepository) {
                return $mockRepository;
            }
        );

        $this->expectException(ExchangeRateNotAvailableException::class);
        $this->artisan('ops:exchange-rate:fetch');
    }
}
