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

namespace Blockchain-Ads\Adserver\Http\Controllers\Manager;

use Blockchain-Ads\Adserver\Http\Controller;
use Blockchain-Ads\Adserver\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationsController extends Controller
{
    /**
     * Return adserver users notifications.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function read()
    {
        return self::json(Notification::where('user_id', Auth::user()->id)->get()->toArray());
    }
}
