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
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // null = guest

            $table->string('ip_address', 45)->nullable();     // IPv4 and IPv6 support
            $table->string('session_id')->nullable();         // extra guest tracking layer

            $table->string('user_agent')->nullable();         // future: device tracking
            $table->string('country')->nullable();            // future: geo tracking
            $table->boolean('is_flagged')->default(false);    // future: flag suspicious votes

            $table->timestamps();

            // Prevent duplicate votes
            // One vote per user per poll
            $table->unique(['poll_id', 'user_id']);

            // One vote per IP per poll (for guests)
            $table->unique(['poll_id', 'ip_address']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
