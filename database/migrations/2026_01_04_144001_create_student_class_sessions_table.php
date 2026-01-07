<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Ensure a clean state if a previous failed migration partially created the table
        Schema::dropIfExists('student_class_sessions');

        Schema::create('student_class_sessions', function (Blueprint $table) {
            $table->id();

            $table->unsignedInteger('session_id');
            $table->string('student_id');

            $table->enum('enrollment_status', ['enrolled', 'dropped'])->default('enrolled');
            $table->timestamp('enrolled_at')->useCurrent();

            $table->unique(['session_id', 'student_id']);

            $table->foreign('session_id')
                ->references('session_id')
                ->on('class_sessions')
                ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('student_id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_sessions');
    }
};

