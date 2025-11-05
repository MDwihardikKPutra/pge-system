<?php

namespace App\Helpers;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    /**
     * Cache duration constants (in seconds)
     */
    const CACHE_1_HOUR = 3600;
    const CACHE_30_MINUTES = 1800;
    const CACHE_10_MINUTES = 600;
    const CACHE_5_MINUTES = 300;

    /**
     * Check if the current cache store supports tagging
     */
    private static function supportsTagging(): bool
    {
        $store = Cache::getStore();
        $driver = config('cache.default');
        
        // Only redis, memcached, and dynamodb support tagging
        return in_array($driver, ['redis', 'memcached', 'dynamodb']);
    }

    /**
     * Get cache instance with tags if supported, otherwise regular cache
     */
    private static function taggedCache(array $tags = [])
    {
        if (self::supportsTagging() && !empty($tags)) {
            return Cache::tags($tags);
        }
        return Cache::store();
    }

    /**
     * Get cached projects dropdown
     * Cache key: 'projects_dropdown'
     * Cache duration: 1 hour
     */
    public static function getProjectsDropdown()
    {
        return Cache::remember('projects_dropdown', self::CACHE_1_HOUR, function() {
            return Project::active()->orderedByName()->get();
        });
    }

    /**
     * Clear projects dropdown cache
     */
    public static function clearProjectsDropdown()
    {
        Cache::forget('projects_dropdown');
    }

    /**
     * Clear all project-related caches
     */
    public static function clearProjectCaches()
    {
        self::clearProjectsDropdown();
        // Clear EAR projects dropdown if exists
        Cache::forget('ear_projects_dropdown');
        // Clear project management users cache
        Cache::forget('project_management_all_users');
        // Clear dashboard module data cache (projects affect module data)
        $currentMonth = now()->format('Y-m');
        Cache::forget("admin_dashboard_module_data_{$currentMonth}");
    }

    /**
     * Get cached dashboard users list
     * Cache key: 'dashboard_users_list'
     * Cache duration: 30 minutes
     */
    public static function getDashboardUsersList()
    {
        return Cache::remember('dashboard_users_list', self::CACHE_30_MINUTES, function() {
            return \App\Models\User::where('is_active', true)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'user');
                })
                ->with(['modules' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Clear dashboard users list cache
     */
    public static function clearDashboardUsersList()
    {
        Cache::forget('dashboard_users_list');
    }

    /**
     * Get cached admin dashboard recent activities
     * Cache key: 'admin_dashboard_recent_activities_{date}'
     * Cache duration: 10 minutes
     * Uses cache tags for grouped invalidation if supported
     */
    public static function getAdminDashboardRecentActivities(callable $callback)
    {
        $cacheKey = 'admin_dashboard_recent_activities_' . now()->format('Y-m-d-H-i');
        
        if (self::supportsTagging()) {
            return Cache::tags(['dashboard', 'dashboard-recent-activities'])
                ->remember($cacheKey, self::CACHE_10_MINUTES, $callback);
        }
        
        // Fallback: use regular cache without tags
        return Cache::remember($cacheKey, self::CACHE_10_MINUTES, $callback);
    }

    /**
     * Clear admin dashboard recent activities cache
     * Uses cache tags for efficient grouped invalidation if supported
     */
    public static function clearAdminDashboardRecentActivities()
    {
        if (self::supportsTagging()) {
            // Clear all cache with dashboard-recent-activities tag
            Cache::tags(['dashboard-recent-activities'])->flush();
            return;
        }
        
        // Fallback: clear cache for current and previous hour
        $currentHour = now()->format('Y-m-d-H');
        $previousHour = now()->subHour()->format('Y-m-d-H');
        
        for ($i = 0; $i <= 59; $i++) {
            Cache::forget("admin_dashboard_recent_activities_{$currentHour}-" . str_pad($i, 2, '0', STR_PAD_LEFT));
            Cache::forget("admin_dashboard_recent_activities_{$previousHour}-" . str_pad($i, 2, '0', STR_PAD_LEFT));
        }
    }

    /**
     * Clear all dashboard-related caches
     * Uses cache tags if supported, otherwise clears individual cache keys
     */
    public static function clearDashboardCaches()
    {
        if (self::supportsTagging()) {
            // Clear all dashboard-related caches using tags
            Cache::tags(['dashboard'])->flush();
            return;
        }
        
        // Fallback: clear individual cache keys
        self::clearAdminDashboardRecentActivities();
        
        // Clear module data cache for current month and previous month
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        Cache::forget("admin_dashboard_module_data_{$currentMonth}");
        Cache::forget("admin_dashboard_module_data_{$previousMonth}");
    }

    /**
     * Get cached EAR dropdowns
     */
    public static function getEarUsersDropdown()
    {
        return Cache::remember('ear_users_dropdown', self::CACHE_1_HOUR, function() {
            return \App\Models\User::where('is_active', true)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'user');
                })
                ->orderBy('name')
                ->get();
        });
    }

    public static function getEarProjectsDropdown()
    {
        return Cache::remember('ear_projects_dropdown', self::CACHE_1_HOUR, function() {
            return Project::where('is_active', true)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get cached project management users
     */
    public static function getProjectManagementUsers()
    {
        return Cache::remember('project_management_all_users', self::CACHE_1_HOUR, function() {
            return \App\Models\User::where('is_active', true)
                ->whereDoesntHave('roles', function($q) {
                    $q->where('name', 'admin');
                })
                ->orderBy('name')
                ->get();
        });
    }
}

