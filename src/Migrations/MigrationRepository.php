<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Migrations;

use ClickHouseDB\Client;
use ClickHouseDB\Statement;
use Illuminate\Support\Str;

class MigrationRepository
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var Client
     */
    protected $client;

    public function __construct(string $table, Client $client)
    {
        $this->table = $table;
        $this->client = $client;
    }

    /**
     * Creating a new table to store migrations.
     *
     * @return Statement
     */
    public function create(): Statement
    {
        if (config('clickhouse.config.cluster.enabled')) {
            return $this->client->write("
            CREATE TABLE IF NOT EXISTS {db}.{table} ON CLUSTER {cluster}
            (
                migration String,
                batch UInt32,
                applied_at DateTime DEFAULT NOW()
            )
            ENGINE = ReplicatedReplacingMergeTree('{zookeeper_path}/{shard}/{db}/{table}_{hash}', '{replica}')
            ORDER BY migration
        ", [
                'table' => $this->table,
                'hash' => md5(Str::random(8)),
                'shard' => config('clickhouse.config.cluster.shard'),
                'replica' => config('clickhouse.config.cluster.replica'),
                'zookeeper_path' => config('clickhouse.config.cluster.zookeeper_path'),
                'db' => config('clickhouse.config.options.database'),
                'cluster' => config('clickhouse.config.cluster.name'),
            ]);
        }

        return $this->client->write('
            CREATE TABLE IF NOT EXISTS {table} (
                migration String,
                batch UInt32,
                applied_at DateTime DEFAULT NOW()
            )
            ENGINE = ReplacingMergeTree()
            ORDER BY migration
        ', [
            'table' => $this->table,
        ]);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        $rows = $this->client->select('SELECT migration FROM {table}', [
            'table' => $this->table,
        ])->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * Get latest accepted migrations.
     *
     * @return array
     */
    public function latest(): array
    {
        $rows = $this->client->select('
            SELECT migration
            FROM {table}
            ORDER BY batch DESC, migration DESC
        ', [
            'table' => $this->table,
        ])->rows();

        return collect($rows)->pluck('migration')->all();
    }

    /**
     * @return int
     */
    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        return $this->client
            ->select('SELECT MAX(batch) AS batch FROM {table}', ['table' => $this->table])
            ->fetchOne('batch');
    }

    /**
     * @param  string  $migration
     * @param  int  $batch
     * @return Statement
     */
    public function add(string $migration, int $batch): Statement
    {
        return $this->client->insert($this->table, [[$migration, $batch]], ['migration', 'batch']);
    }

    /**
     * @param  string  $migration
     * @return Statement
     */
    public function delete(string $migration): Statement
    {
        return $this->client->write('ALTER TABLE {table} DELETE WHERE migration=:migration', [
            'table' => $this->table,
            'migration' => $migration,
        ]);
    }

    /**
     * @return int
     */
    public function total(): int
    {
        return (int) $this->client->select('SELECT COUNT(*) AS count FROM {table}', [
            'table' => $this->table,
        ])->fetchOne('count');
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return (bool) $this->client->select('EXISTS TABLE {table}', [
            'table' => $this->table,
        ])->fetchOne('result');
    }

    /**
     * @param  string  $migration
     * @return array|null
     */
    public function find(string $migration): ?array
    {
        return $this->client->select('SELECT * FROM {table} WHERE migration=:migration LIMIT 1', [
            'table' => $this->table,
            'migration' => $migration,
        ])->fetchOne();
    }
}
