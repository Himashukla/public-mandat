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
        Schema::create('poll_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained()->onDelete('cascade');

            $table->string('label');                         
            $table->integer('position')->default(0);      // future: drag & drop ordering
            $table->string('color')->nullable();          // future: custom color per option in options list

            $table->string('image')->nullable();          // future: we can provide image options
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
};
