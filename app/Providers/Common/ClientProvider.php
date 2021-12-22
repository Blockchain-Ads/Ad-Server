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

namespace Blockchain-Ads\Adserver\Providers\Common;

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Adserver\Client\ClassifierExternalClient;
use Blockchain-Ads\Adserver\Client\GuzzleAdPayClient;
use Blockchain-Ads\Adserver\Client\GuzzleAdSelectClient;
use Blockchain-Ads\Adserver\Client\GuzzleAdsOperatorClient;
use Blockchain-Ads\Adserver\Client\GuzzleAdUserClient;
use Blockchain-Ads\Adserver\Client\GuzzleClassifierExternalClient;
use Blockchain-Ads\Adserver\Client\GuzzleDemandClient;
use Blockchain-Ads\Adserver\Client\GuzzleLicenseClient;
use Blockchain-Ads\Adserver\Client\GuzzleSupplyClient;
use Blockchain-Ads\Adserver\Client\LocalPublisherBannerClassifier;
use Blockchain-Ads\Adserver\Client\MultipleExternalClassifierAdClassifyClient;
use Blockchain-Ads\Adserver\Repository\Common\ClassifierExternalRepository;
use Blockchain-Ads\Adserver\Repository\Common\EloquentExchangeRateRepository;
use Blockchain-Ads\Adserver\Services\Common\ClassifierExternalSignatureVerifier;
use Blockchain-Ads\Classify\Application\Service\ClassifierInterface;
use Blockchain-Ads\Common\Application\Service\AdClassify;
use Blockchain-Ads\Common\Application\Service\Ads;
use Blockchain-Ads\Common\Application\Service\AdUser;
use Blockchain-Ads\Common\Application\Service\ExchangeRateRepository;
use Blockchain-Ads\Common\Application\Service\LicenseProvider;
use Blockchain-Ads\Common\Application\Service\SignatureVerifier;
use Blockchain-Ads\Common\Infrastructure\Service\PhpAdsClient;
use Blockchain-Ads\Demand\Application\Service\AdPay;
use Blockchain-Ads\Supply\Application\Service\AdSelect;
use Blockchain-Ads\Supply\Application\Service\BannerClassifier;
use Blockchain-Ads\Supply\Application\Service\DemandClient;
use Blockchain-Ads\Supply\Application\Service\SupplyClient;
use GuzzleHttp\Client;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use function config;

final class ClientProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AdPay::class,
            function () {
                return new GuzzleAdPayClient(
                    new Client(
                        [
                            'headers' => ['Content-Type' => 'application/json', 'Cache-Control' => 'no-cache'],
                            'base_uri' => config('app.adpay_endpoint'),
                            'timeout' => 300,
                        ]
                    )
                );
            }
        );

        $this->app->bind(
            AdSelect::class,
            function () {
                $client = new Client(
                    [
                        'headers' => ['Content-Type' => 'application/json', 'Cache-Control' => 'no-cache'],
                        'base_uri' => config('app.adselect_endpoint'),
                        'timeout' => 5,
                    ]
                );

                return new GuzzleAdSelectClient($client);
            }
        );

        $this->app->bind(
            AdUser::class,
            function () {
                return new GuzzleAdUserClient(new Client(
                    [
                        'headers' => ['Content-Type' => 'application/json', 'Cache-Control' => 'no-cache'],
                        'base_uri' => config('app.aduser_base_url'),
                        'timeout' => 3,
                    ]
                ));
            }
        );

        $this->app->bind(
            AdClassify::class,
            function (Application $app) {
                return new MultipleExternalClassifierAdClassifyClient(
                    $app->make(ClassifierExternalClient::class),
                    new ClassifierExternalRepository()
                );
            }
        );

        $this->app->bind(
            DemandClient::class,
            function (Application $app) {
                $timeoutForDemandService = 15;

                return new GuzzleDemandClient(
                    $app->make(ClassifierExternalRepository::class),
                    $app->make(ClassifierExternalSignatureVerifier::class),
                    $app->make(SignatureVerifier::class),
                    $timeoutForDemandService
                );
            }
        );

        $this->app->bind(
            SupplyClient::class,
            function () {
                $timeoutForSupplyService = 15;

                return new GuzzleSupplyClient($timeoutForSupplyService);
            }
        );

        $this->app->bind(
            Ads::class,
            function (Application $app) {
                return new PhpAdsClient($app->make(AdsClient::class));
            }
        );

        $this->app->bind(
            BannerClassifier::class,
            function (Application $app) {
                return new LocalPublisherBannerClassifier($app->make(ClassifierInterface::class));
            }
        );

        $this->app->bind(
            LicenseProvider::class,
            function () {
                return new GuzzleLicenseClient(
                    new Client(
                        [
                            'headers' => ['Content-Type' => 'application/json', 'Cache-Control' => 'no-cache'],
                            'base_uri' => config('app.license_url'),
                            'timeout' => 5,
                        ]
                    ),
                    (string)config('app.license_id')
                );
            }
        );

        $this->app->bind(
            ExchangeRateRepository::class,
            function () {
                return new GuzzleAdsOperatorClient(
                    new Client(
                        [
                            'headers' => ['Content-Type' => 'application/json'],
                            'base_uri' => config('app.ads_operator_server_url'),
                            'timeout' => 5,
                        ]
                    )
                );
            }
        );

        $this->app->bind(
            EloquentExchangeRateRepository::class,
            function () {
                return new EloquentExchangeRateRepository();
            }
        );

        $this->app->bind(
            ClassifierExternalClient::class,
            function () {
                return new GuzzleClassifierExternalClient(
                    new Client(
                        [
                            'headers' => ['Content-Type' => 'application/json', 'Cache-Control' => 'no-cache'],
                            'timeout' => 30,
                        ]
                    )
                );
            }
        );
    }
}
