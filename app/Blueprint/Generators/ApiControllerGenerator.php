<?php

namespace App\Blueprint\Generators;

use Blueprint\Blueprint;
use Blueprint\Contracts\Generator;
use Blueprint\Models\Controller;
use Blueprint\Tree;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ApiControllerGenerator implements Generator
{
    protected Filesystem $filesystem;

    protected Tree $tree;

    protected array $output = [];

    protected array $imports = [];

    protected array $types = ['controllers'];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function output(Tree $tree): array
    {
        $this->tree = $tree;

        /** @var Controller $controller */
        foreach ($tree->controllers() as $controller) {
            // Only process API controllers (those in Api namespace)
            if (! Str::startsWith($controller->namespace(), 'Api')) {
                continue;
            }

            $this->generateApiController($controller);
        }

        return $this->output;
    }

    public function types(): array
    {
        return $this->types;
    }

    protected function generateApiController(Controller $controller): void
    {
        $this->imports = [];

        // Load the main class stub
        $stub = $this->filesystem->stub('api-controller.class.stub');

        // Get the model for this controller
        $modelName = Str::singular($controller->prefix());
        $model = $this->tree->modelForContext($modelName);

        // Add required imports
        $this->addImport('Illuminate\Http\JsonResponse');
        $this->addImport('App\Http\Controllers\Api\BaseApiController');
        $this->addImport('Spatie\QueryBuilder\AllowedFilter');

        if ($model) {
            $this->addImport($model->fullyQualifiedClassName());

            // Add Resource import
            $resourceFqcn = $this->getResourceFqcn($controller->namespace(), $modelName);
            $this->addImport($resourceFqcn);
        }

        // Add Data object import for store/update methods
        foreach ($controller->methods() as $methodName => $_statements) {
            if (in_array($methodName, ['store', 'update'])) {
                $dataClass = $this->getDataFqcn($modelName);
                $this->addImport($dataClass);
                break; // Only need to add once
            }
        }

        $path = $this->getPath($controller);
        $content = $this->populateStub($stub, $controller, $model);

        $this->create($path, $content);
        $this->output['created'][] = ['API Controller', $path];
    }

    protected function populateStub(string $stub, Controller $controller, $model): string
    {
        $modelName = Str::singular($controller->prefix());

        $stub = str_replace('{{ namespace }}', $controller->fullyQualifiedNamespace(), $stub);
        $stub = str_replace('{{ class }}', $controller->className(), $stub);
        $stub = str_replace('{{ model }}', Str::studly($modelName), $stub);
        $stub = str_replace('{{ modelClass }}', Str::studly($modelName).'::class', $stub);
        $stub = str_replace('{{ resourceClass }}', Str::studly($modelName).'Resource::class', $stub);

        // Generate allowed filters from model columns
        $stub = str_replace('{{ allowedFilters }}', $this->buildAllowedFilters($model), $stub);

        // Generate allowed sorts from model columns
        $stub = str_replace('{{ allowedSorts }}', $this->buildAllowedSorts($model), $stub);

        // Generate allowed includes from relationships
        $stub = str_replace('{{ allowedIncludes }}', $this->buildAllowedIncludes($model), $stub);

        // Generate CRUD methods
        $stub = str_replace('{{ methods }}', $this->buildApiMethods($controller, $modelName), $stub);

        $stub = str_replace('{{ imports }}', $this->buildImports(), $stub);

        return $stub;
    }

    protected function buildAllowedFilters($model): string
    {
        if (! $model) {
            return '[]';
        }

        $filters = [];
        $hasAllowedFilter = false;

        foreach ($model->columns() as $column) {
            $columnName = $column->name();

            // Skip timestamps and id
            if (in_array($columnName, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            // Add exact filters for foreign keys and enums
            if (Str::endsWith($columnName, '_id')) {
                $filters[] = "AllowedFilter::exact('{$columnName}')";
                $hasAllowedFilter = true;
            } elseif ($column->dataType() === 'enum') {
                $filters[] = "AllowedFilter::exact('{$columnName}')";
                $hasAllowedFilter = true;
            } elseif (in_array($column->dataType(), ['string', 'text', 'longtext'])) {
                // Regular partial match for text fields
                $filters[] = "'{$columnName}'";
            }
        }

        // Remove AllowedFilter from imports if not used
        if (! $hasAllowedFilter) {
            $this->removeImport('Spatie\QueryBuilder\AllowedFilter');
        }

        return $this->formatArray($filters);
    }

    protected function buildAllowedSorts($model): string
    {
        if (! $model) {
            return "['created_at', 'id']";
        }

        $sorts = [];

        // Add common sortable fields from model
        foreach ($model->columns() as $column) {
            $name = $column->name();

            if (in_array($name, ['created_at', 'published_at', 'updated_at', 'title', 'name'])) {
                $sorts[] = $name;
            }
        }

        // Always include id if not already there
        if (! in_array('id', $sorts)) {
            $sorts[] = 'id';
        }

        return $this->formatArray($sorts, true);
    }

    protected function buildAllowedIncludes($model): string
    {
        if (! $model || ! method_exists($model, 'relationships')) {
            return '[]';
        }

        $includes = [];

        foreach ($model->relationships() as $relationships) {
            // Relationships can be a string or array
            if (is_string($relationships)) {
                $includes[] = Str::camel($relationships);
            } elseif (is_array($relationships)) {
                foreach ($relationships as $relationship) {
                    $includes[] = Str::camel($relationship);
                }
            }
        }

        return $this->formatArray(array_unique($includes), true);
    }

    protected function buildApiMethods(Controller $controller, string $modelName): string
    {
        $methods = '';
        $camelModel = Str::camel($modelName);
        $studlyModel = Str::studly($modelName);

        // Only generate store, update, destroy (index and show are in BaseApiController)
        foreach ($controller->methods() as $name => $_statements) {
            if (! in_array($name, ['store', 'update', 'destroy'])) {
                continue;
            }

            $stubFile = "api-controller.method.{$name}.stub";

            try {
                $stub = $this->filesystem->stub($stubFile);
            } catch (\Exception) {
                // Stub file doesn't exist, skip this method
                continue;
            }

            $method = str_replace('{{ modelVariable }}', $camelModel, $stub);
            $method = str_replace('{{ modelClass }}', $studlyModel, $method);
            $method = str_replace('{{ modelName }}', Str::title(Str::snake($studlyModel, ' ')), $method);
            $method = str_replace('{{ resourceClass }}', $studlyModel.'Resource', $method);

            // Add Data object for store/update
            if (in_array($name, ['store', 'update'])) {
                $dataClass = $studlyModel.'Data';
                $method = str_replace('{{ dataClass }}', $dataClass, $method);
            }

            if (! empty($methods)) {
                $methods .= PHP_EOL;
            }
            $methods .= $method;
        }

        return empty($methods) ? '' : PHP_EOL.$methods;
    }

    protected function formatArray(array $items, bool $quoted = false): string
    {
        if (empty($items)) {
            return '[]';
        }

        if ($quoted) {
            $items = array_map(fn ($item) => "'{$item}'", $items);
        }

        if (count($items) === 1) {
            return '['.$items[0].']';
        }

        return '['.PHP_EOL.'            '.implode(','.PHP_EOL.'            ', $items).','.PHP_EOL.'        ]';
    }

    protected function addImport(string $class): void
    {
        if (! in_array($class, $this->imports)) {
            $this->imports[] = $class;
        }
    }

    protected function removeImport(string $class): void
    {
        $this->imports = array_filter($this->imports, fn ($import) => $import !== $class);
    }

    protected function buildImports(): string
    {
        if (empty($this->imports)) {
            return '';
        }

        sort($this->imports);

        return implode(PHP_EOL, array_map(fn ($import) => "use {$import};", $this->imports));
    }

    protected function getResourceFqcn(string $namespace, string $modelName): string
    {
        return config('blueprint.namespace').'\\Http\\Resources\\'.$namespace.'\\'.Str::studly($modelName).'Resource';
    }

    protected function getFormRequestFqcn(string $namespace, string $modelName, string $method): string
    {
        return config('blueprint.namespace').'\\Http\\Requests\\'.$namespace.'\\'.Str::studly($modelName).Str::studly($method).'Request';
    }

    protected function getDataFqcn(string $modelName): string
    {
        return config('blueprint.namespace').'\\Data\\'.Str::studly($modelName).'Data';
    }

    protected function getPath(Controller $controller): string
    {
        $path = str_replace('\\', '/', Blueprint::relativeNamespace($controller->fullyQualifiedClassName()));

        return sprintf('%s/%s.php', Blueprint::appPath(), $path);
    }

    protected function create(string $path, string $content): void
    {
        if (! $this->filesystem->exists(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0755, true);
        }

        $this->filesystem->put($path, $content);
    }
}
