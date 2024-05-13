<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Stubs\Handlers;

use Adata\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class TableStubHandler implements MigrationStubHandlerContract
{
    /**
     * {@inheritdoc}
     */
    public function populate(string $content, array $parameters): string
    {
        return str_replace(
            ['{{ table }}', '{{table}}'],
            $parameters['table'],
            $content
        );
    }
}
