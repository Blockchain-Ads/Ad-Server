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

namespace Blockchain-Ads\Adserver\Tests\Http;

use Blockchain-Ads\Adserver\Models\User;
use Blockchain-Ads\Adserver\Tests\TestCase;
use Blockchain-Ads\Common\Application\Service\AdClassify;
use Blockchain-Ads\Common\Application\Service\AdUser;
use Blockchain-Ads\Mock\Client\DummyAdClassifyClient;
use Blockchain-Ads\Mock\Client\DummyAdUserClient;

class OptionsTest extends TestCase
{
    public function testTargeting(): void
    {
        $this->actingAs(factory(User::class)->create(), 'api');

        $response = $this->getJson('/api/options/campaigns/targeting');
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    '*' => [
                        'key',
                        'label',
                    ],
                ]
            );

        $content = json_decode($response->content(), true);
        $this->assertStructure($content);
    }

    private function assertStructure(array $content): void
    {
        foreach ($content as $item) {
            if ($item['children'] ?? false) {
                self::assertNotEmpty($item['children']);
                self::assertFalse($item['values'] ?? false);
                self::assertFalse($item['allowInput'] ?? false);
            } else {
                self::assertIsArray($item['values']);
                self::assertIsBool($item['allowInput']);
            }
            self::assertIsString($item['valueType']);
            self::assertIsString($item['key']);
            self::assertIsString($item['label']);
        }
    }

    public function testFiltering(): void
    {
        $this->actingAs(factory(User::class)->create(), 'api');

        $response = $this->getJson('/api/options/sites/filtering');
        $response->assertStatus(200)
            ->assertJsonStructure(
                [
                    '*' => [
                        'key',
                        'label',
                    ],
                ]
            );

        $content = json_decode($response->content(), true);
        $this->assertStructure($content);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(
            AdUser::class,
            static function () {
                return new DummyAdUserClient();
            }
        );

        $this->app->bind(
            AdClassify::class,
            static function () {
                return new DummyAdClassifyClient();
            }
        );
    }
}
