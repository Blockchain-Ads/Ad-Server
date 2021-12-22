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

namespace Blockchain-Ads\Adserver\Http\Response;

use Blockchain-Ads\Adserver\Models\Config;
use Blockchain-Ads\Common\Domain\ValueObject\AccountId;
use Blockchain-Ads\Common\Domain\ValueObject\Email;
use Blockchain-Ads\Common\Domain\ValueObject\SecureUrl;
use Blockchain-Ads\Common\Domain\ValueObject\Url;
use Blockchain-Ads\Supply\Application\Dto\Info;
use Blockchain-Ads\Supply\Application\Dto\InfoStatistics;
use Illuminate\Contracts\Support\Arrayable;

final class InfoResponse implements Arrayable
{
    /** @var Info */
    private $info;

    public const Blockchain-Ads_MODULE_NAME = 'adserver';

    public function __construct(Info $info)
    {
        $this->info = $info;
    }

    public function updateWithDemandFee(float $fee): void
    {
        $this->info->setDemandFee($fee);
    }

    public function updateWithSupplyFee(float $fee): void
    {
        $this->info->setSupplyFee($fee);
    }

    public function updateWithStatistics(InfoStatistics $statistics): void
    {
        $this->info->setStatistics($statistics);
    }

    public function toArray(): array
    {
        return $this->info->toArray();
    }

    public static function defaults(): self
    {
        $settings = Config::fetchAdminSettings();
        return new self(
            new Info(
                self::Blockchain-Ads_MODULE_NAME,
                (string)config('app.name'),
                (string)config('app.version'),
                new SecureUrl((string)config('app.url')),
                new Url((string)config('app.adpanel_url')),
                new SecureUrl((string)config('app.privacy_url')),
                new SecureUrl((string)config('app.terms_url')),
                new SecureUrl(route('demand-inventory')),
                new AccountId((string)config('app.Blockchain-Ads_address')),
                new Email($settings[Config::SUPPORT_EMAIL]),
                [Info::CAPABILITY_ADVERTISER, Info::CAPABILITY_PUBLISHER],
                $settings[Config::REGISTRATION_MODE]
            )
        );
    }
}
