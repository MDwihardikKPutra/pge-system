<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\VendorPayment;
use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Notification;

class VendorPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected User $admin;
    protected Vendor $vendor;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test users
        $this->user = User::factory()->create();
        $this->user->assignRole('user');
        
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
        
        // Create test data
        $this->vendor = Vendor::factory()->create();
        $this->project = Project::factory()->create();
        
        Storage::fake('public');
    }

    /** @test */
    public function user_can_view_vendor_payments_index()
    {
        $response = $this->actingAs($this->user)
            ->get(route('user.vendor-payments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('payment.vendor-payment.index');
    }

    /** @test */
    public function admin_can_view_all_vendor_payments()
    {
        // Create payments for different users
        VendorPayment::factory()->count(3)->create(['user_id' => $this->user->id]);
        VendorPayment::factory()->count(2)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.vendor-payments.index'));

        $response->assertStatus(200);
        $response->assertViewHas('vendorPayments');
    }

    /** @test */
    public function user_can_only_view_own_vendor_payments()
    {
        // Create payments for different users
        VendorPayment::factory()->count(3)->create(['user_id' => $this->user->id]);
        VendorPayment::factory()->count(2)->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->user)
            ->get(route('user.vendor-payments.index'));

        $response->assertStatus(200);
        $vendorPayments = $response->viewData('vendorPayments');
        $this->assertEquals(3, $vendorPayments->total());
    }

    /** @test */
    public function user_can_store_vendor_payment_with_all_fields()
    {
        Notification::fake();

        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'project', // Must be: project, kantor, atau lainnya
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-001',
            'po_number' => 'PO-2025-TEST-001',
            'amount' => 50000000,
            'description' => 'Pembayaran DP untuk jasa konsultasi engineering',
            'notes' => 'Mohon disetujui segera',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertRedirect(route('user.vendor-payments.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('vendor_payments', [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'invoice_number' => 'INV-2025-TEST-001',
            'amount' => 50000000,
            'status' => ApprovalStatus::PENDING->value,
        ]);
    }

    /** @test */
    public function user_can_store_vendor_payment_with_minimum_fields()
    {
        Notification::fake();

        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'kantor', // Must be: project, kantor, atau lainnya
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-002',
            'amount' => 25000000,
            'description' => 'Pembayaran termin 1',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertRedirect(route('user.vendor-payments.index'));
        
        $this->assertDatabaseHas('vendor_payments', [
            'invoice_number' => 'INV-2025-TEST-002',
            'po_number' => null,
            'notes' => null,
        ]);
    }

    /** @test */
    public function user_can_store_vendor_payment_with_documents()
    {
        Notification::fake();

        $file = UploadedFile::fake()->create('invoice.pdf', 100);

        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'lainnya', // Must be: project, kantor, atau lainnya
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-003',
            'amount' => 150000000,
            'description' => 'Pelunasan pembayaran',
            'documents' => [$file],
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertRedirect(route('user.vendor-payments.index'));
        
        $vendorPayment = VendorPayment::where('invoice_number', 'INV-2025-TEST-003')->first();
        $this->assertNotNull($vendorPayment);
        // Note: Document storage testing would require Document model setup
    }

    /** @test */
    public function vendor_payment_validation_requires_all_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), []);

        $response->assertSessionHasErrors([
            'vendor_id',
            'project_id',
            'payment_type',
            'payment_date',
            'invoice_number',
            'amount',
            'description',
        ]);
    }

    /** @test */
    public function vendor_payment_validation_rejects_negative_amount()
    {
        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'project',
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-004',
            'amount' => -1000,
            'description' => 'Test description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertSessionHasErrors(['amount']);
    }

    /** @test */
    public function vendor_payment_validation_rejects_invalid_vendor()
    {
        $data = [
            'vendor_id' => 99999, // Non-existent vendor
            'project_id' => $this->project->id,
            'payment_type' => 'DP',
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-005',
            'amount' => 50000000,
            'description' => 'Test description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertSessionHasErrors(['vendor_id']);
    }

    /** @test */
    public function vendor_payment_validation_rejects_invalid_project()
    {
        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => 99999, // Non-existent project
            'payment_type' => 'DP',
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-006',
            'amount' => 50000000,
            'description' => 'Test description',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $response->assertSessionHasErrors(['project_id']);
    }

    /** @test */
    public function user_can_view_single_vendor_payment()
    {
        $vendorPayment = VendorPayment::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.vendor-payments.show', $vendorPayment->id));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'vendorPayment' => [
                'id',
                'payment_number',
                'vendor_id',
                'project_id',
                'amount',
                'status',
            ],
        ]);
    }

    /** @test */
    public function user_can_update_own_vendor_payment()
    {
        $vendorPayment = VendorPayment::factory()->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);

        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'project', // Must be: project, kantor, atau lainnya
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-UPDATED-001',
            'amount' => 75000000,
            'description' => 'Updated description',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('user.vendor-payments.update', $vendorPayment->id), $data);

        $response->assertRedirect(route('user.vendor-payments.index'));
        
        $this->assertDatabaseHas('vendor_payments', [
            'id' => $vendorPayment->id,
            'invoice_number' => 'INV-2025-UPDATED-001',
            'amount' => 75000000,
        ]);
    }

    /** @test */
    public function user_can_delete_own_pending_vendor_payment()
    {
        $vendorPayment = VendorPayment::factory()->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('user.vendor-payments.destroy', $vendorPayment->id));

        $response->assertRedirect(route('user.vendor-payments.index'));
        $this->assertSoftDeleted('vendor_payments', ['id' => $vendorPayment->id]);
    }

    /** @test */
    public function user_cannot_delete_approved_vendor_payment()
    {
        $vendorPayment = VendorPayment::factory()->approved()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('user.vendor-payments.destroy', $vendorPayment->id));

        $response->assertForbidden();
        $this->assertDatabaseHas('vendor_payments', ['id' => $vendorPayment->id]);
    }

    /** @test */
    public function vendor_payment_can_be_filtered_by_status()
    {
        VendorPayment::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'status' => ApprovalStatus::PENDING,
        ]);
        VendorPayment::factory()->approved()->count(2)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('user.vendor-payments.index', ['status' => 'pending']));

        $response->assertStatus(200);
        $vendorPayments = $response->viewData('vendorPayments');
        $this->assertEquals(3, $vendorPayments->total());
    }

    /** @test */
    public function vendor_payment_number_is_generated_automatically()
    {
        Notification::fake();

        $data = [
            'vendor_id' => $this->vendor->id,
            'project_id' => $this->project->id,
            'payment_type' => 'project', // Must be: project, kantor, atau lainnya
            'payment_date' => now()->format('Y-m-d'),
            'invoice_number' => 'INV-2025-TEST-007',
            'amount' => 50000000,
            'description' => 'Test description',
        ];

        $this->actingAs($this->user)
            ->post(route('user.vendor-payments.store'), $data);

        $vendorPayment = VendorPayment::where('invoice_number', 'INV-2025-TEST-007')->first();
        $this->assertNotNull($vendorPayment->payment_number);
        // Payment number format: VP-YYYY-MM-XXXX
        $this->assertStringContainsString('VP-', $vendorPayment->payment_number);
    }
}

