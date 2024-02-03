<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RBACMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('user_roles')) {
            Schema::create('user_roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('role_name', 255);
                $table->bigInteger('user_id')->unsigned();
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('users', 'is_activated')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_activated')->default(0);
            });
        }

        if (!Schema::hasColumn('users', 'has_cookie_consent')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('has_cookie_consent')->default(0);
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
        Schema::dropIfExists('user_roles');
        if (Schema::hasColumn('users', 'is_activated')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_activated');
            });
        }
        if (Schema::hasColumn('users', 'has_cookie_consent')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('has_cookie_consent');
            });
        }
    }
}
