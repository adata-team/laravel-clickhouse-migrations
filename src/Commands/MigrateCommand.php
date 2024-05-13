<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Adata\ClickhouseMigrations\Migrations\Migrator;
use Adata\ClickhouseMigrations\Concerns\MigrationPath;
use Adata\ClickhouseMigrations\Concerns\MigrationStep;
use Adata\ClickhouseMigrations\Concerns\MigrationOutput;

class MigrateCommand extends Command
{
    use ConfirmableTrait, MigrationPath, MigrationStep, MigrationOutput;

    /**
     * {@inheritdoc}
     */
    protected $signature = 'clickhouse-migrate
                {--force : Force the operation to run when in production}
                {--output : Show migrations to apply before executing}
                {--path= : Path to Clickhouse directory with migrations}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--step= : Number of migrations to rollback}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Run the ClickHouse database migrations';

    /**
     * Execute the console command.
     *
     * @param  Migrator  $migrator
     * @return void
     */
    public function handle(Migrator $migrator): void
    {
        $this->migrator = $migrator;

        $migrator->ensureTableExists()
            ->setOutput($this->getOutput())
            ->setMigrationPath($this->getMigrationPath());

        $migrations = $migrator->getMigrationsUp();

        if (! $this->outputMigrations($migrations) || ! $this->confirmToProceed()) {
            return;
        }

        $migrator->runUp($this->getStep());
    }
}
