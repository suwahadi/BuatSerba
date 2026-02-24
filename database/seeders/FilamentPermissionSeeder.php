<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class FilamentPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guardName = 'web';

        $resourcePermissions = [
            'view_any',
            'view',
            'create',
            'update',
            'delete',
            'restore',
            'force_delete',
        ];

        $this->syncPermissionsForDirectory(
            basePath: app_path('Filament/Resources'),
            baseNamespace: 'App\\Filament\\Resources\\',
            prefix: 'resource',
            abilities: $resourcePermissions,
            guardName: $guardName,
        );

        $this->syncPermissionsForDirectory(
            basePath: app_path('Filament/Pages'),
            baseNamespace: 'App\\Filament\\Pages\\',
            prefix: 'page',
            abilities: ['access'],
            guardName: $guardName,
        );

        $this->syncPermissionsForDirectory(
            basePath: app_path('Filament/Widgets'),
            baseNamespace: 'App\\Filament\\Widgets\\',
            prefix: 'widget',
            abilities: ['access'],
            guardName: $guardName,
        );

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<int, string>  $abilities
     */
    protected function syncPermissionsForDirectory(
        string $basePath,
        string $baseNamespace,
        string $prefix,
        array $abilities,
        string $guardName,
    ): void {
        if (! File::exists($basePath)) {
            return;
        }

        $files = File::allFiles($basePath);

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $relative = Str::of($file->getPathname())
                ->after($basePath . DIRECTORY_SEPARATOR)
                ->replace(DIRECTORY_SEPARATOR, '\\')
                ->replaceLast('.php', '')
                ->toString();

            $fqcn = $baseNamespace . $relative;

            if (! class_exists($fqcn)) {
                continue;
            }

            // Only create permissions for Filament Resources / Pages / Widgets
            $isTarget = match ($prefix) {
                'resource' => is_subclass_of($fqcn, \Filament\Resources\Resource::class),
                'page' => is_subclass_of($fqcn, \Filament\Pages\Page::class),
                'widget' => is_subclass_of($fqcn, \Filament\Widgets\Widget::class),
                default => false,
            };

            if (! $isTarget) {
                continue;
            }

            $key = $this->makeEntityKey($fqcn);

            foreach ($abilities as $ability) {
                Permission::firstOrCreate([
                    'name' => "{$prefix}.{$key}.{$ability}",
                    'guard_name' => $guardName,
                ]);
            }
        }
    }

    protected function makeEntityKey(string $fqcn): string
    {
        $base = class_basename($fqcn);

        // Remove common suffixes
        $base = Str::of($base)
            ->replaceEnd('Resource', '')
            ->replaceEnd('Page', '')
            ->replaceEnd('Widget', '')
            ->toString();

        // Convert to snake_case and pluralize
        return Str::of($base)->snake()->plural()->toString();
    }
}
