<?php

namespace Orchestra\Workbench\Tests\Integrations;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Workbench\Database\Factories\UserFactory;

use function Orchestra\Testbench\join_paths;

class WorkbenchTest extends TestCase
{
    use InteractsWithPublishedFiles;
    use RefreshDatabase;

    protected array $files = [
        'resources/views/dashboard.blade.php',
    ];

    /** @test */
    public function it_can_display_the_default_dashboard()
    {
        $user = UserFactory::new()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee('You\'re logged in!');
    }

    /**
     * @test
     *
     * @depends it_can_display_the_default_dashboard
     */
    public function it_can_override_the_configured_views()
    {
        File::put(resource_path(join_paths('views', 'dashboard.blade.php')), 'Hello World');

        $user = UserFactory::new()->create();

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertDontSeeText('You\'re logged in!')
            ->assertSee('Hello World');
    }
}
