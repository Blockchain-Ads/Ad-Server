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

namespace Blockchain-Ads\Adserver\Services\Common;

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Ads\Exception\CommandException;
use Blockchain-Ads\Ads\Util\AdsConverter;
use Blockchain-Ads\Adserver\Models\AdsPayment;
use Blockchain-Ads\Adserver\Models\Config;
use Blockchain-Ads\Adserver\Utilities\SqlUtils;
use DateTime;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AdsLogReader
{
    /** @var AdsClient */
    private $adsClient;

    public function __construct(AdsClient $adsClient)
    {
        $this->adsClient = $adsClient;
    }

    /**
     * @throws CommandException
     */
    public function parseLog(): int
    {
        $log = $this->adsClient->getLog(Config::fetchDateTime(Config::ADS_LOG_START))->getLog();

        if (!$log) {
            return 0;
        }

        $transactionCount = $this->extractAdsPayments($log);
        $this->extractAndStoreLastEventTime($log);

        return $transactionCount;
    }

    private function extractAdsPayments(array $log): int
    {
        $count = 0;
        foreach ($log as $logEntry) {
            $type = $logEntry['type'];
            if (($type === 'send_many' || $type === 'send_one') && $logEntry['inout'] === 'in') {
                $transactionId = $logEntry['id'];
                $amountInClicks = AdsConverter::adsToClicks($logEntry['amount']);
                $address = $logEntry['address'];

                $adsPayment = AdsPayment::create($transactionId, $amountInClicks, $address);

                try {
                    $adsPayment->save();
                    ++$count;
                } catch (QueryException $queryException) {
                    if (SqlUtils::isDuplicatedEntry($queryException)) {
                        Log::info(sprintf('Transaction [%s] rejected. It is already in the database.', $transactionId));
                    } else {
                        Log::error(
                            sprintf(
                                'Transaction [%s] rejected due to (%s)',
                                $transactionId,
                                $queryException->getMessage()
                            )
                        );
                    }
                }
            }
        }

        return $count;
    }

    private function extractAndStoreLastEventTime(array $log): void
    {
        $count = count($log);
        $lastEventIndex = $count - 1;
        if ($count > 0) {
            $timestamp = $log[$lastEventIndex]['time'];

            Config::upsertDateTime(Config::ADS_LOG_START, new DateTime('@' . $timestamp));
        }
    }
}
