<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class DiscoversTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_resolve_web_routes_from_discovers()
    {
        $this->get('/hello')
            ->assertOk();
    }

    #[Test]
    public function it_can_resolve_commands_from_discovers()
    {
        $this->artisan('workbench:inspire')->assertOk();
    }
}
