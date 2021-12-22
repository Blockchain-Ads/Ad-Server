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

namespace Blockchain-Ads\Adserver\Events;

use Blockchain-Ads\Adserver\Http\Utils;
use Blockchain-Ads\Adserver\Models\Campaign;
use Blockchain-Ads\Adserver\Utilities\UuidStringGenerator;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignCreating
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(Campaign $campaign)
    {
        $campaign->uuid = UuidStringGenerator::v4();
        $campaign->secret = Utils::base64Encoded16BytesSecret();
    }
}
