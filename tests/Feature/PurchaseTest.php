<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Project;
use App\Models\Purchase;
use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class PurchaseTest extends TestCase
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
        
        Storage::fake('public');
    }

    /** @test */
    public function user_can_view_purchases_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.purchases.index'));

        $response->assertStatus(200);
        $response->assertViewIs('payment.purchase.index');
    }

    /** @test */
    public function user_can_store_purchase_with_all_fields()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'type' => 'barang', // Must be: barang atau jasa (lowercase)
            'category' => 'project', // Must be: project, kantor, atau lainnya (lowercase)
            'item_name' => 'Laptop Dell XPS 15 9530',
            'description' => 'Laptop Dell XPS 15 dengan Intel i7, 16GB RAM, 512GB SSD',
            'quantity' => 5,
            'unit' => 'Unit',
            'unit_price' => 25000000,
            'notes' => 'Untuk keperluan tim development',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.purchases.store'), $data);

        $response->assertRedirect(route('user.purchases.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('purchases', [
            'project_id' => $this->project->id,
            'item_name' => 'Laptop Dell XPS 15 9530',
            'quantity' => 5,
            'unit_price' => 25000000,
            'total_price' => 125000000, // 5 * 25000000
            'status' => ApprovalStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function purchase_total_price_is_calculated_automatically()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'type' => 'jasa', // Must be: barang atau jasa (lowercase)
            'category' => 'project', // Must be: project, kantor, atau lainnya (lowercase)
            'item_name' => 'Jasa Konsultasi Engineering',
            'description' => 'Konsultasi engineering untuk feasibility study',
            'quantity' => 3,
            'unit' => 'Bulan',
            'unit_price' => 50000000,
        ];

        $this->actingAs($this->user)
            ->post(route('user.purchases.store'), $data);

        $purchase = Purchase::where('item_name', 'Jasa Konsultasi Engineering')->first();
        $this->assertEquals(150000000, $purchase->total_price); // 3 * 50000000
    }

    /** @test */
    public function purchase_validation_requires_all_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.purchases.store'), []);

        $response->assertSessionHasErrors([
            'project_id',
            'type',
            'category',
            'item_name',
            'description',
            'quantity',
            'unit',
            'unit_price',
        ]);
    }

    /** @test */
    public function purchase_validation_rejects_negative_quantity()
    {
        $data = [
            'project_id' => $this->project->id,
            'type' => 'barang',
            'category' => 'project',
            'item_name' => 'Test Item',
            'description' => 'Test description',
            'quantity' => -1,
            'unit' => 'Unit',
            'unit_price' => 1000000,
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.purchases.store'), $data);

        $response->assertSessionHasErrors(['quantity']);
    }

    /** @test */
    public function user_can_update_own_purchase()
    {
        $purchase = Purchase::factory()->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);

        $data = [
            'project_id' => $this->project->id,
            'type' => 'barang', // Must be: barang atau jasa
            'category' => 'kantor', // Must be: project, kantor, atau lainnya
            'item_name' => 'Updated Item Name',
            'description' => 'Updated description',
            'quantity' => 10,
            'unit' => 'Unit',
            'unit_price' => 10000000,
        ];

        $response = $this->actingAs($this->user)
            ->put(route('user.purchases.update', $purchase->id), $data);

        $response->assertRedirect(route('user.purchases.index'));
        
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'item_name' => 'Updated Item Name',
            'total_price' => 100000000, // 10 * 10000000
        ]);
    }

    /** @test */
    public function purchase_can_be_filtered_by_status()
    {
        Purchase::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);
        Purchase::factory()->approved()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.purchases.index', ['status' => 'approved']));

        $response->assertStatus(200);
        $purchases = $response->viewData('purchases');
        $this->assertEquals(2, $purchases->total());
    }

    /** @test */
    public function purchase_number_is_generated_automatically()
    {
        Notification::fake();

        $data = [
            'project_id' => $this->project->id,
            'type' => 'barang',
            'category' => 'project',
            'item_name' => 'Test Item',
            'description' => 'Test description',
            'quantity' => 1,
            'unit' => 'Unit',
            'unit_price' => 1000000,
        ];

        $this->actingAs($this->user)
            ->post(route('user.purchases.store'), $data);

        $purchase = Purchase::where('item_name', 'Test Item')->first();
        $this->assertNotNull($purchase->purchase_number);
        $this->assertStringContainsString('PUR-', $purchase->purchase_number);
    }
}

