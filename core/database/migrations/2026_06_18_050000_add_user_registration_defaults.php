<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `users` MODIFY `tcal` varchar(255) NOT NULL DEFAULT '0'");
        DB::statement("ALTER TABLE `users` MODIFY `profile_picture` varchar(255) NOT NULL DEFAULT 'user.png'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `users` MODIFY `tcal` varchar(255) NOT NULL");
        DB::statement("ALTER TABLE `users` MODIFY `profile_picture` varchar(255) NOT NULL");
    }
};
