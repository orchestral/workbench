<?php

namespace Orchestra\Workbench\Tests\Events;

use Illuminate\Console\View\Components\Factory as ViewComponents;
use Mockery as m;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\Events\InstallStarted;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallStartedTest extends TestCase
{
    use WithWorkbench;

    /**
     * @test
     * @dataProvider basicInstallationDataProvider
     */
    public function it_can_determine_using_basic_installation(bool $hasOption, ?bool $getOption, bool $expected)
    {
        $event = new InstallStarted(
            $input = m::mock(InputInterface::class),
            m::mock(OutputInterface::class),
            m::mock(ViewComponents::class)
        );

        $input->shouldReceive('hasOption')->with('basic')->once()->andReturn($hasOption);
        $input->shouldReceive('getOption')->with('basic')->times(\is_null($getOption) ? 0 : 1)->andReturn($getOption);

        $this->assertSame($expected, $event->isBasicInstallation());
    }

    public static function basicInstallationDataProvider()
    {
        yield [true, true, true];
        yield [false, null, false];
        yield [false, null, false];
        yield [true, false, false];
    }
}
