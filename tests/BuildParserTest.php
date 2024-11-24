<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\BuildParser;
use Orchestra\Workbench\Workbench;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Workbench\Database\Seeders\DatabaseSeeder;

class BuildParserTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    #[DataProvider('buildDataProvider')]
    public function it_can_parse_build_steps($given, array $expected)
    {
        $steps = BuildParser::make(value($given));

        $this->assertSame($expected, $steps->all());
    }

    public static function buildDataProvider()
    {
        yield [
            function () {
                return Workbench::config('build');
            }, [
                'asset-publish' => [],
                'create-sqlite-db' => [],
                'migrate:refresh' => [
                    '--seed' => true,
                    '--seeder' => DatabaseSeeder::class,
                ],
            ],
        ];

        yield [
            [
                'asset-publish',
                'create-sqlite-db',
                ['migrate:refresh' => ['--seed' => true, '--seeder' => DatabaseSeeder::class]],
            ], [
                'asset-publish' => [],
                'create-sqlite-db' => [],
                'migrate:refresh' => [
                    '--seed' => true,
                    '--seeder' => DatabaseSeeder::class,
                ],
            ],
        ];

        yield [
            [
                'asset-publish',
                'workbench:build',
                'workbench-install',
                'create-sqlite-db',
            ], [
                'asset-publish' => [],
                'create-sqlite-db' => [],
            ],
        ];
    }
}
