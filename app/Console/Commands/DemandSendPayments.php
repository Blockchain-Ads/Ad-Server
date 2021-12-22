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

use Blockchain-Ads\Adserver\Models\Payment;
use Blockchain-Ads\Common\Application\Service\Ads;
use Blockchain-Ads\Common\Application\Service\Exception\AdsException;
use Illuminate\Database\Eloquent\Collection;

class DemandSendPayments extends BaseCommand
{
    public const COMMAND_SIGNATURE = 'ops:demand:payments:send';

    public const STATUS_OK = 0;

    public const STATUS_LOCKED = 1;

    public const STATUS_ERROR_ADS = 2;

    protected $signature = self::COMMAND_SIGNATURE;

    protected $description = 'Sends payments to supply adservers and license server';

    public function handle(Ads $ads): int
    {
        if (!$this->lock()) {
            $this->info('Command ' . self::COMMAND_SIGNATURE . ' already running');

            return self::STATUS_LOCKED;
        }

        $this->info('Start command ' . self::COMMAND_SIGNATURE);

        $allPayments = Payment::fetchByStatus(Payment::STATE_NEW, false);
        /** @var $zeroPayments Collection */
        /** @var $payments Collection */
        [$zeroPayments, $payments] = $allPayments->partition(function (Payment $payment) {
            return $payment->fee === 0;
        });
        $zeroPayments->each(function (Payment $payment) {
            $payment->state = Payment::STATE_SENT;
            $payment->save();
        });

        $paymentCount = count($payments);
        $this->info("Found $paymentCount sendable payments.");

        if (!$paymentCount) {
            $this->release();

            return self::STATUS_OK;
        }

        $this->info("Sending $paymentCount payments from " . config('app.Blockchain-Ads_address') . '.');

        try {
            $tx = $ads->sendPayments($payments);
        } catch (AdsException $exception) {
            $this->error(
                sprintf(
                    '[DemandSendPayments] AdsException (%d) (%s)',
                    $exception->getCode(),
                    $exception->getMessage()
                )
            );
            $this->release();

            return self::STATUS_ERROR_ADS;
        }

        $payments->each(function (Payment $payment) use ($tx) {
            $payment->tx_id = $tx->getId();
            $payment->tx_time = $tx->getTime()->getTimestamp();
            $payment->tx_data = $tx->getData();

            $payment->state = Payment::STATE_SENT;

            $payment->account_hashin = $tx->getAccountHashin();
            $payment->account_hashout = $tx->getAccountHashout();
            $payment->account_msid = $tx->getAccountMsid();

            $payment->save();

            $this->info("#{$payment->id}: {$payment->transferableAmount()} clicks to {$payment->account_address};");
        });

        $this->info("Spent {$tx->getDeduct()} clicks, including a {$tx->getFee()} clicks network fee.");
        $this->info("TransactionId: {$tx->getId()}");
        $this->release();

        return self::STATUS_OK;
    }
}
