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

namespace Blockchain-Ads\Adserver\Console\Commands;

use Blockchain-Ads\Adserver\Console\Locker;
use Blockchain-Ads\Common\Application\Service\LicenseDecoder;
use Blockchain-Ads\Common\Application\Service\LicenseProvider;
use Blockchain-Ads\Common\Application\Service\LicenseVault;
use Blockchain-Ads\Common\Exception\RuntimeException;
use Blockchain-Ads\Supply\Application\Service\Exception\UnexpectedClientResponseException;

class FetchLicenseCommand extends BaseCommand
{
    protected $signature = 'ops:license:fetch';

    protected $description = 'Fetch operator license from License Server';
    /** @var LicenseProvider */
    private $license;
    /** @var LicenseDecoder */
    private $licenseDecoder;
    /** @var LicenseVault */
    private $licenseVault;

    public function __construct(
        Locker $locker,
        LicenseProvider $license,
        LicenseDecoder $licenseDecoder,
        LicenseVault $licenseVault
    ) {
        $this->license = $license;
        $this->licenseDecoder = $licenseDecoder;
        $this->licenseVault = $licenseVault;

        parent::__construct($locker);
    }

    public function handle(): void
    {
        if (!$this->lock()) {
            $this->info('Command ' . $this->signature . ' already running');

            return;
        }

        $this->info('Start command ' . $this->signature);

        try {
            $encodedLicense = $this->license->fetchLicense();
            $this->licenseDecoder->decode($encodedLicense->toString());
            $this->licenseVault->store($encodedLicense->toString());
        } catch (UnexpectedClientResponseException | RuntimeException $exception) {
            if (config('app.license_id') === '') {
                $this->info('No license key');
                exit(1);
            }

            $this->error($exception->getMessage());

            return;
        }

        $this->info('License has been downloaded');
    }
}
