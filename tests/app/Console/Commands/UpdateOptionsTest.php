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

namespace Blockchain-Ads\Adserver\Tests\Console\Commands;

use Blockchain-Ads\Adserver\Client\DummyAdClassifyClient;
use Blockchain-Ads\Adserver\Tests\Console\ConsoleTestCase;
use Blockchain-Ads\Common\Application\Service\AdClassify;
use Blockchain-Ads\Common\Application\Service\AdUser;
use Blockchain-Ads\Mock\Client\DummyAdUserClient;

class UpdateOptionsTest extends ConsoleTestCase
{
    public function testTargetingOptionsUpdate(): void
    {
        $this->app->bind(
            AdUser::class,
            function () {
                return new DummyAdUserClient();
            }
        );

        $this->artisan('ops:targeting-options:update')
            ->assertExitCode(0);
    }

    public function testFilteringOptionsUpdate(): void
    {
        $this->app->bind(
            AdClassify::class,
            function () {
                return new DummyAdClassifyClient();
            }
        );

        $this->artisan('ops:filtering-options:update')
            ->assertExitCode(0);
    }
}
