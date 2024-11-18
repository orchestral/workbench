<?php

namespace Orchestra\Workbench\Tests\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Attributes\WithMigration;
use Orchestra\Testbench\Contracts\Config as ConfigContract;
use Orchestra\Testbench\Factories\UserFactory;
use Orchestra\Testbench\Foundation\Config;
use Orchestra\Testbench\TestCase;
use Orchestra\Workbench\WorkbenchServiceProvider;
use PHPUnit\Framework\Attributes\Test;

#[WithConfig('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF')]
#[WithConfig('database.default', 'testing')]
#[WithMigration]
class WorkbenchControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Define routes setup.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    protected function defineRoutes($router)
    {
        $router->get('/workbench', ['uses' => fn () => 'hello world']);
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

    #[Test]
    public function it_can_get_current_user_information()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench/user/web');

        $response->assertOk()->assertExactJson([
            'id' => $user->getKey(),
            'className' => \get_class($user),
        ]);
    }

    #[Test]
    public function it_can_get_current_user_information_without_authenticated_user_return_empty_array()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get('/_workbench/user/web');

        $response->assertOk()->assertExactJson([]);
    }

    #[Test]
    public function it_can_authenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get("/_workbench/login/{$user->getKey()}/web");

        $response->assertRedirect('/');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_can_authenticate_a_user_using_email()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->get("/_workbench/login/{$user->email}/web");

        $response->assertRedirect('/');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_can_deauthenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench/logout/web');

        $response->assertRedirect('/');

        $this->assertGuest('web');
    }

    #[Test]
    public function it_can_automatically_authenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->getKey(), 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')->get('/_workbench/');

        $response->assertRedirect('/workbench');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_can_automatically_authenticate_a_user_using_email()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => $user->email, 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')->get('/_workbench/');

        $response->assertRedirect('/workbench');

        $this->assertAuthenticated('web')
            ->assertAuthenticatedAs($user);
    }

    #[Test]
    public function it_can_automatically_deauthenticate_a_user()
    {
        $user = UserFactory::new()->create();

        $this->instance(ConfigContract::class, new Config([
            'workbench' => ['start' => '/workbench', 'user' => null, 'guard' => 'web'],
        ]));

        $response = $this->assertGuest('web')
            ->actingAs($user, 'web')
            ->get('/_workbench');

        $response->assertRedirect('/workbench');

        $this->assertGuest('web');
    }
}
