<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CustomersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Customer::all();
    }

    public function headings(): array
    {
        return [
            'id', 'company_id', 'user_id', 'customer_number', 'first_name', 'last_name', 'email', 'phone', 'address_line_1', 'address_line_2', 'city', 'state', 'postal_code', 'country', 'date_of_birth', 'customer_since', 'last_purchase_date', 'total_purchases', 'gdpr_consent_date', 'right_to_be_forgotten_requested', 'right_to_be_forgotten_date', 'data_portability_requested', 'data_portability_date', 'data_processing_consent', 'marketing_consent', 'third_party_sharing_consent', 'data_retention_consent', 'data_processing_purpose', 'data_retention_period', 'is_active', 'notes', 'created_at', 'updated_at'
        ];
    }
}
