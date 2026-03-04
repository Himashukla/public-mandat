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
        Schema::create('polls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // admin who created it

            $table->string('question');
            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('allow_guest_votes')->default(true);

            $table->timestamp('starts_at')->nullable();       // future: schedule polls
            $table->timestamp('ends_at')->nullable();         // future: auto-close polls

            $table->boolean('is_multiple_choice')->default(false); // future: we can allow multiple choice as answers
            $table->integer('max_votes_per_user')->default(1); // future: we can allow multiple votes from users
            $table->string('results_visibility')->default('always'); // future: we can choose when to shows votes - always/after_vote/after_close

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
