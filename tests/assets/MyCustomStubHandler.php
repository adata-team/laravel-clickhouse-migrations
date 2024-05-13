<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Tests\assets;

use Adata\ClickhouseMigrations\Contracts\MigrationStubHandlerContract;

class MyCustomStubHandler implements MigrationStubHandlerContract
{
    /**
     * {@inheritdoc}
     */
    public function populate(string $content, array $parameters): string
    {
        return str_replace('{{myCustomPlace}}', $parameters['myCustomParameter'], $content);
    }
}
