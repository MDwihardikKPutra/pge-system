# Project Searchable Select - Dokumentasi

## Overview

Fitur **Project Searchable Select** menggantikan dropdown project tradisional dengan searchable input yang lebih efisien untuk menangani banyak project. Fitur ini menggunakan Alpine.js untuk interaktifitas dan API endpoint untuk pencarian real-time.

## Fitur Utama

### 1. Partial Search

-   Pencarian real-time dengan debounce 300ms
-   Pencarian berdasarkan nama project atau kode project
-   Auto-load initial projects jika query kosong
-   Loading indicator saat pencarian

### 2. Auto-fetch Project Info

-   Auto-fetch project info by ID untuk edit mode
-   Display format: `Nama Project (KODE)`
-   Clear selection button untuk reset

### 3. Konsistensi Implementasi

-   Digunakan di semua form: Work Plans, Work Realizations, Payment modals
-   Reusable Alpine.js component
-   Konsisten di create, edit, dan modal forms

## Komponen Teknis

### 1. Alpine.js Component

**File**: `resources/views/layouts/app.blade.php`

**Component Name**: `projectSearchableSelect`

**Parameter**:

-   `initialDisplay` (string|null): Nilai display awal (format: "Nama (KODE)")
-   `initialId` (int|null): ID project yang sudah dipilih

**Methods**:

-   `init()`: Initialize component, set initial values, fetch jika perlu
-   `searchProjects()`: Search projects via API dengan debounce
-   `fetchProjectById(projectId)`: Fetch specific project by ID
-   `selectProject(project)`: Select project dari dropdown
-   `clearSelection()`: Clear selection dan reload projects
-   `destroy()`: Cleanup timeout dan abort fetch requests

### 2. API Endpoint

**Route**: `GET /projects/search`

**File**: `app/Http/Controllers/ProjectController.php`

**Parameters**:

-   `q` (string, optional): Search query (nama atau kode)
-   `id` (int, optional): Project ID untuk fetch specific project
-   `limit` (int, optional, default: 20, max: 100): Limit hasil

**Response**:

```json
{
    "projects": [
        {
            "id": 1,
            "name": "Nama Project",
            "code": "KODE",
            "display": "Nama Project (KODE)"
        }
    ]
}
```

**Validation**:

-   `id` harus numeric (prevent SQL injection)
-   `limit` di-capped maksimal 100
-   Query di-escape dengan proper encoding

### 3. Blade Component (Optional)

**File**: `resources/views/components/project-searchable-select.blade.php`

**Props**:

-   `name` (string, default: 'project_id'): Name untuk hidden input
-   `value` (int|null): Selected project ID
-   `required` (bool, default: false): Required field
-   `placeholder` (string, default: 'Cari atau pilih project...')
-   `class` (string, default: ''): Additional CSS classes

**Usage**:

```blade
<x-project-searchable-select
    name="project_id"
    :value="$workPlan->project_id"
    required
/>
```

## Implementasi di Form

### Work Plans

**Files**:

-   `resources/views/work/work-plans/create.blade.php`
-   `resources/views/work/work-plans/edit.blade.php`
-   `resources/views/work/work-plans/modal.blade.php`

**Implementation**:

```blade
<div x-data="projectSearchableSelect(
    (currentPlan?.project?.name ? (currentPlan.project.name + ' (' + currentPlan.project.code + ')') : null),
    currentPlan?.project_id || null
)">
    <input type="hidden" name="project_id" :value="selectedId">
    <!-- Search input dan dropdown -->
</div>
```

### Work Realizations

**Files**:

-   `resources/views/work/work-realizations/create.blade.php`
-   `resources/views/work/work-realizations/edit.blade.php`
-   `resources/views/work/work-realizations/modal.blade.php`

**Implementation**: Similar dengan Work Plans

### Payment Modals

**Files**:

-   `resources/views/payment/spd/modal.blade.php`
-   `resources/views/payment/purchase/modal.blade.php`
-   `resources/views/payment/vendor-payment/modal.blade.php`

**Implementation**:

