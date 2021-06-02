<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusVerifiedAtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->string('status')->nullable();
            $table->string('otp')->nullable();
            $table->string('otp_verified_at')->nullable();
            $table->integer('verified_at')->nullable();
            $table->string('user_role')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table->dropColumn('status');
            $table->dropColumn('verified_at');
            $table->dropColumn('user_role');
            $table->dropColumn('otp');
            $table->dropColumn('otp_verified_at');

        });
    }
}
