<?php

namespace Orchestra\Workbench\Tests;

use Orchestra\Workbench\StubRegistrar;
use PHPUnit\Framework\TestCase;

use function Orchestra\Testbench\join_paths;

class StubRegistrarTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider defaultStubsDataProvider
     */
    public function it_can_resolve_default_configuration(string $given, ?string $expected)
    {
        $this->assertSame($expected, (new StubRegistrar)->file($given));
    }

    public function it_can_swap_stub_file()
    {
        $registrar = new StubRegistrar;

        $registrar->swap('config', $file = realpath(join_paths(__DIR__, '..', 'testbench.yaml')));

        $this->assertSame($file, $registrar->file('config'));
        $this->assertNotSame(join_paths(__DIR__, '..', 'src', 'Console', 'stubs', 'testbench.yaml'), $registrar->file('config'));
    }

    public static function defaultStubsDataProvider()
    {
        $defaultStubFile = join_paths(__DIR__, '..', 'src', 'Console', 'stubs');

        yield ['config', realpath(join_paths($defaultStubFile, 'testbench.yaml'))];
        yield ['config.basic', realpath(join_paths($defaultStubFile, 'testbench.plain.yaml'))];
        yield ['gitignore', realpath(join_paths($defaultStubFile, 'workbench.gitignore'))];
        yield ['routes.api', realpath(join_paths($defaultStubFile, 'routes', 'api.php'))];
        yield ['routes.console', realpath(join_paths($defaultStubFile, 'routes', 'console.php'))];
        yield ['routes.web', realpath(join_paths($defaultStubFile, 'routes', 'web.php'))];
        yield ['seeders.database', realpath(join_paths($defaultStubFile, 'database', 'seeders', 'DatabaseSeeder.php'))];
    }
}
