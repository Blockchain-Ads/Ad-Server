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

namespace Blockchain-Ads\Mock\Client;

use Blockchain-Ads\Common\Application\Dto\PageRank;
use Blockchain-Ads\Common\Application\Dto\Taxonomy;
use Blockchain-Ads\Common\Application\Factory\TaxonomyFactory;
use Blockchain-Ads\Common\Application\Service\AdUser;
use Blockchain-Ads\Supply\Application\Dto\ImpressionContext;
use Blockchain-Ads\Supply\Application\Dto\UserContext;
use RuntimeException;

use function base_path;
use function file_get_contents;
use function GuzzleHttp\json_decode;

final class DummyAdUserClient implements AdUser
{
    public function fetchPageRank(string $url): PageRank
    {
        return new PageRank(1, AdUser::PAGE_INFO_OK);
    }

    public function fetchPageRankBatch(array $urls): array
    {
        $result = [];

        foreach ($urls as $id => $url) {
            $result[$id] = [
                'rank' => 1,
                'info' => AdUser::PAGE_INFO_OK,
            ];
        }

        return $result;
    }

    public function fetchTargetingOptions(): Taxonomy
    {
        $path = base_path('tests/mock/targeting_schema.json');
        $var = file_get_contents($path);
        $taxonomy = json_decode($var, true);

        return TaxonomyFactory::fromArray($taxonomy);
    }

    public function getUserContext(ImpressionContext $context): UserContext
    {
        throw new RuntimeException('Method getUserContext() not implemented');
    }

    public function reassessPageRankBatch(array $urls): array
    {
        $result = [];

        foreach ($urls as $id => $urlData) {
            $result[$id] = [
                'status' => AdUser::REASSESSMENT_STATE_ACCEPTED,
            ];
        }

        return $result;
    }
}
