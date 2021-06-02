<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCounterIdCountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('users', 'counter_id')){
            Schema::table('users', function (Blueprint $table) {
                //
                $table->unsignedBigInteger('counter_id')->nullable();
                $table->foreign('counter_id')
                    ->references('id')
                    ->on('counters');
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
        if(Schema::hasColumn('users', 'counter_id')){
            Schema::table('users', function (Blueprint $table) {
                //
                $table->dropForeign('counter_id');
            });
        }
    }
}
