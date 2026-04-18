<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('category', ['Web', 'App', 'Mobile', 'Script', 'Other'])->default('Web');
            $table->json('tech_stack')->nullable();
            $table->json('images')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->smallInteger('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->index(['category', 'sort_order']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
