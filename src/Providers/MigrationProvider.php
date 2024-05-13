<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Providers;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\ServiceProvider;
use Adata\ClickhouseMigrations\Clickhouse;
use Adata\ClickhouseMigrations\Migrations\Migrator;
use Adata\ClickhouseMigrations\Stubs\MigrationStub;
use Adata\ClickhouseMigrations\Commands\MigrateCommand;
use Adata\ClickhouseMigrations\Commands\MigrateMakeCommand;
use Adata\ClickhouseMigrations\Migrations\MigrationCreator;
use Adata\ClickhouseMigrations\Migrations\MigrationRepository;
use Adata\ClickhouseMigrations\Commands\MigrateRollbackCommand;
use Adata\ClickhouseMigrations\Contracts\MigrationCreatorContract;

class MigrationProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->app->singleton('clickhouse', static function ($app, array $config = []) {
            $clickhouse = new Clickhouse($config);

            return $clickhouse->getClient();
        });

        $this->app->bind(Migrator::class, static function ($app, array $parameters = []) {
            $client = $parameters['client'] ?? app('clickhouse');
            $table = $parameters['table'] ?? config('clickhouse.migrations.table');
            $filesystem = $parameters['filesystem'] ?? app(Filesystem::class);

            $repository = new MigrationRepository($table, $client);

            return new Migrator($repository, $filesystem);
        });

        $this->app->bind(MigrationCreatorContract::class, static function ($app, array $parameters = []) {
            $filesystem = $parameters['filesystem'] ?? app(Filesystem::class);
            $stub = $parameters['stub'] ?? app(MigrationStub::class);

            return new MigrationCreator($filesystem, $stub);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MigrateCommand::class,
                MigrateMakeCommand::class,
                MigrateRollbackCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../../config/clickhouse.php' => config_path('clickhouse.php'),
            ], 'config');
        }
    }
}
