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

namespace Blockchain-Ads\Adserver\Console\Commands;

use Blockchain-Ads\Adserver\Console\Locker;
use Blockchain-Ads\Adserver\Facades\DB;
use Blockchain-Ads\Adserver\Mail\CampaignSuspension;
use Blockchain-Ads\Adserver\Models\AdvertiserBudget;
use Blockchain-Ads\Adserver\Models\Campaign;
use Blockchain-Ads\Adserver\Models\User;
use Blockchain-Ads\Adserver\Models\UserLedgerEntry;
use Blockchain-Ads\Common\Application\Dto\ExchangeRate;
use Blockchain-Ads\Common\Infrastructure\Service\ExchangeRateReader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use InvalidArgumentException;

class DemandBlockRequiredAmount extends BaseCommand
{
    protected $signature = 'ops:demand:payments:block';

    protected $description = 'Reserves user funds for payment for campaign events';

    /** @var ExchangeRateReader */
    private $exchangeRateReader;

    public function __construct(Locker $locker, ExchangeRateReader $exchangeRateReader)
    {
        $this->exchangeRateReader = $exchangeRateReader;

        parent::__construct($locker);
    }

    public function handle(): void
    {
        if (!$this->lock()) {
            $this->info('Command ' . $this->signature . ' already running');

            return;
        }

        $this->info('Start command ' . $this->signature);

        $exchangeRate = $this->exchangeRateReader->fetchExchangeRate();

        DB::beginTransaction();

        UserLedgerEntry::pushBlockedToProcessing();

        $blockades = Campaign::fetchRequiredBudgetsPerUser();
        $this->info('Attempt to create ' . count($blockades) . ' blockades.');
        $this->blockAmountOrSuspendCampaigns($blockades, $exchangeRate);

        DB::commit();

        $this->info('Created ' . count($blockades) . ' new blocking Ledger entries.');
    }

    private function blockAmountOrSuspendCampaigns(Collection $blockade, ExchangeRate $exchangeRate): void
    {
        $blockade->each(static function (AdvertiserBudget $budget, int $userId) use ($exchangeRate) {
            try {
                UserLedgerEntry::blockAdExpense(
                    $userId,
                    $exchangeRate->toClick($budget->total()),
                    $exchangeRate->toClick($budget->bonusable())
                );
            } catch (InvalidArgumentException $e) {
                Log::warning($e->getMessage());

                if (Campaign::suspendAllForUserId($userId) > 0) {
                    Mail::to(User::fetchById($userId))->queue(new CampaignSuspension());
                }
            }
        });
    }
}
