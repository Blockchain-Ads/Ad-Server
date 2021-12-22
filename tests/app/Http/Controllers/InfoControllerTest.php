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

namespace Blockchain-Ads\Adserver\Tests\Http\Controllers;

use Blockchain-Ads\Adserver\Models\Config;
use Blockchain-Ads\Adserver\Tests\TestCase;
use Blockchain-Ads\Config\RegistrationMode;
use Illuminate\Http\Response;

class InfoControllerTest extends TestCase
{
    private const URI_INFO = '/info';

    public function testDefaultInfo(): void
    {
        $response = $this->getJson(self::URI_INFO);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(
            [
                'module' => 'adserver',
                'name' => 'AdServer',
                'version' => '#',
                'capabilities' => [
                    'ADV',
                    'PUB'
                ],
                'serverUrl' => 'https://test',
                'panelUrl' => 'http://adpanel',
                'privacyUrl' => 'https://test/policies/privacy.html',
                'termsUrl' => 'https://test/policies/terms.html',
                'inventoryUrl' => 'https://test/Blockchain-Ads/inventory/list',
                'adsAddress' => '0001-00000005-CBCA',
                'supportEmail' => 'mail@example.com',
                'demandFee' => 0.0199,
                'supplyFee' => 0.0199,
                'registrationMode' => 'public',
                'statistics' => [
                    'users' => 0,
                    'campaigns' => 0,
                    'sites' => 0,
                ],
            ],
            $response->json()
        );
    }

    public function testRegistrationModeInfo(): void
    {
        $response = $this->getJson(self::URI_INFO);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RegistrationMode::PUBLIC, $response->json('registrationMode'));

        Config::updateAdminSettings([Config::REGISTRATION_MODE => RegistrationMode::PRIVATE]);

        $response = $this->getJson(self::URI_INFO);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RegistrationMode::PRIVATE, $response->json('registrationMode'));
    }
}
