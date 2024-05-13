<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Tests\Migrations;

use Illuminate\Filesystem\Filesystem;
use Adata\ClickhouseMigrations\Tests\TestCase;
use Adata\ClickhouseMigrations\Stubs\MigrationStub;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Adata\ClickhouseMigrations\Tests\assets\MyCustomStubHandler;
use Adata\ClickhouseMigrations\Tests\assets\MigrationCreatorExtended;

class MigrationCreatorTest extends TestCase
{
    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function testMakeCompleteStubAndMigrateAndRollbackMigration(): void
    {
        $filesystem = new Filesystem();
        $stub = new MigrationStub($filesystem);
        $creator = new MigrationCreatorExtended($filesystem, $stub);

        $creator->create(
            $this->assetsPath('stubs/complete.stub'),
            'CreateMyMigrationTable',
            $this->dynamicPath('migrations')
        );

        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseTotal(1);
        $this->assertClickhouseContainsMigration('my_migration');
        $this->artisan('clickhouse-migrate:rollback');
        $this->assertClickhouseTotal(0);
    }

    /**
     * @return void
     * @throws FileNotFoundException
     */
    public function testMakeDynamicStubAndUseCustomHandlers(): void
    {
        $filesystem = new Filesystem();
        $stub = new MigrationStub($filesystem, [app(MyCustomStubHandler::class)]);
        $creator = new MigrationCreatorExtended($filesystem, $stub);

        $creator->create(
            $this->assetsPath('stubs/dynamic.stub'),
            'CreateMyMigration2Table',
            $this->dynamicPath('migrations'),
            ['myCustomParameter' => 'products']
        );

        $this->artisan('clickhouse-migrate');
        $this->assertClickhouseTotal(1);
        $this->assertClickhouseContainsMigration('my_migration2');
        $this->artisan('clickhouse-migrate:rollback');
        $this->assertClickhouseTotal(0);
    }
}
