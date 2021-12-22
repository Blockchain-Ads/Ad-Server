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

use Blockchain-Ads\Adserver\Models\Config;
use Blockchain-Ads\Common\Application\Service\LicenseVault;
use Blockchain-Ads\Common\Domain\ValueObject\AccountId;
use Blockchain-Ads\Common\Exception\RuntimeException;

use function apcu_fetch;

class LicenseReader
{
    /** @var LicenseVault */
    private $licenseVault;

    public function __construct(LicenseVault $licenseVault)
    {
        $this->licenseVault = $licenseVault;
    }

    public function getAddress(): AccountId
    {
        $value = apcu_fetch(Config::LICENCE_ACCOUNT);

        if ($value) {
            return new AccountId($value);
        }

        try {
            $license = $this->licenseVault->read();
        } catch (RuntimeException $exception) {
            return new AccountId(Config::fetchStringOrFail(Config::LICENCE_ACCOUNT, true));
        }

        $value = $license->getPaymentAddress();

        apcu_store(Config::LICENCE_ACCOUNT, $value->toString());

        return $value;
    }

    public function getFee(string $type): float
    {
        if (!in_array($type, [Config::LICENCE_RX_FEE, Config::LICENCE_TX_FEE], true)) {
            throw new RuntimeException(sprintf('Unsupported fee (%s) type', $type));
        }

        $value = apcu_fetch($type);

        if ($value) {
            return $value;
        }

        try {
            $license = $this->licenseVault->read();
        } catch (RuntimeException $exception) {
            return Config::fetchFloatOrFail($type, true);
        }

        if ($type === Config::LICENCE_TX_FEE) {
            $value = $license->getDemandFee();
        } elseif ($type === Config::LICENCE_RX_FEE) {
            $value = $license->getSupplyFee();
        }

        apcu_store($type, $value);

        return $value;
    }
}
