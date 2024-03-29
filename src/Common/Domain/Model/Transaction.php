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
use Blockchain-Ads\Common\Domain\ValueObject\TransactionId;
use Blockchain-Ads\Common\Identifiable;

final class Transaction implements Identifiable, Comparable
{
    /** @var TransactionId */
    private $id;

    public function __construct(TransactionId $id)
    {
        $this->id = $id;
    }

    public function id(): Domain\Id
    {
        return $this->id;
    }

    public function equals(object $other): bool
    {
        if ($other instanceof self) {
            return $this->id->equals($other->id);
        }

        return false;
    }
}
