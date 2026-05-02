<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait ManagesCache
{
    /**
     * Forget portfolio-related cache keys.
     */
    protected function forgetPortfolioCache(): void
    {
        Cache::forget('portfolio_all');
        Cache::forget('portfolio_settings');
    }

    /**
     * Forget resource-specific cache keys including archived versions.
     */
    protected function forgetResourceCache(string $resource): void
    {
        Cache::forget($resource);
        Cache::forget("{$resource}_archived");
        $this->forgetPortfolioCache();
    }

    /**
     * Forget project-specific cache keys.
     */
    protected function forgetProjectCache(string $slug): void
    {
        Cache::forget("project_{$slug}");
        $this->forgetResourceCache('projects');
    }

    /**
     * Forget skills cache keys.
     */
    protected function forgetSkillsCache(): void
    {
        Cache::forget('skills');
        Cache::forget('skills_archived');
        $this->forgetPortfolioCache();
    }

    /**
     * Forget services cache keys.
     */
    protected function forgetServicesCache(): void
    {
        Cache::forget('services');
        Cache::forget('services_archived');
        $this->forgetPortfolioCache();
    }

    /**
     * Forget experiences cache keys.
     */
    protected function forgetExperiencesCache(): void
    {
        Cache::forget('experiences');
        Cache::forget('experiences_archived');
        $this->forgetPortfolioCache();
    }

    /**
     * Forget education cache keys.
     */
    protected function forgetEducationCache(): void
    {
        Cache::forget('educations');
        Cache::forget('educations_archived');
        $this->forgetPortfolioCache();
    }

    /**
     * Forget achievements cache keys.
     */
    protected function forgetAchievementsCache(): void
    {
        Cache::forget('achievements');
        Cache::forget('achievements_archived');
        $this->forgetPortfolioCache();
    }

    /**
     * Forget messages cache keys.
     */
    protected function forgetMessagesCache(): void
    {
        Cache::forget('messages');
        Cache::forget('messages_archived');
        $this->forgetPortfolioCache();
    }
}
