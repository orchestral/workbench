<?php

namespace Orchestra\Workbench\Tests\Recipes;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Mockery as m;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Recipes\Command;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Output\OutputInterface;

class CommandTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_resolve_correct_command()
    {
        $kernel = m::mock(ConsoleKernel::class);
        $output = m::mock(OutputInterface::class);

        $command = new Command('vendor:publish', [
            '--tag' => ['laravel-assets'],
        ]);

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'vendor:publish',
                ['--tag' => ['laravel-assets']],
                m::type(OutputInterface::class)
            )->andReturnNull();

        $command->handle($kernel, $output);
    }

    #[Test]
    public function it_can_resolve_correct_command_with_callback()
    {
        $kernel = m::mock(ConsoleKernel::class);
        $output = m::mock(OutputInterface::class);

        $command = new Command(
            'vendor:publish',
            ['--tag' => ['laravel-assets']],
            fn ($o) => $this->assertSame($o, $output)
        );

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'vendor:publish',
                ['--tag' => ['laravel-assets']],
                m::type(OutputInterface::class)
            )->andReturnNull();

        $command->handle($kernel, $output);
    }
}
