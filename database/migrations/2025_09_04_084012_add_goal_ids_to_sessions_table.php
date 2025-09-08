<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGoalIdsToSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up()
{
    Schema::table('sessions', function (Blueprint $table) {
        $table->json('goal_ids')->nullable(); // store array of goal IDs
    });
}

public function down()
{
    Schema::table('sessions', function (Blueprint $table) {
        $table->dropColumn('goal_ids');
    });
}
}
