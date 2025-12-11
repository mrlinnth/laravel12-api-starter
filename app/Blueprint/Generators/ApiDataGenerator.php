<?php

namespace App\Blueprint\Generators;

use Blueprint\Blueprint;
use Blueprint\Contracts\Generator;
use Blueprint\Models\Column;
use Blueprint\Models\Model;
use Blueprint\Tree;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ApiDataGenerator implements Generator
{
    protected Filesystem $filesystem;

    protected Tree $tree;

    protected array $output = [];

    protected array $imports = [];

    protected array $types = ['data'];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function output(Tree $tree): array
    {
        $this->tree = $tree;

        /** @var Model $model */
        foreach ($tree->models() as $model) {
            $this->generateDataClass($model);
        }

        return $this->output;
    }

    public function types(): array
    {
        return $this->types;
    }

    protected function generateDataClass(Model $model): void
    {
        $this->imports = [];

        // Load the stub
        $stub = $this->filesystem->stub('data.class.stub');

        // Add base import
        $this->addImport('Spatie\LaravelData\Data');

        // Build properties from model columns
        $properties = $this->buildProperties($model);

        $path = $this->getPath($model);
        $content = $this->populateStub($stub, $model, $properties);

        $this->create($path, $content);
        $this->output['created'][] = ['Data', $path];
    }

    protected function populateStub(string $stub, Model $model, string $properties): string
    {
        $stub = str_replace('{{ namespace }}', config('blueprint.namespace').'\\Data', $stub);
        $stub = str_replace('{{ class }}', $model->name().'Data', $stub);
        $stub = str_replace('{{ imports }}', $this->buildImports(), $stub);
        $stub = str_replace('{{ properties }}', $properties, $stub);

        return $stub;
    }

    protected function buildProperties(Model $model): string
    {
        $properties = [];
        $indentation = '        ';

        foreach ($model->columns() as $column) {
            $property = $this->buildProperty($column, $model);
            if ($property) {
                $properties[] = $property;
            }
        }

        // Add relationship properties
        if (method_exists($model, 'relationships')) {
            foreach ($model->relationships() as $type => $relationships) {
                if (is_string($relationships)) {
                    $relationships = [$relationships];
                }

                foreach ($relationships as $relationship) {
                    $property = $this->buildRelationshipProperty($relationship, $type);
                    if ($property) {
                        $properties[] = $property;
                    }
                }
            }
        }

        if (empty($properties)) {
            return '';
        }

        return implode(",\n{$indentation}", $properties).',';
    }

    protected function buildProperty(Column $column, Model $model): ?string
    {
        $name = $column->name();

        // Skip id, timestamps, and foreign keys (handled as relationships)
        if (in_array($name, ['id', 'created_at', 'updated_at', 'deleted_at']) || Str::endsWith($name, '_id')) {
            return null;
        }

        $attributes = [];
        $type = $this->getPhpType($column);
        $nullable = $column->isNullable();

        // Add WithCast attribute for enums
        if ($column->dataType() === 'enum') {
            $enumClass = $this->getEnumClass($column, $model);
            if ($enumClass) {
                $this->addImport('Spatie\LaravelData\Attributes\WithCast');
                $this->addImport('Spatie\LaravelData\Casts\EnumCast');
                $this->addImport($enumClass);

                $enumName = class_basename($enumClass);
                $type = $enumName;
                $attributes[] = "#[WithCast(EnumCast::class, type: {$enumName}::class)]";
            }
        }

        // Add WithCast attribute for dates
        if (in_array($column->dataType(), ['date', 'datetime', 'timestamp'])) {
            $this->addImport('Carbon\CarbonImmutable');
            $this->addImport('Spatie\LaravelData\Attributes\WithCast');
            $this->addImport('Spatie\LaravelData\Casts\DateTimeInterfaceCast');

            $type = 'CarbonImmutable';
            $attributes[] = '#[WithCast(DateTimeInterfaceCast::class)]';
        }

        // Build the property line
        $propertyLine = '';
        if (! empty($attributes)) {
            $propertyLine = implode("\n        ", $attributes)."\n        ";
        }

        $propertyLine .= 'public '.($nullable ? '?' : '').$type.' $'.$name;

        return $propertyLine;
    }

    protected function buildRelationshipProperty(string $relationship, string $type): ?string
    {
        $relatedModel = Str::studly(Str::singular($relationship));
        $dataClass = $relatedModel.'Data';

        // Check if the related Data class would exist
        $this->addImport(config('blueprint.namespace')."\\Data\\{$dataClass}");

        if (in_array($type, ['hasMany', 'belongsToMany', 'morphMany', 'morphToMany'])) {
            // Collection of related items - keep plural form
            $name = Str::camel(Str::plural($relationship));

            return "/** @var array<{$dataClass}> */\n        public array \${$name}";
        } elseif (in_array($type, ['belongsTo', 'hasOne', 'morphTo', 'morphOne'])) {
            // Single related item - keep singular form
            $name = Str::camel(Str::singular($relationship));

            return "public {$dataClass} \${$name}";
        }

        return null;
    }

    protected function getPhpType(Column $column): string
    {
        return match ($column->dataType()) {
            'id', 'integer', 'unsignedInteger', 'bigInteger', 'unsignedBigInteger', 'tinyInteger', 'unsignedTinyInteger', 'smallInteger', 'unsignedSmallInteger', 'mediumInteger', 'unsignedMediumInteger' => 'int',
            'decimal', 'float', 'double' => 'float',
            'boolean' => 'bool',
            'json', 'jsonb' => 'array',
            default => 'string',
        };
    }

    protected function getEnumClass(Column $column, Model $model): ?string
    {
        // Try to infer enum class name from column name
        $enumName = Str::studly($column->name());

        // Common patterns
        if (Str::endsWith($enumName, 'Status')) {
            $enumClass = config('blueprint.namespace')."\\Enums\\{$enumName}";
        } elseif (Str::endsWith($enumName, 'Type')) {
            $enumClass = config('blueprint.namespace')."\\Enums\\{$enumName}";
        } else {
            $enumClass = config('blueprint.namespace')."\\Enums\\{$model->name()}{$enumName}";
        }

        return $enumClass;
    }

    protected function addImport(string $class): void
    {
        if (! in_array($class, $this->imports)) {
            $this->imports[] = $class;
        }
    }

    protected function buildImports(): string
    {
        if (empty($this->imports)) {
            return '';
        }

        sort($this->imports);

        return implode(PHP_EOL, array_map(fn ($import) => "use {$import};", $this->imports));
    }

    protected function getPath(Model $model): string
    {
        $path = sprintf(
            '%s/Data/%sData.php',
            Blueprint::appPath(),
            $model->name()
        );

        return $path;
    }

    protected function create(string $path, string $content): void
    {
        if (! $this->filesystem->exists(dirname($path))) {
            $this->filesystem->makeDirectory(dirname($path), 0755, true);
        }

        $this->filesystem->put($path, $content);
    }
}
