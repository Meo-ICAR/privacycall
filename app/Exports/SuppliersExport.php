<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SuppliersExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Supplier::all();
    }

    public function headings(): array
    {
        return [
            'id', 'company_id', 'supplier_number', 'name', 'legal_name', 'registration_number', 'vat_number',
            'contact_person_name', 'contact_person_email', 'contact_person_phone', 'address_line_1', 'address_line_2',
            'city', 'state', 'postal_code', 'country', 'phone', 'email', 'website', 'supplier_type',
            'supplier_category', 'supplier_status', 'supplier_since', 'last_order_date', 'total_orders',
            'total_spent', 'payment_terms', 'credit_limit', 'bank_account_info', 'tax_info', 'gdpr_consent_date',
            'data_processing_consent', 'third_party_sharing_consent', 'data_retention_consent',
            'right_to_be_forgotten_requested', 'right_to_be_forgotten_date', 'data_portability_requested',
            'data_portability_date', 'data_processing_purpose', 'data_retention_period',
            'data_processing_agreement_signed', 'data_processing_agreement_date', 'is_active', 'notes', 'created_at', 'updated_at'
        ];
    }
}
