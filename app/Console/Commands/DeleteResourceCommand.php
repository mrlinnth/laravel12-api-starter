<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DeleteResourceCommand extends Command
{
    protected $signature = 'project:delete-resource {name : The name of the resource to delete}';

    protected $description = 'Delete all related files for an API resource entity';

    public function handle(): int
    {
        $name = $this->argument('name');

        $this->warn('⚠️  WARNING: This will permanently delete all files related to the "'.$name.'" resource!');
        $this->newLine();

        if (! $this->confirm('Are you sure you want to proceed?', false)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('Deleting files for resource: '.$name);
        $this->newLine();

        $filePaths = $this->getResourceFilePaths($name);
        $deletedFiles = [];
        $notFoundFiles = [];

        foreach ($filePaths as $path) {
            $absolutePath = base_path($path);

            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
                $deletedFiles[] = $path;
                $this->line("<fg=green>✓</> Deleted: {$path}");
            } else {
                $notFoundFiles[] = $path;
                $this->line("<fg=yellow>⊘</> Not found: {$path}");
            }
        }

        $this->newLine();

        if (count($deletedFiles) > 0) {
            $this->info('Successfully deleted '.count($deletedFiles).' file(s):');
            foreach ($deletedFiles as $file) {
                $this->line('  - '.$file);
            }
            $this->newLine();
        }

        if (count($notFoundFiles) > 0) {
            $this->comment('Skipped '.count($notFoundFiles).' file(s) (not found):');
            foreach ($notFoundFiles as $file) {
                $this->line('  - '.$file);
            }
            $this->newLine();
        }

        $this->showManualInstructions($name);

        return self::SUCCESS;
    }

    /**
     * Get all file paths for the given resource.
     *
     * @return array<int, string>
     */
    protected function getResourceFilePaths(string $name): array
    {
        return [
            "app/Data/{$name}Data.php",
            "app/Http/Controllers/Api/{$name}Controller.php",
            "app/Http/Requests/Api/{$name}StoreRequest.php",
            "app/Http/Requests/Api/{$name}UpdateRequest.php",
            "app/Http/Resources/Api/{$name}Collection.php",
            "app/Http/Resources/Api/{$name}Resource.php",
            "app/Models/{$name}.php",
            "database/factories/{$name}Factory.php",
            "database/seeders/{$name}Seeder.php",
        ];
    }

    protected function showManualInstructions(string $name): void
    {
        $tableName = \Illuminate\Support\Str::snake(\Illuminate\Support\Str::pluralStudly($name));

        $this->warn('📋 MANUAL CLEANUP REQUIRED:');
        $this->newLine();
        $this->line('Please complete the following tasks manually:');
        $this->newLine();
        $this->line('1. <fg=cyan>Database & Migrations</>');
        $this->line("   - Remove migration file(s) for the '{$tableName}' table from database/migrations/");
        $this->line("   - Drop the '{$tableName}' table if it exists:");
        $this->line("     <fg=gray>php artisan tinker</>");
        $this->line("     <fg=gray>Schema::dropIfExists('{$tableName}');</>");
        $this->line('     <fg=gray>exit</> or run a new migration');
        $this->newLine();
        $this->line('2. <fg=cyan>Routes</>');
        $this->line('   - Remove API routes from routes/api.php');
        $this->line('   - Remove web routes from routes/web.php (if any)');
        $this->newLine();
        $this->line('3. <fg=cyan>References</>');
        $this->line('   - Search and remove references in other models (relationships, imports)');
        $this->line('   - Update any controllers, services, or classes that use this resource');
        $this->line('   - Remove test files from tests/Feature/ and tests/Unit/');
        $this->newLine();
        $this->line('4. <fg=cyan>Database Seeders</>');
        $this->line('   - Remove seeder calls from database/seeders/DatabaseSeeder.php');
        $this->newLine();
        $this->line('5. <fg=cyan>Regenerate Cache & Documentation</>');
        $this->line('   <fg=gray>php artisan optimize:clear</>');
        $this->line('   <fg=gray>php artisan ide-helper:generate</>');
        $this->line('   <fg=gray>php artisan ide-helper:meta</>');
        $this->newLine();
    }
}
