<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\BuildParser;
use Orchestra\Workbench\Workbench;
use PHPUnit\Framework\Attributes\Test;

class BuildParserTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_parse_build_steps()
    {
        $steps = BuildParser::make(Workbench::config('build'));

        $this->assertSame([
            'asset-publish' => [],
            'create-sqlite-db' => [],
            'migrate:refresh' => [
                '--seed' => true,
                '--drop-views' => false,
            ],
        ], $steps->all());
    }
}
