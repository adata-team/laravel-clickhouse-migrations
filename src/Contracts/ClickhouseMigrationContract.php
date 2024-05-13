<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Contracts;

interface ClickhouseMigrationContract
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void;

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void;
}
