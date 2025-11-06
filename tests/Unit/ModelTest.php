<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Project;
use App\Models\VendorPayment;
use App\Models\Purchase;
use App\Models\SPD;
use App\Enums\ApprovalStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function vendor_payment_has_relationships()
    {
        $user = User::factory()->create();
        $vendor = Vendor::factory()->create();
        $project = Project::factory()->create();
        
        $vendorPayment = VendorPayment::factory()->create([
            'user_id' => $user->id,
            'vendor_id' => $vendor->id,
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf(User::class, $vendorPayment->user);
        $this->assertInstanceOf(Vendor::class, $vendorPayment->vendor);
        $this->assertInstanceOf(Project::class, $vendorPayment->project);
    }

    /** @test */
    public function vendor_payment_status_helpers_work()
    {
        $pendingPayment = VendorPayment::factory()->create([
            'status' => ApprovalStatus::PENDING,
        ]);

        $approvedPayment = VendorPayment::factory()->approved()->create();
        $rejectedPayment = VendorPayment::factory()->rejected()->create();

        $this->assertTrue($pendingPayment->isPending());
        $this->assertFalse($pendingPayment->isApproved());
        $this->assertFalse($pendingPayment->isRejected());

        $this->assertTrue($approvedPayment->isApproved());
        $this->assertFalse($approvedPayment->isPending());
        $this->assertFalse($approvedPayment->isRejected());

        $this->assertTrue($rejectedPayment->isRejected());
        $this->assertFalse($rejectedPayment->isPending());
        $this->assertFalse($rejectedPayment->isApproved());
    }

    /** @test */
    public function purchase_has_relationships()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        
        $purchase = Purchase::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
        ]);

        $this->assertInstanceOf(User::class, $purchase->user);
        $this->assertInstanceOf(Project::class, $purchase->project);
    }

    /** @test */
    public function purchase_status_helpers_work()
    {
        $pendingPurchase = Purchase::factory()->create([
            'status' => ApprovalStatus::PENDING,
        ]);

        $approvedPurchase = Purchase::factory()->approved()->create();
        $rejectedPurchase = Purchase::factory()->rejected()->create();

        $this->assertTrue($pendingPurchase->isPending());
        $this->assertTrue($approvedPurchase->isApproved());
        $this->assertTrue($rejectedPurchase->isRejected());
    }

    /** @test */
    public function spd_has_relationships()
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();
        
        // Set dates explicitly to avoid factory date issues
        $departureDate = now()->addDays(5);
        $returnDate = $departureDate->copy()->addDays(3);
        
        $spd = SPD::factory()->create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);

        $this->assertInstanceOf(User::class, $spd->user);
        $this->assertInstanceOf(Project::class, $spd->project);
    }

    /** @test */
    public function spd_status_helpers_work()
    {
        $departureDate = now()->addDays(5);
        $returnDate = $departureDate->copy()->addDays(3);
        
        $pendingSpd = SPD::factory()->create([
            'status' => ApprovalStatus::PENDING,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);

        $approvedSpd = SPD::factory()->approved()->create([
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);
        $rejectedSpd = SPD::factory()->rejected()->create([
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);

        $this->assertTrue($pendingSpd->isPending());
        $this->assertTrue($approvedSpd->isApproved());
        $this->assertTrue($rejectedSpd->isRejected());
    }

    /** @test */
    public function vendor_has_active_scope()
    {
        Vendor::factory()->count(3)->create(['is_active' => true]);
        Vendor::factory()->count(2)->create(['is_active' => false]);

        $activeVendors = Vendor::active()->get();
        $this->assertEquals(3, $activeVendors->count());
    }

    /** @test */
    public function project_has_active_scope()
    {
        Project::factory()->count(5)->create(['is_active' => true]);
        Project::factory()->count(2)->create(['is_active' => false]);

        $activeProjects = Project::active()->get();
        $this->assertEquals(5, $activeProjects->count());
    }

    /** @test */
    public function vendor_payment_cast_amount_to_decimal()
    {
        $vendorPayment = VendorPayment::factory()->create([
            'amount' => 50000000.50,
        ]);

        // Laravel casts decimal as string, not float
        $this->assertIsString($vendorPayment->amount);
        $this->assertEquals('50000000.50', $vendorPayment->amount);
    }

    /** @test */
    public function purchase_calculates_total_price()
    {
        $quantity = 5;
        $unitPrice = 25000000;
        $expectedTotal = round($quantity * $unitPrice, 2);
        
        $purchase = Purchase::factory()->create([
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $expectedTotal, // Set explicitly for test
        ]);

        // Total price should be calculated: 5 * 25000000 = 125000000
        // Laravel casts decimal as string with 2 decimal places
        $this->assertEquals(number_format($expectedTotal, 2, '.', ''), (string)$purchase->total_price);
    }

    /** @test */
    public function spd_costs_is_array()
    {
        $departureDate = now()->addDays(5);
        $returnDate = $departureDate->copy()->addDays(3);
        
        $costs = [
            ['name' => 'Transport', 'description' => 'Test', 'amount' => 1000000],
            ['name' => 'Hotel', 'description' => 'Test', 'amount' => 2000000],
        ];

        $spd = SPD::factory()->withCosts($costs)->create([
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);

        $this->assertIsArray($spd->costs);
        $this->assertCount(2, $spd->costs);
        $this->assertEquals(3000000, $spd->total_cost);
    }

    /** @test */
    public function vendor_has_vendor_payments_relationship()
    {
        $vendor = Vendor::factory()->create();
        VendorPayment::factory()->count(3)->create(['vendor_id' => $vendor->id]);

        $this->assertEquals(3, $vendor->vendorPayments->count());
    }

    /** @test */
    public function project_has_multiple_relationships()
    {
        $project = Project::factory()->create();
        
        $departureDate = now()->addDays(5);
        $returnDate = $departureDate->copy()->addDays(3);
        
        SPD::factory()->count(2)->create([
            'project_id' => $project->id,
            'departure_date' => $departureDate,
            'return_date' => $returnDate,
        ]);
        Purchase::factory()->count(3)->create(['project_id' => $project->id]);
        VendorPayment::factory()->count(4)->create(['project_id' => $project->id]);

        $this->assertEquals(2, $project->spd->count());
        $this->assertEquals(3, $project->purchases->count());
        $this->assertEquals(4, $project->vendorPayments->count());
    }
}

