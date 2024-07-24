<?php

namespace Orchestra\Workbench\Tests\Recipes;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Mockery as m;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Recipes\AssetPublishCommand;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Console\Output\OutputInterface;

class AssetPublishCommandTest extends TestCase
{
    use WithWorkbench;

    #[Test]
    public function it_can_resolve_correct_command()
    {
        $kernel = m::mock(ConsoleKernel::class);
        $output = m::mock(OutputInterface::class);

        $command = new AssetPublishCommand;

        $kernel->shouldReceive('call')
            ->once()
            ->with(
                'vendor:publish',
                ['--tag' => ['workbench-assets', 'laravel-assets'], '--force' => true],
                m::type(OutputInterface::class)
            )->andReturnNull();

        $command->handle($kernel, $output);
    }
}
