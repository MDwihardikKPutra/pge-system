# Testing Documentation

Dokumentasi lengkap untuk automated testing di PGE System.

## Struktur Testing

### 1. Factories

Semua factories tersedia di `database/factories/`:

-   `VendorFactory.php` - Factory untuk Vendor
-   `ProjectFactory.php` - Factory untuk Project
-   `VendorPaymentFactory.php` - Factory untuk VendorPayment
-   `PurchaseFactory.php` - Factory untuk Purchase
-   `SpdFactory.php` - Factory untuk SPD

### 2. Feature Tests

Feature tests untuk testing controller dan HTTP requests:

-   `Feature/VendorPaymentTest.php` - Test untuk VendorPaymentController
-   `Feature/PurchaseTest.php` - Test untuk PurchaseController
-   `Feature/SpdTest.php` - Test untuk SpdController
-   `Feature/SeederTest.php` - Test untuk semua Seeders

### 3. Unit Tests

Unit tests untuk testing models dan services:

-   `Unit/ModelTest.php` - Test untuk semua Models
-   `Unit/PaymentServiceTest.php` - Test untuk PaymentService

## Menjalankan Tests

### Menjalankan Semua Tests

```bash
php artisan test
```

### Menjalankan Tests Specific

```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit

# Specific test file
php artisan test tests/Feature/VendorPaymentTest.php

# Specific test method
php artisan test --filter vendor_payment_validation_requires_all_required_fields
```

### Menjalankan Tests dengan Coverage

```bash
php artisan test --coverage
```

### Menjalankan Tests dengan Output Detail

```bash
php artisan test --verbose
```

## Test Coverage

### Vendor Payment Tests

-   ✅ User can view vendor payments index
-   ✅ Admin can view all vendor payments
-   ✅ User can only view own vendor payments
-   ✅ User can store vendor payment with all fields
-   ✅ User can store vendor payment with minimum fields
-   ✅ User can store vendor payment with documents
-   ✅ Validation requires all required fields
-   ✅ Validation rejects negative amount
-   ✅ Validation rejects invalid vendor/project
-   ✅ User can view single vendor payment
-   ✅ User can update own vendor payment
-   ✅ User can delete own pending vendor payment
-   ✅ User cannot delete approved vendor payment
-   ✅ Vendor payment can be filtered by status
-   ✅ Vendor payment number is generated automatically

### Purchase Tests

-   ✅ User can view purchases index
-   ✅ User can store purchase with all fields
-   ✅ Purchase total price is calculated automatically
-   ✅ Validation requires all required fields
-   ✅ Validation rejects negative quantity
-   ✅ User can update own purchase
-   ✅ Purchase can be filtered by status
-   ✅ Purchase number is generated automatically

### SPD Tests

-   ✅ User can view SPDs index
-   ✅ User can store SPD with single cost
-   ✅ User can store SPD with multiple costs
-   ✅ SPD total cost is calculated automatically
-   ✅ Validation requires all required fields
-   ✅ Validation requires at least one cost
-   ✅ Validation rejects return date before departure date
-   ✅ User can update own SPD
-   ✅ SPD number is generated automatically
-   ✅ SPD can be filtered by status

### Seeder Tests

-   ✅ Database seeder runs successfully
-   ✅ Project seeder creates projects
-   ✅ Project seeder is idempotent
-   ✅ Roles and permissions seeder creates roles
-   ✅ Full data seeder creates sample data
-   ✅ Seeder creates active projects only
-   ✅ Seeder can be run via artisan
-   ✅ Seeder updates existing projects
-   ✅ Seeder creates projects with valid dates
-   ✅ Seeder creates projects with required fields

### Model Tests

-   ✅ Vendor payment has relationships
-   ✅ Vendor payment status helpers work
-   ✅ Purchase has relationships
-   ✅ Purchase status helpers work
-   ✅ SPD has relationships
-   ✅ SPD status helpers work
-   ✅ Vendor has active scope
-   ✅ Project has active scope
-   ✅ Vendor payment cast amount to decimal
-   ✅ Purchase calculates total price
-   ✅ SPD costs is array
-   ✅ Vendor has vendor payments relationship
-   ✅ Project has multiple relationships

### Service Tests

-   ✅ Generate vendor payment number formats correctly
-   ✅ Generate vendor payment number increments sequence
-   ✅ Generate purchase number formats correctly
-   ✅ Generate purchase number increments sequence
-   ✅ Generate SPD number formats correctly
-   ✅ Generate SPD number increments sequence
-   ✅ Calculate SPD total cost sums correctly
-   ✅ Process costs from request handles array costs
-   ✅ Process costs from request handles empty costs
-   ✅ Calculate purchase total price multiplies correctly
-   ✅ Process costs from request handles multiple costs

## Test Data

### Using Factories

```php
// Create single model
$vendor = Vendor::factory()->create();

// Create multiple models
$vendors = Vendor::factory()->count(5)->create();

// Create with specific attributes
$vendorPayment = VendorPayment::factory()->create([
    'amount' => 50000000,
    'status' => ApprovalStatus::PENDING,
]);

// Create with relationships
$vendorPayment = VendorPayment::factory()
    ->for(User::factory())
    ->for(Vendor::factory())
    ->for(Project::factory())
    ->create();
```

### Factory States

```php
// Vendor
Vendor::factory()->inactive()->create();

// VendorPayment
VendorPayment::factory()->approved()->create();
VendorPayment::factory()->rejected()->create();
VendorPayment::factory()->paymentType('DP')->create();

// Purchase
Purchase::factory()->approved()->create();
Purchase::factory()->rejected()->create();
Purchase::factory()->type('Jasa')->create();
Purchase::factory()->category('IT & Teknologi')->create();

// SPD
SPD::factory()->approved()->create();
SPD::factory()->rejected()->create();
SPD::factory()->withCosts($costs)->create();
```

## Best Practices

1. **Always use RefreshDatabase trait** - Ensures clean database for each test
2. **Use factories for test data** - Don't manually create models
3. **Test both success and failure cases** - Cover edge cases
4. **Test validation** - Ensure all validation rules work
5. **Test relationships** - Verify model relationships work correctly
6. **Test business logic** - Test calculations, status changes, etc.
7. **Use descriptive test names** - Make it clear what is being tested
8. **Keep tests isolated** - Each test should be independent
9. **Mock external services** - Use fakes for notifications, storage, etc.
10. **Test authorization** - Ensure users can only access their own data

## Troubleshooting

### Tests Failing

1. Make sure database is configured correctly
2. Run migrations: `php artisan migrate`
3. Clear cache: `php artisan config:clear`
4. Run composer dump-autoload: `composer dump-autoload`

### Factory Errors

-   Make sure all factories have correct model references
-   Check that all required fields are defined
-   Verify relationships are set up correctly

### Seeder Test Errors

-   Make sure seeders are in correct order
-   Check that all dependencies exist
-   Verify database structure matches seeder expectations

## Running Tests in CI/CD

```yaml
# Example GitHub Actions workflow
- name: Run Tests
  run: |
      php artisan test --coverage
```

## Next Steps

1. ✅ Factories created
2. ✅ Feature tests created
3. ✅ Unit tests created
4. ✅ Seeder tests created
5. ⏳ Dusk browser tests (optional - requires ChromeDriver setup)
6. ⏳ Performance tests (optional)
7. ⏳ API tests (if API endpoints exist)

## Notes

-   All tests use `RefreshDatabase` trait for clean state
-   Notification and Storage are faked in tests
-   Tests cover both admin and user roles
-   All validation rules are tested
-   Number generation logic is tested
-   Calculation logic is tested
