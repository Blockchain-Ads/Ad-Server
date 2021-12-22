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

namespace Blockchain-Ads\Adserver\Services;

use Blockchain-Ads\Adserver\Http\Utils;
use Blockchain-Ads\Adserver\Models\EventLog;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EventCaseFinder
{
    public function findByTrackingId(string $campaignPublicId, string $trackingId): array
    {
        $event = EventLog::fetchLastByTrackingId($campaignPublicId, $trackingId);

        if (null === $event) {
            return [];
        }

        $caseId = $event->case_id;

        return [
            $caseId => 1,
        ];
    }

    public function findByCaseId(string $campaignPublicId, string $caseId): array
    {
        $eventId = Utils::createCaseIdContainingEventType($caseId, EventLog::TYPE_VIEW);

        try {
            $event = EventLog::fetchOneByEventId($eventId);
        } catch (ModelNotFoundException $exception) {
            return [];
        }

        if ($campaignPublicId !== $event->campaign_id) {
            return [];
        }

        return [
            $caseId => 1,
        ];
    }
}
