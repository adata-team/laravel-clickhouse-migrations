<?php

return [

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can configure a connection to connect to the ClickHouse
    | database and specify additional configurations.
    |
    */

    'config' => [
        'host' => env('CLICKHOUSE_HOST', 'localhost'),
        'port' => env('CLICKHOUSE_PORT', 8123),
        'username' => env('CLICKHOUSE_USER', 'default'),
        'password' => env('CLICKHOUSE_PASSWORD', ''),
        'cluster' => [
            'enabled' => env('CLICKHOUSE_CLUSTER_ENABLED', false),
            'name' => env('CLICKHOUSE_CLUSTER_NAME', 'default'),
            'zookeeper_path' => env('CLICKHOUSE_CLUSTER_ZOOKEEPER_PATH', '/clickhouse/tables'),
            'shard' => env('CLICKHOUSE_CLUSTER_SHARD', '{shard}'),
            'replica' => env('CLICKHOUSE_CLUSTER_REPLICA', '{replica}'),
        ],
        'options' => [
            'database' => env('CLICKHOUSE_DATABASE', 'default'),
            'timeout' => 1,
            'connectTimeOut' => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Migrations
    |--------------------------------------------------------------------------
    |
    | ClickHouse settings for working with migrations.
    |
    */

    'migrations' => [
        'table' => env('CLICKHOUSE_MIGRATION_TABLE', 'migrations'),
        'path' => database_path('clickhouse-migrations'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Stubs
    |--------------------------------------------------------------------------
    |
    | You can prepare various stub files in order to quickly create
    | migrations on the prepared code.
    |
    */

    'stubs' => [
        //'default' => base_path('stubs/file.stub),
    ],

    /*
    |--------------------------------------------------------------------------
    | ClickHouse Handlers
    |--------------------------------------------------------------------------
    |
    | Global handlers that apply to every stub created. You can also pass the
    | handler through the artisan command.
    |
    */

    'handlers' => [
        'global' => [
            //'App\Clickhouse\Handlers\MyHandler',
        ],
    ],
];
