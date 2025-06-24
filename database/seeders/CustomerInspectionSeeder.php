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

class CustomerInspectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a random company and customer
        $company = Company::inRandomOrder()->first();
        $customer = Customer::where('company_id', $company->id)->inRandomOrder()->first();

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
