<?php

namespace App\Console\Commands\MakeResources;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeTypedResourceCommand extends GeneratorCommand
{
    protected $name = 'make:typed-resource';

    protected $description = 'Create a new typed JSON resource with model binding';

    protected $type = 'Resource';

    protected function getStub(): string
    {
        return app_path('Console/Commands/MakeResources/stubs/typed-resource.stub');
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\\Http\\Resources';
    }

    protected function getArguments(): array
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource class'],
            ['model', InputArgument::REQUIRED, 'The model class the resource is bound to'],
        ];
    }

    protected function buildClass($name): string
    {
        $class = parent::buildClass($name);

        $model = $this->argument('model');
        $modelClass = $this->qualifyModel($model);
        $modelBaseName = class_basename($model);

        return str_replace(
            ['{{ modelClass }}', '{{ modelBaseName }}'],
            [$modelClass, $modelBaseName],
            $class
        );
    }

    protected function qualifyModel(string $model): string
    {
        $model = trim($model, '\\');

        return str_starts_with($model, 'App\\Models')
            ? $model
            : 'App\\Models\\' . $model;
    }

    protected function getOptions(): array
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the resource already exists'],
        ];
    }
}
