<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\User;
use App\Models\ConsentRecord;
use App\Models\DataProcessingActivity;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GdprController extends Controller
{
    /**
     * Request right to be forgotten for a data subject.
     */
    public function requestRightToBeForgotten(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_type' => 'required|in:company,employee,customer,supplier,user',
                'subject_id' => 'required|integer',
                'reason' => 'required|string|max:1000',
                'contact_email' => 'required|email',
                'verification_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $subjectType = $request->subject_type;
            $subjectId = $request->subject_id;

            // Find the subject
            $subject = $this->findSubject($subjectType, $subjectId);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data subject not found'
                ], 404);
            }

            // Update the subject to mark right to be forgotten as requested
            $subject->update([
                'right_to_be_forgotten_requested' => true,
                'right_to_be_forgotten_date' => now(),
            ]);

            // Create a consent record for the withdrawal
            ConsentRecord::create([
                'company_id' => $subject->company_id,
                'consentable_type' => get_class($subject),
                'consentable_id' => $subject->id,
                'consent_type' => 'data_processing',
                'consent_status' => 'withdrawn',
                'consent_method' => 'web_form',
                'consent_date' => now(),
                'withdrawal_date' => now(),
                'consent_text' => 'Right to be forgotten requested: ' . $request->reason,
                'consent_notes' => 'Contact email: ' . $request->contact_email,
                'is_active' => true,
            ]);

            // Store verification document if provided
            if ($request->hasFile('verification_document')) {
                $path = $request->file('verification_document')->store('gdpr/verification', 'private');
                Log::info('Verification document stored: ' . $path);
            }

            DB::commit();

            Log::info("Right to be forgotten requested for {$subjectType} ID: {$subjectId}");

            return response()->json([
                'success' => true,
                'message' => 'Right to be forgotten request submitted successfully. You will be contacted within 30 days.',
                'data' => [
                    'request_id' => uniqid('RTBF-'),
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'request_date' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error requesting right to be forgotten: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing right to be forgotten request'
            ], 500);
        }
    }

    /**
     * Request data portability for a data subject.
     */
    public function requestDataPortability(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_type' => 'required|in:company,employee,customer,supplier,user',
                'subject_id' => 'required|integer',
                'format' => 'required|in:json,csv,xml,pdf',
                'contact_email' => 'required|email',
                'verification_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            $subjectType = $request->subject_type;
            $subjectId = $request->subject_id;

            // Find the subject
            $subject = $this->findSubject($subjectType, $subjectId);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data subject not found'
                ], 404);
            }

            // Update the subject to mark data portability as requested
            $subject->update([
                'data_portability_requested' => true,
                'data_portability_date' => now(),
            ]);

            // Create a consent record for the data portability request
            ConsentRecord::create([
                'company_id' => $subject->company_id,
                'consentable_type' => get_class($subject),
                'consentable_id' => $subject->id,
                'consent_type' => 'data_processing',
                'consent_status' => 'granted',
                'consent_method' => 'web_form',
                'consent_date' => now(),
                'consent_text' => 'Data portability requested in ' . $request->format . ' format',
                'consent_notes' => 'Contact email: ' . $request->contact_email,
                'is_active' => true,
            ]);

            // Store verification document if provided
            if ($request->hasFile('verification_document')) {
                $path = $request->file('verification_document')->store('gdpr/verification', 'private');
                Log::info('Verification document stored: ' . $path);
            }

            DB::commit();

            Log::info("Data portability requested for {$subjectType} ID: {$subjectId} in {$request->format} format");

            return response()->json([
                'success' => true,
                'message' => 'Data portability request submitted successfully. You will receive your data within 30 days.',
                'data' => [
                    'request_id' => uniqid('DP-'),
                    'subject_type' => $subjectType,
                    'subject_id' => $subjectId,
                    'format' => $request->format,
                    'request_date' => now(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error requesting data portability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing data portability request'
            ], 500);
        }
    }

    /**
     * Get data processing activities for a subject.
     */
    public function getDataProcessingActivities(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_type' => 'required|in:company,employee,customer,supplier,user',
                'subject_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subjectType = $request->subject_type;
            $subjectId = $request->subject_id;

            // Find the subject
            $subject = $this->findSubject($subjectType, $subjectId);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data subject not found'
                ], 404);
            }

            // Get data processing activities
            $activities = DataProcessingActivity::where('processable_type', get_class($subject))
                ->where('processable_id', $subject->id)
                ->where('is_active', true)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $activities,
                'message' => 'Data processing activities retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving data processing activities: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving data processing activities'
            ], 500);
        }
    }

    /**
     * Get consent history for a subject.
     */
    public function getConsentHistory(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_type' => 'required|in:company,employee,customer,supplier,user',
                'subject_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subjectType = $request->subject_type;
            $subjectId = $request->subject_id;

            // Find the subject
            $subject = $this->findSubject($subjectType, $subjectId);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data subject not found'
                ], 404);
            }

            // Get consent history
            $consents = ConsentRecord::where('consentable_type', get_class($subject))
                ->where('consentable_id', $subject->id)
                ->orderBy('consent_date', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $consents,
                'message' => 'Consent history retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Error retrieving consent history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving consent history'
            ], 500);
        }
    }

    /**
     * Export data for a subject in the requested format.
     */
    public function exportData(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'subject_type' => 'required|in:company,employee,customer,supplier,user',
                'subject_id' => 'required|integer',
                'format' => 'required|in:json,csv,xml',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $subjectType = $request->subject_type;
            $subjectId = $request->subject_id;
            $format = $request->format;

            // Find the subject
            $subject = $this->findSubject($subjectType, $subjectId);

            if (!$subject) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data subject not found'
                ], 404);
            }

            // Prepare data for export
            $exportData = [
                'subject_info' => $subject->toArray(),
                'consent_history' => $subject->consentRecords()->get()->toArray(),
                'data_processing_activities' => $subject->dataProcessingActivities()->get()->toArray(),
                'export_date' => now()->toISOString(),
                'export_format' => $format,
            ];

            // Generate file based on format
            $filename = "gdpr_export_{$subjectType}_{$subjectId}_{$format}_" . now()->format('Y-m-d_H-i-s');

            switch ($format) {
                case 'json':
                    $content = json_encode($exportData, JSON_PRETTY_PRINT);
                    $filename .= '.json';
                    break;
                case 'csv':
                    $content = $this->arrayToCsv($exportData);
                    $filename .= '.csv';
                    break;
                case 'xml':
                    $content = $this->arrayToXml($exportData);
                    $filename .= '.xml';
                    break;
            }

            // Store the export file
            $path = Storage::disk('private')->put("gdpr/exports/{$filename}", $content);

            Log::info("Data exported for {$subjectType} ID: {$subjectId} in {$format} format");

            return response()->json([
                'success' => true,
                'message' => 'Data exported successfully',
                'data' => [
                    'filename' => $filename,
                    'download_url' => route('gdpr.download', ['filename' => $filename]),
                    'expires_at' => now()->addDays(7)->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error exporting data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error exporting data'
            ], 500);
        }
    }

    /**
     * Find a subject by type and ID.
     */
    private function findSubject(string $type, int $id)
    {
        switch ($type) {
            case 'company':
                return Company::find($id);
            case 'employee':
                return Employee::find($id);
            case 'customer':
                return Customer::find($id);
            case 'supplier':
                return Supplier::find($id);
            case 'user':
                return User::find($id);
            default:
                return null;
        }
    }

    /**
     * Convert array to CSV format.
     */
    private function arrayToCsv(array $data): string
    {
        $csv = '';

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $csv .= $this->arrayToCsv($value);
            } else {
                $csv .= "\"{$key}\",\"{$value}\"\n";
            }
        }

        return $csv;
    }

    /**
     * Convert array to XML format.
     */
    private function arrayToXml(array $data): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<gdpr_export>' . "\n";

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml .= $this->arrayToXml($value);
            } else {
                $xml .= "  <{$key}>{$value}</{$key}>\n";
            }
        }

        $xml .= '</gdpr_export>';
        return $xml;
    }
}
