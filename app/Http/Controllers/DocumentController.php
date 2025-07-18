<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'file' => 'required|file|mimes:txt,pdf|max:5120', // 5MB max
            'document_type_id' => 'required|exists:document_types,id',
        ]);

        $modelClass = $request->input('documentable_type');
        $modelId = $request->input('documentable_id');
        if (!class_exists($modelClass)) {
            return back()->with('error', 'Invalid model type.');
        }
        $model = $modelClass::findOrFail($modelId);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Determine company context for Drive upload
        $companyId = $request->input('company_id');
        $driveDirectory = null;
        if ($companyId) {
            $company = \App\Models\Company::find($companyId);
            if ($company && $company->drive_directory) {
                $driveDirectory = $company->drive_directory;
            }
        }

        // Use existing Drive upload service
        if ($driveDirectory) {
            // Example: DriveUploadService::upload($file, $driveDirectory)
            $drivePath = app('App\\Services\\DriveUploadService')->upload($file, $driveDirectory);
            $path = $drivePath;
        } else {
            // fallback to local/public storage
            $path = $file->storeAs('documents', $filename, 'public');
        }

        $document = $model->documents()->create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'uploaded_by' => Auth::id(),
            'document_type_id' => $request->input('document_type_id'),
            'company_id' => $companyId,
        ]);

        return back()->with('success', 'Document uploaded successfully.');
    }

    public function destroy(Document $document)
    {
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return back()->with('success', 'Document deleted successfully.');
    }
}
