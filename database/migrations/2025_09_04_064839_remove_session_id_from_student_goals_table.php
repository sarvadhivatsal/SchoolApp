<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveSessionIdFromStudentGoalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
   public function up(): void
    {
        Schema::table('student_goals', function (Blueprint $table) {
            $table->dropForeign('student_goals_session_id_foreign');
            $table->dropColumn('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_goals', function (Blueprint $table) {
            // It's good practice to revert changes in the 'down' method
            $table->unsignedBigInteger('session_id')->after('student_id');
            $table->foreign('session_id')->references('id')->on('sessions')->onDelete('cascade');
        });
    }
};


