<?php
namespace Wallacemaxters\Navalha\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'navalha:make-component')]
class MakeComponent extends Command
{
    protected $signature = 'navalha:make-component {name} {--force}';

    public function handle()
    {
        $component = $this->argument('name');

        $this->generateClass($component);
        $this->generateView($component);

    }

    protected function generateView($component)
    {
        $viewFilename = resource_path(
            'views/navalha/' . str($component)->lower()->replace('\\', '/') . '.blade.php'
        );

        File::ensureDirectoryExists(File::dirname($viewFilename), 0755, true);

        $stub = base_path('stubs/view.stub');

        if (! File::exists($stub)) {
            $stub = __DIR__ . '/../../stubs/view.stub';
        }

        $contents = File::get($stub);

        if (File::exists($viewFilename) && !$this->option('force')) {
            $this->error("File {$viewFilename} already exists!");
            return;
        }

        File::put($viewFilename, $contents);

        $this->line("File <info>{$viewFilename}</info> created");
    }

    protected function generateClass(string $component)
    {
        $className = 'App\\Navalha\\' . Str::replace('/', "\\", $component);

        $classBaseName = class_basename($className);

        $replaces = [
            '{{ view }}' => 'navalha.' . str($component)->lower()->replace(['\\', '/'], '.'),
            '{{ componentClass }}' => $classBaseName,
        ];

        $stub = base_path('stubs/component.stub');

        if (! File::exists($stub)) {
            $stub = __DIR__ . '/../../stubs/component.stub';
        }

        $path = str($className)->after('App\\')->start('app/')->replace('\\', '/')->finish('.php');

        File::ensureDirectoryExists(File::dirname($path), 0755, true);

        $contents = Str::swap($replaces, File::get($stub));

        File::put($path, $contents);

        $this->line("File <info>{$path}</info> created");
    }
}
