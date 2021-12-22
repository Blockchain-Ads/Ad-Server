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
use Blockchain-Ads\Adserver\Utilities\InvoiceUtils;
use Illuminate\Http\JsonResponse;
use Symfony\Component\Intl\Countries;

class ConfigController extends Controller
{

    public function Blockchain-AdsAddress(): JsonResponse
    {
        return self::json(['Blockchain-AdsAddress' => config('app.Blockchain-Ads_address')], 200);
    }

    public function countries(): JsonResponse
    {
        $countries = [];
        foreach (Countries::getNames() as $code => $name) {
            $countries[$code] = [
                'code' => $code,
                'name' => $name,
                'eu_tax' => false,
            ];
            if ('MK' === $code) {
                $countries['XI'] = [
                    'code' => 'XI',
                    'name' => 'Northern Ireland',
                    'eu_tax' => false,
                ];
            }
        }
        foreach (InvoiceUtils::EU_COUNTRIES as $code) {
            $countries[$code]['eu_tax'] = true;
        }
        return self::json(array_values($countries));
    }
}
