<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PaymentService;
use App\Models\VendorPayment;
use App\Models\Purchase;
use App\Models\SPD;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService();
    }

    /** @test */
    public function generate_vendor_payment_number_formats_correctly()
    {
        $number = $this->paymentService->generateVendorPaymentNumber();
        
        $this->assertStringContainsString('VP-', $number);
        $this->assertMatchesRegularExpression('/^VP-\d{4}-\d{2}-\d{4}$/', $number);
    }

    /** @test */
    public function generate_vendor_payment_number_increments_sequence()
    {
        $firstNumber = $this->paymentService->generateVendorPaymentNumber();
        
        VendorPayment::factory()->create([
            'payment_number' => $firstNumber,
            'created_at' => now(),
        ]);

        $secondNumber = $this->paymentService->generateVendorPaymentNumber();
        
        $this->assertNotEquals($firstNumber, $secondNumber);
        
        // Extract sequence numbers
        preg_match('/VP-\d{4}-\d{2}-(\d{4})/', $firstNumber, $firstMatch);
        preg_match('/VP-\d{4}-\d{2}-(\d{4})/', $secondNumber, $secondMatch);
        
        $this->assertGreaterThan((int)$firstMatch[1], (int)$secondMatch[1]);
    }

    /** @test */
    public function generate_purchase_number_formats_correctly()
    {
        $number = $this->paymentService->generatePurchaseNumber();
        
        $this->assertStringContainsString('PUR-', $number);
        $this->assertMatchesRegularExpression('/^PUR-\d{8}-\d{4}$/', $number);
    }

    /** @test */
    public function generate_purchase_number_increments_sequence()
    {
        $firstNumber = $this->paymentService->generatePurchaseNumber();
        
        Purchase::factory()->create([
            'purchase_number' => $firstNumber,
            'created_at' => now(),
        ]);

        $secondNumber = $this->paymentService->generatePurchaseNumber();
        
        $this->assertNotEquals($firstNumber, $secondNumber);
        
        // Extract sequence numbers
        preg_match('/PUR-\d{8}-(\d{4})/', $firstNumber, $firstMatch);
        preg_match('/PUR-\d{8}-(\d{4})/', $secondNumber, $secondMatch);
        
        $this->assertGreaterThan((int)$firstMatch[1], (int)$secondMatch[1]);
    }

    /** @test */
    public function generate_spd_number_formats_correctly()
    {
        $number = $this->paymentService->generateSpdNumber();
        
        $this->assertStringContainsString('SPD-', $number);
        $this->assertMatchesRegularExpression('/^SPD-\d{8}-\d{3}$/', $number);
    }

    /** @test */
    public function generate_spd_number_increments_sequence()
    {
        $firstNumber = $this->paymentService->generateSpdNumber();
        
        SPD::factory()->create([
            'spd_number' => $firstNumber,
            'created_at' => now(),
        ]);

        $secondNumber = $this->paymentService->generateSpdNumber();
        
        $this->assertNotEquals($firstNumber, $secondNumber);
        
        // Extract sequence numbers
        preg_match('/SPD-\d{8}-(\d{3})/', $firstNumber, $firstMatch);
        preg_match('/SPD-\d{8}-(\d{3})/', $secondNumber, $secondMatch);
        
        $this->assertGreaterThan((int)$firstMatch[1], (int)$secondMatch[1]);
    }

    /** @test */
    public function calculate_spd_total_cost_sums_correctly()
    {
        $transport = 5000000;
        $accommodation = 2000000;
        $meal = 1000000;
        $other = 500000;

        $total = $this->paymentService->calculateSpdTotalCost(
            $transport,
            $accommodation,
            $meal,
            $other
        );

        $this->assertEquals(8500000, $total);
    }

    /** @test */
    public function process_costs_from_request_handles_array_costs()
    {
        $requestData = [
            'cost_name' => ['Transport', 'Hotel', 'Makan'],
            'cost_description' => ['Tiket', 'Hotel 3 malam', 'Makan 4 hari'],
            'cost_amount' => [5000000, 2000000, 1000000],
        ];

        $result = $this->paymentService->processCostsFromRequest($requestData);

        $this->assertIsArray($result);
        $this->assertEquals(5000000, $result['transport_cost']);
        $this->assertEquals(2000000, $result['accommodation_cost']);
        $this->assertEquals(1000000, $result['meal_cost']);
        $this->assertEquals(8000000, $result['total_cost']);
        $this->assertIsArray($result['costs']);
        $this->assertCount(3, $result['costs']);
    }

    /** @test */
    public function process_costs_from_request_handles_empty_costs()
    {
        $requestData = [
            'cost_name' => [],
            'cost_description' => [],
            'cost_amount' => [],
        ];

        $result = $this->paymentService->processCostsFromRequest($requestData);

        $this->assertEquals(0, $result['transport_cost']);
        $this->assertEquals(0, $result['accommodation_cost']);
        $this->assertEquals(0, $result['meal_cost']);
        $this->assertEquals(0, $result['total_cost']);
        $this->assertIsArray($result['costs']);
        $this->assertCount(0, $result['costs']);
    }

    /** @test */
    public function calculate_purchase_total_price_multiplies_correctly()
    {
        $unitPrice = 25000000;
        $quantity = 5;

        $total = $this->paymentService->calculatePurchaseTotalPrice($unitPrice, $quantity);

        $this->assertEquals(125000000, $total);
    }

    /** @test */
    public function process_costs_from_request_handles_multiple_costs()
    {
        $requestData = [
            'cost_name' => ['Transport', 'Hotel', 'Makan', 'Lainnya', 'Parkir'],
            'cost_description' => ['Tiket', 'Hotel', 'Makan', 'Lainnya', 'Parkir'],
            'cost_amount' => [5000000, 2000000, 1000000, 500000, 200000],
        ];

        $result = $this->paymentService->processCostsFromRequest($requestData);

        $this->assertEquals(5000000, $result['transport_cost']);
        $this->assertEquals(2000000, $result['accommodation_cost']);
        $this->assertEquals(1000000, $result['meal_cost']);
        $this->assertEquals(700000, $result['other_cost']); // 500000 + 200000
        $this->assertEquals(8700000, $result['total_cost']);
        $this->assertCount(5, $result['costs']);
    }
}

