<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SuppliersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Supplier([
            'company_id' => $row['company_id'] ?? null,
            'supplier_number' => $row['supplier_number'] ?? null,
            'name' => $row['name'] ?? null,
            'legal_name' => $row['legal_name'] ?? null,
            'registration_number' => $row['registration_number'] ?? null,
            'vat_number' => $row['vat_number'] ?? null,
            'contact_person_name' => $row['contact_person_name'] ?? null,
            'contact_person_email' => $row['contact_person_email'] ?? null,
            'contact_person_phone' => $row['contact_person_phone'] ?? null,
            'address_line_1' => $row['address_line_1'] ?? null,
            'address_line_2' => $row['address_line_2'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'country' => $row['country'] ?? null,
            'phone' => $row['phone'] ?? null,
            'email' => $row['email'] ?? null,
            'website' => $row['website'] ?? null,
            'supplier_type' => $row['supplier_type'] ?? null,
            'supplier_category' => $row['supplier_category'] ?? null,
            'supplier_status' => $row['supplier_status'] ?? null,
            'supplier_since' => $row['supplier_since'] ?? null,
            'last_order_date' => $row['last_order_date'] ?? null,
            'total_orders' => $row['total_orders'] ?? null,
            'total_spent' => $row['total_spent'] ?? null,
            'payment_terms' => $row['payment_terms'] ?? null,
            'credit_limit' => $row['credit_limit'] ?? null,
            'bank_account_info' => $row['bank_account_info'] ?? null,
            'tax_info' => $row['tax_info'] ?? null,
            'gdpr_consent_date' => $row['gdpr_consent_date'] ?? null,
            'data_processing_consent' => $row['data_processing_consent'] ?? null,
            'third_party_sharing_consent' => $row['third_party_sharing_consent'] ?? null,
            'data_retention_consent' => $row['data_retention_consent'] ?? null,
            'right_to_be_forgotten_requested' => $row['right_to_be_forgotten_requested'] ?? null,
            'right_to_be_forgotten_date' => $row['right_to_be_forgotten_date'] ?? null,
            'data_portability_requested' => $row['data_portability_requested'] ?? null,
            'data_portability_date' => $row['data_portability_date'] ?? null,
            'data_processing_purpose' => $row['data_processing_purpose'] ?? null,
            'data_retention_period' => $row['data_retention_period'] ?? null,
            'data_processing_agreement_signed' => $row['data_processing_agreement_signed'] ?? null,
            'data_processing_agreement_date' => $row['data_processing_agreement_date'] ?? null,
            'is_active' => $row['is_active'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }
}
