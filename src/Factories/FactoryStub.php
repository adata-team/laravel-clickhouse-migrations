<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Factories;

use Adata\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class FactoryStub
{
    /**
     * @param  string  $type
     * @return string
     * @throws ClickhouseStubException
     */
    public static function make(string $type = 'default'): string
    {
        if ($stub = self::getStubs()[$type] ?? null) {
            return $stub;
        }

        throw new ClickhouseStubException("ClickHouse stub {$type} does not exist");
    }

    /**
     * @return array
     */
    public static function getStubs(): array
    {
        $prefix = self::stubPathPrefix();

        return array_merge([
            'default' => $prefix.'/default.stub',
            'table' => $prefix.'/table.stub',
            'table-cluster' => $prefix.'/table-cluster.stub',
            'dictionary' => $prefix.'/dictionary.stub',
            'dictionary-cluster' => $prefix.'/dictionary-cluster.stub',
        ], self::getCustomStubs());
    }

    /**
     * @return array
     */
    public static function getCustomStubs(): array
    {
        return config('clickhouse.stubs', []);
    }

    /**
     * @return string
     */
    protected static function stubPathPrefix(): string
    {
        return dirname(__DIR__, 2).'/stubs';
    }
}
