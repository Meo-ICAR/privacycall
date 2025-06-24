<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    public function index()
    {
        $types = DocumentType::all();
        return view('document_types.index', compact('types'));
    }

    public function create()
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create document types.');
        }
        return view('document_types.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can create document types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name',
        ]);
        DocumentType::create(['name' => $request->name]);
        return redirect()->route('document-types.index')->with('success', 'Document type created.');
    }

    public function edit(DocumentType $documentType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can edit document types.');
        }
        return view('document_types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can update document types.');
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:document_types,name,' . $documentType->id,
        ]);
        $documentType->update(['name' => $request->name]);
        return redirect()->route('document-types.index')->with('success', 'Document type updated.');
    }

    public function destroy(DocumentType $documentType)
    {
        if (!auth()->user() || !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Only superadmin can delete document types.');
        }
        $documentType->delete();
        return redirect()->route('document-types.index')->with('success', 'Document type deleted.');
    }
}
