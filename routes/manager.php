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


use Blockchain-Ads\Adserver\Http\Controllers\Manager\BidStrategyController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\CampaignsController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\ClassifierController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\ConfigController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\InvoicesController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\NotificationsController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\OptionsController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\RefLinksController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\SettingsController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\SitesController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\StatsController;
use Blockchain-Ads\Adserver\Http\Controllers\Manager\WalletController;
use Blockchain-Ads\Adserver\Http\Kernel;
use Illuminate\Support\Facades\Route;

Route::middleware([Kernel::JSON_API])->group(
    function () {
        Route::get('ref-links/info/{token}', [RefLinksController::class, 'info']);
    }
);

Route::middleware([Kernel::USER_ACCESS, Kernel::JSON_API])->group(
    function () {
        Route::get(
            'campaigns/bid-strategy/{bid_strategy_public_id}/spreadsheet',
            [BidStrategyController::class, 'getBidStrategySpreadsheet']
        );
        Route::post(
            'campaigns/bid-strategy/{bid_strategy_public_id}/spreadsheet',
            [BidStrategyController::class, 'putBidStrategySpreadsheet']
        );
        Route::get('campaigns/bid-strategy/uuid-default', [BidStrategyController::class, 'getBidStrategyUuidDefault']);
        Route::get('campaigns/bid-strategy', [BidStrategyController::class, 'getBidStrategy']);
        Route::put('campaigns/bid-strategy', [BidStrategyController::class, 'putBidStrategy']);
        Route::patch(
            'campaigns/bid-strategy/{bid_strategy_public_id}',
            [BidStrategyController::class, 'patchBidStrategy']
        );
        Route::delete(
            'campaigns/bid-strategy/{bid_strategy_public_id}',
            [BidStrategyController::class, 'deleteBidStrategy']
        );

        Route::get('campaigns', [CampaignsController::class, 'browse'])
            ->name('app.campaigns.browse');
        Route::get('campaigns/{campaign_id}', [CampaignsController::class, 'read'])
            ->name('app.campaigns.read');
        Route::post('campaigns', [CampaignsController::class, 'add'])
            ->name('app.campaigns.add');
        Route::patch('campaigns/{campaign_id}', [CampaignsController::class, 'edit'])
            ->name('app.campaigns.edit');
        Route::put('campaigns/{campaign}/status', [CampaignsController::class, 'changeStatus'])
            ->name('app.campaigns.change_status');
        Route::patch('campaigns/{campaign}/activate-outdated', [CampaignsController::class, 'activateOutdatedCampaign'])
            ->name('app.campaigns.activate_outdated');
        Route::put(
            'campaigns/{campaign_id}/banner/{banner_id}/status',
            [CampaignsController::class, 'changeBannerStatus']
        )
            ->name('app.campaigns.change_banner_status');
        Route::delete('campaigns/{campaign_id}', [CampaignsController::class, 'delete'])
            ->name('app.campaigns.delete');
        Route::post('campaigns/banner', [CampaignsController::class, 'upload'])
            ->name('app.campaigns.upload');
        Route::post('campaigns/{campaign_id}/clone', [CampaignsController::class, 'clone'])
            ->name('app.campaigns.clone');

        Route::post('campaigns/{campaign_id}/classify', [CampaignsController::class, 'classify'])
            ->name('app.campaigns.classify');
        Route::delete('campaigns/{campaign_id}/classify', [CampaignsController::class, 'disableClassify'])
            ->name('app.campaigns.disable_classify');

        Route::post('sites/domain/validate', [SitesController::class, 'verifyDomain']);
        Route::post('sites', [SitesController::class, 'create'])
            ->name('app.sites.add');
        Route::get('sites/sizes/{site_id?}', [SitesController::class, 'readSitesSizes'])
            ->name('app.sites.sizes');
        Route::get('sites/{site}/rank', [SitesController::class, 'readSiteRank']);
        Route::get('sites/{site}', [SitesController::class, 'read'])
            ->name('app.sites.read');
        Route::patch('sites/{site}', [SitesController::class, 'update'])
            ->name('app.sites.edit');
        Route::delete('sites/{site}', [SitesController::class, 'delete'])
            ->name('app.sites.delete');
        Route::get('sites', [SitesController::class, 'list'])
            ->name('app.sites.browse');
        Route::put('sites/{site}/status', [SitesController::class, 'changeStatus'])
            ->name('app.sites.change_status');
        Route::get('sites/{site}/codes', [SitesController::class, 'sitesCodes'])
            ->name('app.sites.code');

# actions
        Route::get('config/Blockchain-Ads-address', [ConfigController::class, 'Blockchain-AdsAddress']);
        Route::get('countries', [ConfigController::class, 'countries']);
        Route::get('notifications', [NotificationsController::class, 'read']);
        Route::get('settings/notifications', [SettingsController::class, 'readNotifications']);

        Route::get('ref-links', [RefLinksController::class, 'browse']);
        Route::post('ref-links', [RefLinksController::class, 'add']);
        Route::get('invoices', [InvoicesController::class, 'browse']);
        Route::post('invoices', [InvoicesController::class, 'add']);

        Route::get('options/campaigns', [OptionsController::class, 'campaigns']);
        Route::get('options/campaigns/targeting', [OptionsController::class, 'targeting']);
        Route::post('options/campaigns/targeting-reach', [OptionsController::class, 'targetingReach']);
        Route::get('options/sites/filtering', [OptionsController::class, 'filtering']);
        Route::get('options/sites/languages', [OptionsController::class, 'languages']);
        Route::get('options/sites/zones', [OptionsController::class, 'zones']);

// Routes for Withdraw/Deposit
        Route::get('withdrawal-info', [WalletController::class, 'withdrawalInfo']);
        Route::post('calculate-withdrawal', [WalletController::class, 'calculateWithdrawal']);
        Route::post('wallet/withdraw', [WalletController::class, 'withdraw']);
        Route::get('deposit-info', [WalletController::class, 'depositInfo']);
        Route::get('wallet/history', [WalletController::class, 'history']);
        Route::post('wallet/confirm-withdrawal', [WalletController::class, 'confirmWithdrawal'])
            ->name('wallet.confirm-withdrawal');
        Route::delete('wallet/cancel-withdrawal/{entry}', [WalletController::class, 'cancelWithdrawal'])
            ->name('wallet.cancel-withdrawal');
        Route::get('now-payments/init', [WalletController::class, 'nowPaymentsInit']);

// statistics
        Route::get(
            'campaigns/stats/chart/{type}/{resolution}/{date_start}/{date_end}',
            [StatsController::class, 'advertiserChart']
        );
        Route::get(
            'campaigns/stats/table2/{date_start}/{date_end}',
            [StatsController::class, 'advertiserStatsWithTotal']
        );
        Route::get(
            'campaigns/stats/kw/{date_start}/{date_end}',
            [StatsController::class, 'advertiserStatsConversions']
        );
        Route::get(
            'sites/stats/chart/{type}/{resolution}/{date_start}/{date_end}',
            [StatsController::class, 'publisherChart']
        );
        Route::get('sites/stats/table2/{date_start}/{date_end}', [StatsController::class, 'publisherStatsWithTotal']);

        Route::get('stats/report/sites/{date_start}/{date_end}', [StatsController::class, 'publisherReportFileCreate']);
        Route::get(
            'stats/report/campaigns/{date_start}/{date_end}',
            [StatsController::class, 'advertiserReportFileCreate']
        );
        Route::get('stats/report/list', [StatsController::class, 'reportList']);
        Route::get('stats/report/{uuid}', [StatsController::class, 'reportDownload']);

        Route::get('sites/stats/report/{date_start}/{date_end}', [StatsController::class, 'publisherReport']);
        Route::get('campaigns/stats/report/{date_start}/{date_end}', [StatsController::class, 'advertiserReport']);

        Route::get('classifications/{site_id?}', [ClassifierController::class, 'fetch']);
        Route::patch('classifications/{site_id?}', [ClassifierController::class, 'add']);
    }
);
