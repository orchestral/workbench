<?php

namespace Orchestra\Workbench\Tests\Http\Middleware;

use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\WorkbenchServiceProvider;

class CatchDefaultRouteTest extends TestCase
{
    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set([
            'app.key' => 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF',
            'database.default' => 'testing',
        ]);
    }

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('/workbench', ['uses' => function () {
            return 'hello world';
        }]);
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<\Illuminate\Support\ServiceProvider>>
     */
    protected function getPackageProviders($app)
    {
        return [
            WorkbenchServiceProvider::class,
        ];
    }

    /**
     * @test
     */
    public function it_would_redirect_to_workbench_path()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->getKey(), 'guard' => 'web'],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertRedirect('/_workbench');
    }

    /**
     * @test
     */
    public function it_would_show_default_page()
    {
        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => true],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertOk();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => true, 'welcome' => true],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertOk();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => false, 'welcome' => true],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertOk();
    }

    /**
     * @test
     */
    public function it_would_not_redirect_to_workbench_path_if_configuration_doesnt_requires_it()
    {
        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => false],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertNotFound();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => true, 'welcome' => false],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertNotFound();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/', 'install' => false, 'welcome' => false],
        ]));

        $this->assertGuest('web')->get('/')
            ->assertNotFound();
    }

    /**
     * @test
     */
    public function it_would_not_redirect_to_workbench_path_on_path_other_than_root()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->getKey(), 'guard' => 'web'],
        ]));

        $this->assertGuest('web')->get('/workbench')
            ->assertOk();

        $this->assertGuest('web');
    }
}
