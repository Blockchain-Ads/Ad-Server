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

namespace Blockchain-Ads\Adserver\Http\Middleware;

use Blockchain-Ads\Adserver\Models\Token;
use Blockchain-Ads\Adserver\Models\User;
use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Impersonation
{
    private const HEADER_NAME = 'x-Blockchain-Ads-impersonation';

    public function handle(Request $request, Closure $next)
    {
        $header = $request->header(self::HEADER_NAME);

        if ($header && $header !== 'null' && Auth::user()->isAdmin()) {
            if (false !== ($token = Token::check($header))) {
                $userId = (int)$token['payload'];

                /** @var User|Authenticatable $user */
                $user = User::where('id', $userId)
                    ->where('is_admin', 0)
                    ->first();

                if ($user) {
                    Auth::setUser($user);
                }
            }
        }

        return $next($request);
    }
}
