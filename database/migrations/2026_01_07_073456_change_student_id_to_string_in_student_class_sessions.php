<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::table('student_class_sessions', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['student_id']);

            // Change column type
            $table->string('student_id')->change();

            // Re-add foreign key
            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('student_class_sessions', function (Blueprint $table) {
            $table->dropForeign(['student_id']);
            $table->unsignedInteger('student_id')->change();

            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }
};
