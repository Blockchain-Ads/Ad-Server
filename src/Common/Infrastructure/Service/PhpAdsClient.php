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

namespace Blockchain-Ads\Common\Infrastructure\Service;

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Ads\Command\SendManyCommand;
use Blockchain-Ads\Ads\Entity\Tx;
use Blockchain-Ads\Ads\Exception\CommandException;
use Blockchain-Ads\Adserver\Models\Payment;
use Blockchain-Ads\Common\Application\Service\Ads;
use Blockchain-Ads\Common\Application\Service\Exception\AdsException;
use Illuminate\Support\Collection;

class PhpAdsClient implements Ads
{
    private $adsClient;

    public function __construct(AdsClient $adsClient)
    {
        $this->adsClient = $adsClient;
    }

    public function getPublicKeyByAccountAddress(string $accountAddress): string
    {
        try {
            $response = $this->adsClient->getAccount($accountAddress);
        } catch (CommandException $exception) {
            throw new AdsException(sprintf('Account %s cannot be fetched.', $accountAddress));
        }

        return $response->getAccount()->getPublicKey();
    }

    public function sendPayments(Collection $payments): Tx
    {
        $wires = $payments->groupBy('account_address')
            ->map(function (Collection $payments) {
                return $payments->sum(function (Payment $payment) {
                    return $payment->transferableAmount();
                });
            });

        $command = new SendManyCommand($wires->toArray());

        try {
            $response = $this->adsClient->runTransaction($command);
        } catch (CommandException $exception) {
            throw new AdsException($exception->getMessage(), $exception->getCode());
        }

        return $response->getTx();
    }
}
