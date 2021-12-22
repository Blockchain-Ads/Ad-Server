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

namespace Blockchain-Ads\Classify\Application\Service;

use Blockchain-Ads\Classify\Application\Exception\BannerNotVerifiedException;
use Blockchain-Ads\Classify\Domain\Model\ClassificationCollection;

interface ClassifierInterface
{
    public const KEYWORD_ACCEPTED = true;
    public const KEYWORD_DECLINED = false;

    /**
     * @param int $bannerId
     *
     * @throws BannerNotVerifiedException
     *
     * @return ClassificationCollection
     */
    public function fetch(int $bannerId): ClassificationCollection;
}
