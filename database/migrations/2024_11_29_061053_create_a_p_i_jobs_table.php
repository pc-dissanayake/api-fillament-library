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
        Schema::create('a_p_i_jobs', function (Blueprint $table) {
            $table->char('id',36)->primary();
            $table->char('public_uuid',36)->unique();
            $table->boolean('active')->default(true);
            $table->boolean('locked')->default(false);
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('api_prefix_uuid',36)->unique();
            $table->string('table_uuid',36)->unique();
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_p_i_jobs');
    }
};
