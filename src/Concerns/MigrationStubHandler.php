<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Concerns;

use Adata\ClickhouseMigrations\Stubs\Handlers\TableStubHandler;
use Adata\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

trait MigrationStubHandler
{
    /**
     * @return MigrationStubHandlerContract[]
     */
    protected function getStubHandlers(): array
    {
        return array_merge(
            $this->getDefaultHandlers(),
            $this->getConfigGlobalHandlers(),
            $this->getOptionHandlers()
        );
    }

    /**
     * @return MigrationStubHandlerContract[]
     */
    protected function getDefaultHandlers(): array
    {
        $classes = [];

        if ($this->hasOption('table') && $this->option('table')) {
            $classes[] = TableStubHandler::class;
        }

        return $this->makeHandlers($classes);
    }

    /**
     * @return MigrationStubHandlerContract[]
     */
    protected function getOptionHandlers(): array
    {
        $classes = (array) $this->option('stub.handler');

        return $this->makeHandlers($classes);
    }

    /**
     * @return MigrationStubHandlerContract[]
     */
    protected function getConfigGlobalHandlers(): array
    {
        $classes = config('clickhouse.handlers.global', []);

        return $this->makeHandlers($classes);
    }

    /**
     * @param  array  $classes
     * @return MigrationStubHandlerContract[]
     */
    private function makeHandlers(array $classes): array
    {
        return collect($classes)->map(static function ($class) {
            return app($class);
        })->all();
    }
}
