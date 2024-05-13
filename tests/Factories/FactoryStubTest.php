<?php

declare(strict_types=1);

namespace Adata\ClickhouseMigrations\Tests\Factories;

use Adata\ClickhouseMigrations\Tests\TestCase;
use Adata\ClickhouseMigrations\Factories\FactoryStub;
use Adata\ClickhouseMigrations\Exceptions\ClickhouseStubException;

class FactoryStubTest extends TestCase
{
    /**
     * @return void
     * @throws ClickhouseStubException
     */
    public function testMakeExistsStub(): void
    {
        $stubs = FactoryStub::getStubs();

        $stubFile = FactoryStub::make(key($stubs));

        self::assertStringContainsString($stubs[key($stubs)], $stubFile);
    }

    /**
     * @return void
     * @throws ClickhouseStubException
     */
    public function testOverridePackageStub(): void
    {
        $stubs = FactoryStub::getStubs();

        [$existsStubKey, $existsStubValue] = [key($stubs), $stubs[key($stubs)]];
        config(['clickhouse.stubs' => [$existsStubKey => $existsStubValue.'/new/path']]);

        $stubFile = FactoryStub::make($existsStubKey);

        self::assertEquals($existsStubValue.'/new/path', $stubFile);
    }

    /**
     * @return void
     */
    public function testMakeNonExistsStub(): void
    {
        try {
            FactoryStub::make('non-exists-type');

            self::fail('ClickhouseStubException not thrown');
        } catch (\Exception $e) {
            self::assertEquals(ClickhouseStubException::class, get_class($e));
        }
    }
}
