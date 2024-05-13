<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Tests\assets;

use Adata\ClickhouseMigrations\Migrations\MigrationCreator;

class MigrationCreatorExtended extends MigrationCreator
{
    /**
     * {@inheritdoc}
     */
    protected function getDatePrefix(): string
    {
        return '2020_01_01_000000';
    }
}
