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

namespace Blockchain-Ads\Tests\Common\Infrastructure\Service;

use Blockchain-Ads\Adserver\Repository\Common\EloquentExchangeRateRepository;
use Blockchain-Ads\Adserver\Tests\TestCase;
use Blockchain-Ads\Common\Application\Dto\ExchangeRate;
use Blockchain-Ads\Common\Application\Service\Exception\ExchangeRateNotAvailableException;
use Blockchain-Ads\Common\Application\Service\ExchangeRateRepository;
use Blockchain-Ads\Common\Infrastructure\Service\ExchangeRateReader;
use DateTime;

class ExchangeRateReaderTest extends TestCase
{
    public function testExchangeRateReaderEmptyStorageAndRemoteFail(): void
    {
        $repositoryRemote = $this->createMock(ExchangeRateRepository::class);
        $repositoryRemote->expects($this->once())->method('fetchExchangeRate')->willThrowException(
            new ExchangeRateNotAvailableException()
        );

        $repositoryStorable = $this->createMock(EloquentExchangeRateRepository::class);
        $repositoryStorable->expects($this->once())->method('fetchExchangeRate')->willThrowException(
            new ExchangeRateNotAvailableException()
        );
        $repositoryStorable->expects($this->never())->method('storeExchangeRate');

        $exchangeRateReader = new ExchangeRateReader($repositoryStorable, $repositoryRemote);

        $this->expectException(ExchangeRateNotAvailableException::class);
        $exchangeRateReader->fetchExchangeRate();
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testExchangeRateReaderEmptyStorageAndRemoteSuccess(float $exchangeRateValue): void
    {
        $exchangeRateDateTime = null;

        $repositoryRemote = $this->createMock(ExchangeRateRepository::class);
        $repositoryRemote->expects($this->once())->method('fetchExchangeRate')->willReturnCallback(
            function (DateTime $dateTime) use ($exchangeRateValue, &$exchangeRateDateTime) {
                $exchangeRateDateTime = (clone $dateTime)->setTime((int)$dateTime->format('H'), 0);

                return new ExchangeRate($exchangeRateDateTime, $exchangeRateValue, 'USD');
            }
        );

        $repositoryStorable = $this->createMock(EloquentExchangeRateRepository::class);
        $repositoryStorable->expects($this->once())->method('fetchExchangeRate')->willThrowException(
            new ExchangeRateNotAvailableException()
        );
        $repositoryStorable->expects($this->once())->method('storeExchangeRate');

        $exchangeRateReader = new ExchangeRateReader($repositoryStorable, $repositoryRemote);

        $exchangeRate = $exchangeRateReader->fetchExchangeRate();
        $this->assertEquals($exchangeRateValue, $exchangeRate->getValue());
        $this->assertEquals($exchangeRateDateTime, $exchangeRate->getDateTime());
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testExchangeRateReaderEmptyStorageAndRemoteOldValue(float $exchangeRateValue): void
    {
        $exchangeRateDateTime = (new DateTime())->modify('-1 year');

        $repositoryRemote = $this->createMock(ExchangeRateRepository::class);
        $repositoryRemote->expects($this->once())->method('fetchExchangeRate')->willReturnCallback(
            function () use ($exchangeRateValue, $exchangeRateDateTime) {
                return new ExchangeRate($exchangeRateDateTime, $exchangeRateValue, 'USD');
            }
        );

        $repositoryStorable = $this->createMock(EloquentExchangeRateRepository::class);
        $repositoryStorable->expects($this->once())->method('fetchExchangeRate')->willThrowException(
            new ExchangeRateNotAvailableException()
        );
        $repositoryStorable->expects($this->once())->method('storeExchangeRate');

        $exchangeRateReader = new ExchangeRateReader($repositoryStorable, $repositoryRemote);

        $this->expectException(ExchangeRateNotAvailableException::class);
        $exchangeRateReader->fetchExchangeRate();
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testExchangeRateReaderSuccessStorage(float $exchangeRateValue): void
    {
        $exchangeRateDateTime = null;

        $repositoryRemote = $this->createMock(ExchangeRateRepository::class);
        $repositoryRemote->expects($this->never())->method('fetchExchangeRate');

        $repositoryStorable = $this->createMock(EloquentExchangeRateRepository::class);
        $repositoryStorable->expects($this->once())->method('fetchExchangeRate')->willReturnCallback(
            function (DateTime $dateTime) use ($exchangeRateValue, &$exchangeRateDateTime) {
                $exchangeRateDateTime = (clone $dateTime)->setTime((int)$dateTime->format('H'), 0);

                return new ExchangeRate($exchangeRateDateTime, $exchangeRateValue, 'USD');
            }
        );
        $repositoryStorable->expects($this->never())->method('storeExchangeRate');

        $exchangeRateReader = new ExchangeRateReader($repositoryStorable, $repositoryRemote);

        $exchangeRate = $exchangeRateReader->fetchExchangeRate();
        $this->assertEquals($exchangeRateValue, $exchangeRate->getValue());
        $this->assertEquals($exchangeRateDateTime, $exchangeRate->getDateTime());
    }

    /**
     * @dataProvider exchangeRateProvider
     */
    public function testExchangeRateReaderStorageOldValueAndRemoteSuccess(float $exchangeRateValue): void
    {
        $exchangeRateDateTime = null;

        $repositoryRemote = $this->createMock(ExchangeRateRepository::class);
        $repositoryRemote->expects($this->once())->method('fetchExchangeRate')->willReturnCallback(
            function (DateTime $dateTime) use ($exchangeRateValue, &$exchangeRateDateTime) {
                $exchangeRateDateTime = (clone $dateTime)->setTime((int)$dateTime->format('H'), 0);

                return new ExchangeRate($exchangeRateDateTime, $exchangeRateValue, 'USD');
            }
        );

        $repositoryStorable = $this->createMock(EloquentExchangeRateRepository::class);
        $repositoryStorable->expects($this->once())->method('fetchExchangeRate')->willReturnCallback(
            function () {
                $exchangeRateValue = 1;
                $exchangeRateDateTime = (new DateTime())->modify('-1 year');

                return new ExchangeRate($exchangeRateDateTime, $exchangeRateValue, 'USD');
            }
        );
        $repositoryStorable->expects($this->once())->method('storeExchangeRate');

        $exchangeRateReader = new ExchangeRateReader($repositoryStorable, $repositoryRemote);

        $exchangeRate = $exchangeRateReader->fetchExchangeRate();
        $this->assertEquals($exchangeRateValue, $exchangeRate->getValue());
        $this->assertEquals($exchangeRateDateTime, $exchangeRate->getDateTime());
    }

    public function exchangeRateProvider(): array
    {
        return [
            [0.5],
            [1.0],
            [1.5],
        ];
    }
}
