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

namespace Blockchain-Ads\Tests\Supply\Application\Dto\Classification;

use Blockchain-Ads\Common\Domain\ValueObject\Uuid;
use Blockchain-Ads\Supply\Application\Dto\Classification\Collection;
use PHPUnit\Framework\TestCase;

final class CollectionTest extends TestCase
{
    public function testAddingAnEmptyItem(): void
    {
        $collection = new Collection();
        $bannerId = (string)Uuid::v4();

        $collection->addEmptyClassification($bannerId);

        $this->assertCount(0, $collection->findByBannerId($bannerId));
    }

    public function testWhenAddingMultipleClassificationsForBanner(): void
    {
        $collection = new Collection();
        $banner1Id = (string)Uuid::v4();
        $banner2Id = (string)Uuid::v4();

        $collection->addClassification($banner1Id, 'classify', ['1:1']);
        $collection->addClassification($banner1Id, 'classify', ['1:1:2']);
        $collection->addClassification($banner1Id, 'classify', ['2:2:1']);
        $collection->addClassification($banner1Id, 'classify', ['2:1']);

        $collection->addClassification($banner2Id, 'classify', ['4:1:1']);
        $collection->addClassification($banner2Id, 'classify', ['4:1:1']);

        $this->assertCount(4, $collection->findByBannerId($banner1Id));
        $this->assertCount(2, $collection->findByBannerId($banner2Id));
    }
}
