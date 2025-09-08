<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSessionIdToStudentGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_goals', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('student_id');
        $table->foreign('session_id')->references('id')->on('sessions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_goals', function (Blueprint $table) {
            $table->dropForeign(['session_id']);
        $table->dropColumn('session_id');
        });
    }
}