```blade
<div x-data="projectSearchableSelect(null, formData.project_id || null)"
    x-init="
        let isInitialized = false;
        $watch('selectedId', value => {
            if (isInitialized && formData.project_id !== value) {
                formData.project_id = value;
            }
        });
        $watch('formData.project_id', value => {
            if (!isInitialized) {
                isInitialized = true;
                return;
            }
            if (value && selectedId !== value) {
                fetchProjectById(value);
            } else if (!value && selectedId) {
                clearSelection();
            }
        });
    ">
    <!-- Search input dan dropdown -->
</div>
```

**Note**: Payment modals menggunakan `isInitialized` flag untuk mencegah infinite loop antara `selectedId` dan `formData.project_id` watchers.

## Security Features

### 1. SQL Injection Prevention

-   Validasi `projectId` dengan `is_numeric()` sebelum query
-   Cast ke `(int)` untuk memastikan type safety
-   Query menggunakan parameterized queries (Laravel Eloquent)

### 2. Input Validation

-   Limit parameter di-capped maksimal 100
-   URL encoding untuk semua parameters
-   Response validation sebelum parsing JSON

### 3. Error Handling

-   Proper error handling untuk fetch requests
-   AbortError tidak di-log sebagai error (expected behavior)
-   Graceful degradation jika API error

## Performance Optimizations

### 1. Debouncing

-   300ms debounce untuk mengurangi API calls
-   Clear previous timeout sebelum membuat yang baru

### 2. Request Cancellation

-   AbortController untuk cancel pending requests
-   Cleanup saat component destroy
-   Prevent memory leaks

### 3. Response Validation

-   Check `response.ok` sebelum parse JSON
-   Validate data structure sebelum access properties
-   Fallback ke empty array jika data invalid

### 4. Memory Management

-   Cleanup timeout di `destroy()` method
-   Abort fetch requests saat component destroy
-   Clear references untuk prevent memory leaks

## Bug Fixes

### 1. Double Binding Issue

**Problem**: `x-model` dan `:value` pada elemen yang sama menyebabkan konflik
**Fix**: Hapus `:value`, hanya gunakan `x-model="searchQuery"`

### 2. Race Condition

**Problem**: Watch handlers bisa trigger infinite loop
**Fix**: Gunakan `isInitialized` flag untuk prevent loop saat initialization

### 3. Memory Leaks

**Problem**: Timeout dan fetch requests tidak di-cleanup
**Fix**: Tambahkan `destroy()` method untuk cleanup

### 4. Missing Error Handling

**Problem**: Tidak ada validasi response API
**Fix**: Tambahkan response validation dan proper error handling

### 5. SQL Injection Vulnerability

**Problem**: `projectId` tidak di-validate
**Fix**: Tambahkan `is_numeric()` validation dan cast ke `(int)`

## Testing Checklist

-   [ ] Search projects by name
-   [ ] Search projects by code
-   [ ] Select project dari dropdown
-   [ ] Clear selection
-   [ ] Edit mode dengan existing project
-   [ ] Create mode tanpa project
-   [ ] Error handling saat API error
-   [ ] Memory cleanup saat component destroy
-   [ ] Debounce bekerja dengan benar
-   [ ] Request cancellation bekerja

## Future Improvements

1. **Caching**: Cache hasil search untuk mengurangi API calls
2. **Pagination**: Support pagination untuk hasil yang banyak
3. **Keyboard Navigation**: Arrow keys untuk navigate dropdown
4. **Accessibility**: ARIA labels dan keyboard support
5. **Custom Styling**: Props untuk customize styling
6. **Loading States**: Better loading indicators

## Related Files

### Controllers

-   `app/Http/Controllers/ProjectController.php`

### Views

-   `resources/views/layouts/app.blade.php` (Alpine.js component)
-   `resources/views/components/project-searchable-select.blade.php`
-   `resources/views/work/work-plans/*.blade.php`
-   `resources/views/work/work-realizations/*.blade.php`
-   `resources/views/payment/*/modal.blade.php`

### Routes

-   `routes/web.php` (Route: `projects.search`)

## References

-   [Alpine.js Documentation](https://alpinejs.dev/)
-   [Laravel Eloquent](https://laravel.com/docs/eloquent)
-   [Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API)
