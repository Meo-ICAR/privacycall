<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CustomerInspection;
use App\Models\Employee;
use App\Models\Company;
use App\Models\Holding;
use App\Models\Customer;
use Illuminate\Support\Arr;
use App\Models\Supplier;
use App\Models\Document;

class CustomerInspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a random company and customer
        $company = Company::inRandomOrder()->first();
        if (!$company) {
            $company = Company::factory()->create();
        }

        $customer = Customer::where('company_id', $company->id)->inRandomOrder()->first();
        if (!$customer) {
            $customer = Customer::factory()->create(['company_id' => $company->id]);
        }

        // Ensure at least one employee exists
        $employees = Employee::where('company_id', $company->id)->get();
        if ($employees->isEmpty()) {
            $employees = Employee::factory()->count(3)->create(['company_id' => $company->id]);
        }

        // Ensure at least one eligible supplier exists
        $suppliers = Supplier::where('company_id', $company->id)
            ->where('supplier_type', '!=', 'Individual')->get();
        if ($suppliers->isEmpty()) {
            $suppliers = Supplier::factory()->count(2)->create([
                'company_id' => $company->id,
                'supplier_type' => 'goods', // or 'services', 'both'
            ]);
        }

        // Fetch all documents related to the company
        $companyId = $company->id;
        $companyDocIds = Document::where('documentable_type', Company::class)
            ->where('documentable_id', $companyId)
            ->pluck('id');
        $customerIds = Customer::where('company_id', $companyId)->pluck('id');
        $customerDocIds = Document::where('documentable_type', Customer::class)
            ->whereIn('documentable_id', $customerIds)
            ->pluck('id');
        $employeeIds = Employee::where('company_id', $companyId)->pluck('id');
        $employeeDocIds = Document::where('documentable_type', Employee::class)
            ->whereIn('documentable_id', $employeeIds)
            ->pluck('id');
        $supplierIds = Supplier::where('company_id', $companyId)->pluck('id');
        $supplierDocIds = Document::where('documentable_type', Supplier::class)
            ->whereIn('documentable_id', $supplierIds)
            ->pluck('id');
        $allDocIds = $companyDocIds
            ->merge($customerDocIds)
            ->merge($employeeDocIds)
            ->merge($supplierDocIds)
            ->unique();
        $documents = Document::whereIn('id', $allDocIds)->get();

        // Create a customer inspection
        $inspection = CustomerInspection::create([
            'company_id' => $company->id,
            'customer_id' => $customer->id,
            'inspection_date' => now(),
            'notes' => 'Seeded inspection',
            'status' => 'pending',
        ]);

        // Get all employees of the same company
        $employees = Employee::where('company_id', $company->id)->get();

        // Exclude some employees (randomly exclude 1 if more than 2)
        $excludedEmployeeIds = $employees->count() > 2 ? Arr::random($employees->pluck('id')->toArray(), 1) : [];
        $employees = $employees->whereNotIn('id', $excludedEmployeeIds);

        // Add others from the same holding (not in the same company)
        $additionalEmployees = collect();
        if ($company->holding_id) {
            $additionalEmployees = Employee::whereHas('company', function ($q) use ($company) {
                $q->where('holding_id', $company->holding_id)->where('id', '!=', $company->id);
            })->take(2)->get();
        }

        // Merge and remove duplicates
        $allEmployees = $employees->merge($additionalEmployees)->unique('id');

        // Prepare attach data with custom position and hire_date
        $attachData = [];
        foreach ($allEmployees as $employee) {
            $attachData[$employee->id] = [
                'position' => $employee->position . ' (inspected)',
                'hire_date' => $employee->hire_date ? $employee->hire_date->subYears(rand(0,2)) : now()->subYears(rand(1,5)),
            ];
        }

        // Attach to the inspection
        $inspection->employees()->sync($attachData);
    }
}
