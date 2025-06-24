<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CustomersImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Customer([
            'company_id' => $row['company_id'] ?? null,
            'user_id' => $row['user_id'] ?? null,
            'customer_number' => $row['customer_number'] ?? null,
            'first_name' => $row['first_name'] ?? null,
            'last_name' => $row['last_name'] ?? null,
            'email' => $row['email'] ?? null,
            'phone' => $row['phone'] ?? null,
            'address_line_1' => $row['address_line_1'] ?? null,
            'address_line_2' => $row['address_line_2'] ?? null,
            'city' => $row['city'] ?? null,
            'state' => $row['state'] ?? null,
            'postal_code' => $row['postal_code'] ?? null,
            'country' => $row['country'] ?? null,
            'date_of_birth' => $row['date_of_birth'] ?? null,
            'customer_since' => $row['customer_since'] ?? null,
            'last_purchase_date' => $row['last_purchase_date'] ?? null,
            'total_purchases' => $row['total_purchases'] ?? null,
            'gdpr_consent_date' => $row['gdpr_consent_date'] ?? null,
            'right_to_be_forgotten_requested' => $row['right_to_be_forgotten_requested'] ?? null,
            'right_to_be_forgotten_date' => $row['right_to_be_forgotten_date'] ?? null,
            'data_portability_requested' => $row['data_portability_requested'] ?? null,
            'data_portability_date' => $row['data_portability_date'] ?? null,
            'data_processing_consent' => $row['data_processing_consent'] ?? null,
            'marketing_consent' => $row['marketing_consent'] ?? null,
            'third_party_sharing_consent' => $row['third_party_sharing_consent'] ?? null,
            'data_retention_consent' => $row['data_retention_consent'] ?? null,
            'data_processing_purpose' => $row['data_processing_purpose'] ?? null,
            'data_retention_period' => $row['data_retention_period'] ?? null,
            'is_active' => $row['is_active'] ?? null,
            'notes' => $row['notes'] ?? null,
        ]);
    }
}
