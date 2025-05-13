<?php

namespace App\Console\Commands\MakeDTO;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeDtoCommand extends Command
{
    protected $signature = 'make:dto {name} {--interface}';
    protected $description = 'Create a new DTO class (with optional interface)';

    public function handle()
    {
        $name = Str::studly($this->argument('name'));
        $dtoPath = app_path("DTO/{$name}.php");
        $interfaceName = "{$name}Interface";
        $interfacePath = app_path("DTO/Contracts/{$interfaceName}.php");

        // DTO scaffold
        $dtoStub = "<?php

namespace App\DTO;" . ($this->option('interface') ? "\n\nuse App\DTO\Contracts\\{$interfaceName};" : '') . "

class {$name}" . ($this->option('interface') ? " implements {$interfaceName}" : '') . "
{
    // TODO: Add properties and constructor
}
";

        // Write DTO
        if (!File::exists($dtoPath)) {
            File::ensureDirectoryExists(dirname($dtoPath));
            File::put($dtoPath, $dtoStub);
            $this->info("✅ DTO created: {$dtoPath}");
        } else {
            $this->warn("⚠️ DTO already exists: {$dtoPath}");
        }

        // Optionally generate interface
        if ($this->option('interface')) {
            if (!File::exists($interfacePath)) {
                $interfaceStub = "<?php

namespace App\DTO\Contracts;

interface {$interfaceName}
{
    //
}
";
                File::ensureDirectoryExists(dirname($interfacePath));
                File::put($interfacePath, $interfaceStub);
                $this->info("✅ Interface created: {$interfacePath}");
            } else {
                $this->warn("⚠️ Interface already exists: {$interfacePath}");
            }
        }

        return Command::SUCCESS;
    }
}
