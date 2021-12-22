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

namespace Blockchain-Ads\Adserver\Providers;

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Ads\Driver\CliDriver;
use Blockchain-Ads\Adserver\Facades\DB;
use Blockchain-Ads\Adserver\Repository\Advertiser\MySqlStatsRepository as MysqlAdvertiserStatsRepository;
use Blockchain-Ads\Adserver\Repository\Common\EloquentExchangeRateRepository;
use Blockchain-Ads\Adserver\Repository\Publisher\MySqlStatsRepository as MysqlPublisherStatsRepository;
use Blockchain-Ads\Adserver\Services\AdsExchange;
use Blockchain-Ads\Adserver\Services\Common\AdsLogReader;
use Blockchain-Ads\Adserver\Services\NowPayments;
use Blockchain-Ads\Advertiser\Repository\StatsRepository as AdvertiserStatsRepository;
use Blockchain-Ads\Common\Application\Service\ExchangeRateRepository;
use Blockchain-Ads\Common\Application\Service\LicenseDecoder;
use Blockchain-Ads\Common\Application\Service\LicenseVault;
use Blockchain-Ads\Common\Infrastructure\Service\ExchangeRateReader;
use Blockchain-Ads\Common\Infrastructure\Service\LicenseDecoderV1;
use Blockchain-Ads\Common\Infrastructure\Service\LicenseReader;
use Blockchain-Ads\Common\Infrastructure\Service\LicenseVaultFilesystem;
use Blockchain-Ads\Demand\Application\Service\TransferMoneyToColdWallet;
use Blockchain-Ads\Demand\Application\Service\WalletFundsChecker;
use Blockchain-Ads\Publisher\Repository\StatsRepository as PublisherStatsRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            AdsClient::class,
            function () {
                $drv = new CliDriver(
                    config('app.Blockchain-Ads_address'),
                    config('app.Blockchain-Ads_secret'),
                    config('app.Blockchain-Ads_node_host'),
                    config('app.Blockchain-Ads_node_port')
                );
                $drv->setCommand(config('app.Blockchain-Ads_command'));
                $drv->setWorkingDir(config('app.Blockchain-Ads_workingdir'));

                return new AdsClient($drv);
            }
        );

        $this->app->bind(
            AdsLogReader::class,
            function (Application $app) {
                return new AdsLogReader($app->make(AdsClient::class));
            }
        );

        $this->app->bind(
            AdvertiserStatsRepository::class,
            function () {
                return new MysqlAdvertiserStatsRepository();
            }
        );

        $this->app->bind(
            PublisherStatsRepository::class,
            function () {
                return new MysqlPublisherStatsRepository();
            }
        );

        $this->app->bind(
            TransferMoneyToColdWallet::class,
            function (Application $app) {
                $coldWalletAddress = (string)config('app.Blockchain-Ads_wallet_cold_address');
                $minAmount = (int)config('app.Blockchain-Ads_wallet_min_amount');
                $maxAmount = (int)config('app.Blockchain-Ads_wallet_max_amount');
                $adsClient = $app->make(AdsClient::class);

                return new TransferMoneyToColdWallet($minAmount, $maxAmount, $coldWalletAddress, $adsClient);
            }
        );

        $this->app->bind(
            WalletFundsChecker::class,
            function (Application $app) {
                $minAmount = (int)config('app.Blockchain-Ads_wallet_min_amount');
                $maxAmount = (int)config('app.Blockchain-Ads_wallet_max_amount');
                $adsClient = $app->make(AdsClient::class);

                return new WalletFundsChecker($minAmount, $maxAmount, $adsClient);
            }
        );

        $this->app->bind(
            LicenseDecoder::class,
            function () {
                return new LicenseDecoderV1((string)config('app.license_key'));
            }
        );

        $this->app->bind(
            LicenseVault::class,
            function (Application $app) {
                $path = Storage::disk('local')->path('license.txt');

                return new LicenseVaultFilesystem($path, $app->make(LicenseDecoder::class));
            }
        );

        $this->app->bind(
            LicenseReader::class,
            function (Application $app) {
                return new LicenseReader($app->make(LicenseVault::class));
            }
        );

        $this->app->bind(
            ExchangeRateReader::class,
            function (Application $app) {
                return new ExchangeRateReader(
                    $app->make(EloquentExchangeRateRepository::class),
                    $app->make(ExchangeRateRepository::class)
                );
            }
        );

        $this->app->bind(
            AdsExchange::class,
            function (Application $app) {
                return new AdsExchange();
            }
        );

        $this->app->bind(
            NowPayments::class,
            function (Application $app) {
                return new NowPayments(
                    $app->make(ExchangeRateReader::class),
                    $app->make(AdsExchange::class)
                );
            }
        );
    }

    public function boot()
    {
        if (config('app.debug')) {
            DB::listen(
                function ($query) {
                    Log::debug($query->sql);
                }
            );
        }
        Blade::directive('date', function ($date) {
            return "<?php echo (new \DateTime($date))->format('d-m-Y'); ?>";
        });
        Blade::directive('money', function ($money) {
            return "<?php echo number_format($money, 2, ',', ' '); ?>";
        });
        Blade::directive('spellout', function ($number) {
            return "<?php echo (new \NumberFormatter('en', \NumberFormatter::SPELLOUT))->format($number); ?>";
        });
    }
}
