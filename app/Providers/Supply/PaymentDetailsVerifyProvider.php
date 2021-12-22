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

namespace Blockchain-Ads\Adserver\Providers\Supply;

use Blockchain-Ads\Common\Application\Service\Ads;
use Blockchain-Ads\Common\Application\Service\SignatureVerifier;
use Blockchain-Ads\Common\Infrastructure\Service\SodiumCompatSignatureVerifier;
use Blockchain-Ads\Demand\Application\Service\PaymentDetailsVerify;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class PaymentDetailsVerifyProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            SignatureVerifier::class,
            function () {
                return new SodiumCompatSignatureVerifier();
            }
        );

        $this->app->bind(
            PaymentDetailsVerify::class,
            function (Application $app) {
                return new PaymentDetailsVerify(
                    $app->make(SignatureVerifier::class),
                    $app->make(Ads::class)
                );
            }
        );
    }
}
