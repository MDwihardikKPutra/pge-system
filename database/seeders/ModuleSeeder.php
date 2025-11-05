<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sync modules from config/modules.php to database
     */
    public function run(): void
    {
        $this->command->info('📦 Syncing modules from config to database...');
        
        $modulesConfig = config('modules.list', []);
        $sortOrder = 0;

        foreach ($modulesConfig as $key => $config) {
            // Default modules: work-plan dan work-realization harus is_default = true
            $isDefault = in_array($key, ['work-plan', 'work-realization']) ? true : false;
            
            Module::updateOrCreate(
                ['key' => $key],
                [
                    'label' => $config['label'] ?? ucfirst($key),
                    'icon' => $config['icon'] ?? '•',
                    'description' => $config['description'] ?? null,
                    'routes' => $config['routes'] ?? [],
                    'actions' => $config['actions'] ?? config('modules.default_actions', ['view', 'create', 'update', 'delete']),
                    'assignable_to_user' => $config['assignable_to_user'] ?? true,
                    'admin_only' => $config['admin_only'] ?? false,
                    'is_default' => $isDefault, // work-plan dan work-realization adalah default modules
                    'is_active' => true,
                    'sort_order' => $sortOrder++,
                    'category' => $config['category'] ?? 'modul', // Default to modul
                ]
            );
            
            $this->command->info("   ✅ Module '{$key}' synced");
        }

        $this->command->info('✅ All modules synced successfully!');
        $this->command->info('   Total modules: ' . Module::count());
        $this->command->info('');
        $this->command->info('📌 Default modules for user role:');
        $this->command->info('   - work-plan (Rencana Kerja)');
        $this->command->info('   - work-realization (Realisasi Kerja)');
    }
}
