<?php
/**
 * Copyright (c) 2018-2019 Blockchain-Ads sp. z o.o.
 *
 * This file is part of AdServer
 *
 * AdServer is free software: you can redistribute and/or modify it
 * under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * AdServer is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AdServer. If not, see <https://www.gnu.org/licenses/>
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransactionTimeToAdsPaymentsTable extends Migration
{
    public function up(): void
    {
        Schema::table(
            'ads_payments',
            static function (Blueprint $table) {
                $table->timestamp('tx_time')->nullable();

                $table->index('status', 'ads_payments_status_index');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'ads_payments',
            static function (Blueprint $table) {
                $table->dropIndex('ads_payments_status_index');

                $table->dropColumn('tx_time');
            }
        );
    }
}
