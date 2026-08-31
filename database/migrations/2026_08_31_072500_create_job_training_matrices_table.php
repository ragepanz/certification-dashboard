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
        Schema::create('job_training_matrices', function (Blueprint $table) {
            $table->id();
            $table->string('job_title');
            $table->string('training_code')->nullable();
            $table->string('training_name');
            $table->string('traintype')->nullable();
            $table->string('validity_type')->default('Forever'); // '2-Year' atau 'Forever'
            $table->boolean('no_need_training')->default(false);
            $table->timestamps();

            $table->unique(['job_title', 'training_name']);
            $table->index('job_title');
            $table->index('training_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_training_matrices');
    }
};
