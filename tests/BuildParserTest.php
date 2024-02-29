<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\BuildParser;
use Orchestra\Workbench\Workbench;

class BuildParserTest extends TestCase
{
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
