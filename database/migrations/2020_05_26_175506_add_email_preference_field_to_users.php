<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailPreferenceFieldToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('users', 'email_opt_in')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('email_opt_in')->default(0);
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
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'email_opt_in')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropColumn('email_opt_in');
                });
            }
        });
    }
}
