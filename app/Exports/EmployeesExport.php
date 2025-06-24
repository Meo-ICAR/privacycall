<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EmployeesExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Employee::all();
    }

    public function headings(): array
    {
        return [
            'id', 'company_id', 'employee_number', 'first_name', 'last_name', 'email', 'phone', 'position', 'department', 'date_of_birth', 'hire_date', 'termination_date', 'salary', 'status', 'gdpr_consent_date', 'right_to_be_forgotten_requested', 'right_to_be_forgotten_date', 'data_portability_requested', 'data_portability_date', 'data_processing_purpose', 'data_retention_period', 'is_active', 'notes', 'created_at', 'updated_at'
        ];
    }
}
