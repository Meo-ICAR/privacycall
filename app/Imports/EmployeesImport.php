<?php

namespace App\Imports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Employee([
            'company_id' => $row['company_id'] ?? null,
            'employee_number' => $row['employee_number'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'position' => $row['position'] ?? null,
            'department' => $row['department'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'hire_date' => $row['hire_date'] ?? null,
            'termination_date' => $row['termination_date'] ?? null,
            'salary' => $row['salary'] ?? null,
            'status' => $row['status'] ?? null,
            'gdpr_consent_date' => $row['gdpr_consent_date'] ?? null,
            'right_to_be_forgotten_requested' => $row['right_to_be_forgotten_requested'] ?? null,
            'right_to_be_forgotten_date' => $row['right_to_be_forgotten_date'] ?? null,
            'data_portability_requested' => $row['data_portability_requested'] ?? null,
            'data_portability_date' => $row['data_portability_date'] ?? null,
            'data_processing_purpose' => $row['data_processing_purpose'] ?? null,
            'data_retention_period' => $row['data_retention_period'] ?? null,
            'is_active' => $row['is_active'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }
}
