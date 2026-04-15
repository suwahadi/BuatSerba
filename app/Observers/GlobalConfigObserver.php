<?php

namespace App\Observers;

use App\Models\GlobalConfig;
use Illuminate\Support\Facades\Cache;

class GlobalConfigObserver
{
    /**
     * Handle the GlobalConfig "created" event.
     */
    public function created(GlobalConfig $globalConfig): void
    {
        $this->clearCache();
    }

    /**
     * Handle the GlobalConfig "updated" event.
     */
    public function updated(GlobalConfig $globalConfig): void
    {
        $this->clearCache();
    }

    /**
     * Handle the GlobalConfig "deleted" event.
     */
    public function deleted(GlobalConfig $globalConfig): void
    {
        $this->clearCache();
    }

    /**
     * Handle the GlobalConfig "restored" event.
     */
    public function restored(GlobalConfig $globalConfig): void
    {
        $this->clearCache();
    }

    /**
     * Clear global config cache
     */
    protected function clearCache(): void
    {
        $configs = GlobalConfig::all();
        
        foreach ($configs as $config) {
            Cache::forget('global_config.' . $config->key);
        }
        
        Cache::forget('global_config.cashback');
        Cache::forget('global_config.premium_membership_price');
        Cache::forget('global_config.maintenance_mode');
        Cache::forget('global_config.site_name');
    }
}
