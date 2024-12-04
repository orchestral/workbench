<?php

namespace Orchestra\Workbench\Tests\Console\Concerns;

use Illuminate\Filesystem\Filesystem;
use Mockery as m;
use Orchestra\Testbench\Concerns\InteractsWithMockery;
use Orchestra\Workbench\Console\Concerns\InteractsWithFiles;
use PHPUnit\Framework\TestCase;

class InteractsWithFilesTest extends TestCase
{
    use InteractsWithMockery;

    /** {@inheritDoc} */
    #[\Override]
    protected function tearDown(): void
    {
        $this->tearDownTheTestEnvironmentUsingMockery();

        parent::tearDown();
    }

    /** @test */
    public function it_can_replace_contents()
    {
        $filesystem = m::mock(Filesystem::class);

        $fixture = new class($filesystem)
        {
            use InteractsWithFiles;

            public function __construct(public Filesystem $filesystem) {}

            public function replace(array|string $search, array|string $replace, string $path)
            {
                $this->replaceInFile($this->filesystem, $search, $replace, $path);
            }
        };

        $filesystem->shouldReceive('get')->with('testbench.yaml')->once()->andReturn("laravel: '@testbench'")
            ->shouldReceive('put')->with('testbench.yaml', "laravel: '@testbench-dusk'");

        $fixture->replace("laravel: '@testbench'", "laravel: '@testbench-dusk'", 'testbench.yaml');
    }
}
