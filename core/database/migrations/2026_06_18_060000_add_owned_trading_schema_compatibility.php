<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trading_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('trading_logs', 'price')) {
                $table->string('price')->default('0')->after('amount_converted');
            }

            if (!Schema::hasColumn('trading_logs', 'finalz')) {
                $table->string('finalz')->default('0')->after('sl');
            }

            if (!Schema::hasColumn('trading_logs', 'coinz')) {
                $table->string('coinz')->default('0')->after('finalz');
            }

            if (!Schema::hasColumn('trading_logs', 'profit')) {
                $table->string('profit')->default('0')->after('coinz');
            }
        });

        Schema::table('demo_trading_logs', function (Blueprint $table) {
            if (!Schema::hasColumn('demo_trading_logs', 'finalz')) {
                $table->string('finalz')->default('0')->after('sl');
            }

            if (!Schema::hasColumn('demo_trading_logs', 'coinz')) {
                $table->string('coinz')->default('0')->after('finalz');
            }

            if (!Schema::hasColumn('demo_trading_logs', 'profit')) {
                $table->string('profit')->default('0')->after('coinz');
            }
        });
    }

    public function down()
    {
        Schema::table('trading_logs', function (Blueprint $table) {
            foreach (['price', 'finalz', 'coinz', 'profit'] as $column) {
                if (Schema::hasColumn('trading_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('demo_trading_logs', function (Blueprint $table) {
            foreach (['finalz', 'coinz', 'profit'] as $column) {
                if (Schema::hasColumn('demo_trading_logs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
