<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\SPD;
use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class SpdTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        $this->project = Project::factory()->create();
    }

    /** @test */
    public function user_can_view_spds_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.spd.index'));

        $response->assertStatus(200);
        $response->assertViewIs('payment.spd.index');
    }

    /** @test */
    public function user_can_store_spd_with_single_cost()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Jakarta',
            'purpose' => 'Meeting dengan client untuk presentasi proposal',
            'departure_date' => now()->addDays(5)->format('Y-m-d'),
            'return_date' => now()->addDays(7)->format('Y-m-d'),
            'cost_name' => ['Transport'],
            'cost_description' => ['Tiket pesawat PP Jakarta-Bandung'],
            'cost_amount' => [3000000],
            'notes' => 'Menginap di hotel selama 2 malam',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $response->assertRedirect(route('user.spd.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('spd', [
            'project_id' => $this->project->id,
            'destination' => 'Jakarta',
            'total_cost' => 3000000,
            'status' => ApprovalStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function user_can_store_spd_with_multiple_costs()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Surabaya',
            'purpose' => 'Site visit dan koordinasi dengan vendor',
            'departure_date' => now()->addDays(10)->format('Y-m-d'),
            'return_date' => now()->addDays(13)->format('Y-m-d'),
            'cost_name' => ['Transport', 'Hotel', 'Makan', 'Lainnya'],
            'cost_description' => [
                'Tiket pesawat PP + transport lokal',
                'Hotel 3 malam @ Rp 500.000/malam',
                'Uang makan 4 hari @ Rp 200.000/hari',
                'Parkir dan tol',
            ],
            'cost_amount' => [5000000, 1500000, 800000, 500000],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $response->assertRedirect(route('user.spd.index'));
        
        $spd = SPD::where('destination', 'Surabaya')->first();
        $this->assertNotNull($spd);
        $this->assertEquals(7800000, $spd->total_cost); // 5000000 + 1500000 + 800000 + 500000
        $this->assertIsArray($spd->costs);
        $this->assertCount(4, $spd->costs);
    }

    /** @test */
    public function spd_total_cost_is_calculated_automatically()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Yogyakarta',
            'purpose' => 'Training dan workshop',
            'departure_date' => now()->addDays(15)->format('Y-m-d'),
            'return_date' => now()->addDays(20)->format('Y-m-d'),
            'cost_name' => ['Transport', 'Hotel', 'Makan', 'Training Fee'],
            'cost_description' => ['Tiket', '5 malam', '6 hari', 'Workshop fee'],
            'cost_amount' => [4000000, 3000000, 1500000, 5000000],
        ];

        $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $spd = SPD::where('destination', 'Yogyakarta')->first();
        $this->assertEquals(13500000, $spd->total_cost); // Sum of all costs
    }

    /** @test */
    public function spd_validation_requires_all_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.spd.store'), []);

        $response->assertSessionHasErrors([
            'project_id',
            'destination',
            'purpose',
            'departure_date',
            'return_date',
        ]);
    }

    /** @test */
    public function spd_validation_requires_at_least_one_cost()
    {
        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Jakarta',
            'purpose' => 'Test purpose',
            'departure_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(2)->format('Y-m-d'),
            // No costs provided
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function spd_validation_rejects_return_date_before_departure_date()
    {
        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Jakarta',
            'purpose' => 'Test purpose',
            'departure_date' => now()->addDays(5)->format('Y-m-d'),
            'return_date' => now()->format('Y-m-d'), // Before departure
            'cost_name' => ['Transport'],
            'cost_description' => ['Test'],
            'cost_amount' => [1000000],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $response->assertSessionHasErrors(['return_date']);
    }

    /** @test */
    public function user_can_update_own_spd()
    {
        $spd = SPD::factory()->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);

        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Updated Destination',
            'purpose' => 'Updated purpose',
            'departure_date' => now()->addDays(10)->format('Y-m-d'),
            'return_date' => now()->addDays(12)->format('Y-m-d'),
            'cost_name' => ['Transport', 'Hotel'],
            'cost_description' => ['Updated transport', 'Updated hotel'],
            'cost_amount' => [5000000, 2000000],
        ];

        $response = $this->actingAs($this->user)
            ->put(route('user.spd.update', $spd->id), $data);

        $response->assertRedirect(route('user.spd.index'));
        
        $this->assertDatabaseHas('spd', [
            'id' => $spd->id,
            'destination' => 'Updated Destination',
            'total_cost' => 7000000, // 5000000 + 2000000
        ]);
    }

    /** @test */
    public function spd_number_is_generated_automatically()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'destination' => 'Bandung',
            'purpose' => 'Test purpose',
            'departure_date' => now()->format('Y-m-d'),
            'return_date' => now()->addDays(1)->format('Y-m-d'),
            'cost_name' => ['Transport'],
            'cost_description' => ['Test'],
            'cost_amount' => [500000],
        ];

        $this->actingAs($this->user)
            ->post(route('user.spd.store'), $data);

        $spd = SPD::where('destination', 'Bandung')->first();
        $this->assertNotNull($spd->spd_number);
        $this->assertStringContainsString('SPD-', $spd->spd_number);
    }

    /** @test */
    public function spd_can_be_filtered_by_status()
    {
        SPD::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);
        SPD::factory()->approved()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.spd.index', ['status' => 'rejected']));

        $response->assertStatus(200);
        $spds = $response->viewData('spds');
        $this->assertEquals(0, $spds->total()); // No rejected SPDs
    }
}

