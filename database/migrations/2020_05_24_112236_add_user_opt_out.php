<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserOptOut extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'opt_out_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('opt_out_token')->unique()->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'opt_out_token')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('opt_out_token');
            });
        }
    }
}
