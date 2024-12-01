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
        Schema::create('api_table_structures', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('api_job_id');
            $table->string('column_name');
            $table->string('column_type');
            $table->string('collation')->nullable();
            $table->string('attributes')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_nullable')->default(true);
            $table->string('default_value')->nullable();
            $table->string('table_key')->nullable();
            $table->text('comments')->nullable();
            $table->string('laravel_validation_rule')->nullable();
            $table->string('laravel_validation_rule_value')->nullable();
            $table->timestamps();

            $table->foreign('api_job_id')->references('id')->on('a_p_i_jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_table_structures');
    }
};