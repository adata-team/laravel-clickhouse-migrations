<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Stubs\Handlers;

use Adata\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class DictionaryStubHandler implements MigrationStubHandlerContract
{
    /**
     * {@inheritdoc}
     */
    public function populate(string $content, array $parameters): string
    {
        return str_replace(
            ['{{ dictionary }}', '{{dictionary}}'],
            $parameters['dictionary'],
            $content
        );
    }
}
