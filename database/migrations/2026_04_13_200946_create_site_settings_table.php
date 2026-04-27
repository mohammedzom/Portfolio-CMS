<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            // Personal
            $table->string('first_name')->default('Mohamed');
            $table->string('last_name')->default('Zomlot');
            $table->string('tagline')->nullable();
            $table->text('bio')->nullable();
            $table->text('about_me')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cv_file')->nullable();
            $table->string('url_prefix')->nullable();
            $table->string('url_suffix')->nullable();
            $table->json('languages')->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();

            // Social links
            $table->json('social_links')->nullable();

            // Stats
            $table->unsignedSmallInteger('years_experience')->default(0);
            $table->unsignedSmallInteger('projects_count')->default(0);
            $table->unsignedSmallInteger('clients_count')->default(0);

            // Availability
            $table->boolean('available_for_freelance')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
