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

namespace Blockchain-Ads\Common\Domain\Model;

use Blockchain-Ads\Common\Comparable;
use Blockchain-Ads\Common\Domain;
use Blockchain-Ads\Common\Domain\ValueObject\AccountId;
use Blockchain-Ads\Common\Identifiable;

final class Account implements Identifiable, Comparable
{
    /** @var AccountId */
    private $id;

    public function __construct(AccountId $id)
    {
        $this->id = $id;
    }

    public function id(): Domain\Id
    {
        return $this->id;
    }

    public function equals(object $other): bool
    {
        if (!($other instanceof self)) {
            return false;
        }

        return $this->id->equals($other);
    }
}
