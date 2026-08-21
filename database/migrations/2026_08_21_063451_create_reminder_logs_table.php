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
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_id')->constrained('certifications')->onDelete('cascade');
            $table->string('type'); // 'H-5' or 'H+5'
            $table->string('recipient_email');
            $table->string('status')->default('sent');
            $table->timestamp('sent_at')->useCurrent();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['certification_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
