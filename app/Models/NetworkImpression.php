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

namespace Blockchain-Ads\Adserver\Models;

use Blockchain-Ads\Adserver\Models\Traits\AutomateMutators;
use Blockchain-Ads\Adserver\Models\Traits\BinHex;
use Blockchain-Ads\Adserver\Models\Traits\JsonValue;
use Blockchain-Ads\Common\Domain\ValueObject\Uuid;
use Blockchain-Ads\Supply\Application\Dto\FoundBanners;
use Blockchain-Ads\Supply\Application\Dto\ImpressionContext;
use Blockchain-Ads\Supply\Application\Dto\UserContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use stdClass;

use function hex2bin;

/**
 * @property int id
 * @property int created_at
 * @property int updated_at
 * @property string impression_id
 * @property string tracking_id
 * @property string|null user_id
 * @property stdClass context
 * @property float|null human_score
 * @property float|null page_rank
 * @property string|null user_data
 * @property string|null country
 * @property Collection networkCases
 * @mixin Builder
 */
class NetworkImpression extends Model
{
    use AutomateMutators;
    use BinHex;
    use JsonValue;

    /** @var array */
    protected $visible = [];

    /**
     * The attributes that use some Models\Traits with mutator settings automation
     *
     * @var array
     */
    protected $traitAutomate = [
        'impression_id' => 'BinHex',
        'tracking_id' => 'BinHex',
        'user_id' => 'BinHex',
        'context' => 'JsonValue',
        'user_data' => 'JsonValue',
    ];

    public static function register(
        string $impressionId,
        string $trackingId,
        ImpressionContext $impressionContext,
        UserContext $userContext,
        FoundBanners $foundBanners,
        array $zones
    ): void {
        if (self::where('impression_id', hex2bin($impressionId))->first()) {
            return;
        }

        $log = new self();
        $log->impression_id = $impressionId;
        $log->tracking_id = $trackingId;

        $context = $impressionContext->toArray();
//        $context['banners'] = $foundBanners->toArray();
//        $context['zones'] = $zones;
        $log->context = $context;

        $log->setFieldsDependentOnUserContext($userContext);
        $log->save();
    }

    public static function fetchByImpressionId(string $impressionId): ?NetworkImpression
    {
        return self::where('impression_id', hex2bin($impressionId))->first();
    }

    public function updateWithUserContext(UserContext $userContext): void
    {
        $this->setFieldsDependentOnUserContext($userContext);
        $this->save();
    }

    private function setFieldsDependentOnUserContext(UserContext $userContext): void
    {
        $userId = $userContext->userId();
        if ($userId) {
            $this->user_id = Uuid::fromString($userId)->hex();
        }
        $this->human_score = $userContext->humanScore();
        $this->page_rank = $userContext->pageRank();
        $this->user_data = $userContext->keywords();
        $this->country = $userContext->country();
    }

    public function networkCases(): HasMany
    {
        return $this->hasMany(NetworkCase::class);
    }
}
