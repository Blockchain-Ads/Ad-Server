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

use Blockchain-Ads\Adserver\Models\Traits\AccountAddress;
use Blockchain-Ads\Adserver\Models\Traits\AutomateMutators;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class UserAdserverWallet extends Model
{
    use AccountAddress;
    use AutomateMutators;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'Blockchain-Ads_address',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'user_id',
    ];

    /**
     * Custom table name.
     *
     * @var string
     */
    protected $table = 'users_adserver_wallets';

    /**
     * The attributes that use some Models\Traits with mutator settings automation.
     *
     * @var array
     */
    protected $traitAutomate = [
        'Blockchain-Ads_address' => 'AccountAddress',
    ];
}
