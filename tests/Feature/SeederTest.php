<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Project;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Models\Purchase;
use App\Models\SPD;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function database_seeder_runs_successfully()
    {
        $this->seed(DatabaseSeeder::class);

        // Check that default admin user was created
        $admin = User::where('email', 'admin@pge.local')->first();
        $this->assertNotNull($admin);
        $this->assertTrue($admin->hasRole('admin'));

        // Check that default user was created
        $user = User::where('email', 'user@pge.local')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('user'));
    }

    /** @test */
    public function project_seeder_creates_projects()
    {
        $this->seed(ProjectSeeder::class);

        $projects = Project::all();
        $this->assertGreaterThan(0, $projects->count());

        // Check specific projects exist
        $this->assertDatabaseHas('projects', [
            'code' => 'PRJ-2024-001',
            'name' => 'PLTP Sarulla Expansion',
        ]);

        $this->assertDatabaseHas('projects', [
            'code' => 'PRJ-2024-008',
            'name' => 'PLTP Ulubelu Optimization',
        ]);

        $this->assertDatabaseHas('projects', [
            'code' => 'PRJ-INTERNAL-001',
            'name' => 'Internal Kantor',
        ]);
    }

    /** @test */
    public function project_seeder_is_idempotent()
    {
        // Run seeder twice
        $this->seed(ProjectSeeder::class);
        $firstCount = Project::count();

        $this->seed(ProjectSeeder::class);
        $secondCount = Project::count();

        // Should have same count (updateOrCreate)
        $this->assertEquals($firstCount, $secondCount);
    }

    /** @test */
    public function roles_and_permissions_seeder_creates_roles()
    {
        $this->seed(RolesAndPermissionsSeeder::class);

        // Check that roles exist
        $this->assertTrue(\Spatie\Permission\Models\Role::where('name', 'admin')->exists());
        $this->assertTrue(\Spatie\Permission\Models\Role::where('name', 'user')->exists());
    }

    /** @test */
    public function full_data_seeder_creates_sample_data()
    {
        // First seed base data
        $this->seed([
            RolesAndPermissionsSeeder::class,
            ProjectSeeder::class,
        ]);

        // Then seed full data
        $this->seed(\Database\Seeders\FullDataSeeder::class);

        // Check that sample data was created
        $this->assertGreaterThan(0, Vendor::count());
        $this->assertGreaterThan(0, VendorPayment::count());
        $this->assertGreaterThan(0, Purchase::count());
        $this->assertGreaterThan(0, SPD::count());
    }

    /** @test */
    public function seeder_creates_active_projects_only()
    {
        $this->seed(ProjectSeeder::class);

        $projects = Project::where('is_active', false)->count();
        $this->assertEquals(0, $projects); // All seeded projects should be active
    }

    /** @test */
    public function database_seeder_can_be_run_via_artisan()
    {
        Artisan::call('db:seed', ['--class' => DatabaseSeeder::class]);

        $this->assertTrue(
            User::where('email', 'admin@pge.local')->exists()
        );
    }

    /** @test */
    public function project_seeder_updates_existing_projects()
    {
        // Create a project with same code but different name
        Project::create([
            'name' => 'Old Name',
            'code' => 'PRJ-2024-001',
            'client' => 'Test Client',
            'is_active' => true,
        ]);

        // Run seeder
        $this->seed(ProjectSeeder::class);

        // Project should be updated, not duplicated
        $project = Project::where('code', 'PRJ-2024-001')->first();
        $this->assertEquals('PLTP Sarulla Expansion', $project->name);
        $this->assertEquals(1, Project::where('code', 'PRJ-2024-001')->count());
    }

    /** @test */
    public function seeder_creates_projects_with_valid_dates()
    {
        $this->seed(ProjectSeeder::class);

        $projects = Project::all();
        $this->assertGreaterThan(0, $projects->count()); // Ensure we have projects to test
        
        foreach ($projects as $project) {
            if ($project->start_date) {
                $this->assertInstanceOf(\Carbon\Carbon::class, $project->start_date);
            }
            if ($project->end_date) {
                $this->assertInstanceOf(\Carbon\Carbon::class, $project->end_date);
            }
        }
    }

    /** @test */
    public function seeder_creates_projects_with_required_fields()
    {
        $this->seed(ProjectSeeder::class);

        $projects = Project::all();
        foreach ($projects as $project) {
            $this->assertNotNull($project->name);
            $this->assertNotNull($project->code);
            $this->assertNotNull($project->client);
            $this->assertNotNull($project->is_active);
        }
    }
}

