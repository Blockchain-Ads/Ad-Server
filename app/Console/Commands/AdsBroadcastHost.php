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

use Blockchain-Ads\Ads\AdsClient;
use Blockchain-Ads\Adserver\Console\Locker;
use Blockchain-Ads\Common\Domain\ValueObject\SecureUrl;
use Blockchain-Ads\Common\UrlInterface;
use Blockchain-Ads\Network\Broadcast;
use Blockchain-Ads\Network\BroadcastableUrl;

use function route;

class AdsBroadcastHost extends BaseCommand
{
    /**
     * @var string
     */
    protected $signature = 'ads:broadcast-host';

    /**
     * @var string
     */
    protected $description = 'Sends AdServer host address as broadcast message to blockchain';

    /**
     * @var UrlInterface
     */
    private $infoApiUrl;

    public function __construct(Locker $locker)
    {
        parent::__construct($locker);

        $this->infoApiUrl = new SecureUrl(route('app.infoEndpoint'));
    }

    public function handle(AdsClient $adsClient): void
    {
        if (!$this->lock()) {
            $this->info('Command ' . $this->signature . ' already running');

            return;
        }

        $this->info('Start command ' . $this->signature);

        $url = new BroadcastableUrl($this->infoApiUrl);
        $command = new Broadcast($url);

        $response = $adsClient->runTransaction($command);

        $txId = $response->getTx()->getId();

        $this->info("Url ($url) broadcast successfully. TxId: $txId");
    }
}
