<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use App\Exports\SuppliersExport;
use App\Imports\SuppliersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        $user = Auth::user();
        $companies = collect([$user->company]); // Only user's company
        $supplierTypes = \App\Models\SupplierType::all();
        return view('suppliers.create', compact('companies', 'supplierTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'supplier_type_id' => 'required|exists:supplier_types,id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'supplier_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $data['logo_url'] = Storage::url($filename);
            }

            $supplier = Supplier::create($data);
            DB::commit();
            return redirect()->route('suppliers.show', $supplier->id)->with('success', 'Supplier created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating supplier: ' . $e->getMessage());
            return back()->with('error', 'Error creating supplier.');
        }
    }

    public function show(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }
        $companies = collect([$user->company]); // Only user's company
        $supplierTypes = \App\Models\SupplierType::all();
        return view('suppliers.edit', compact('supplier', 'companies', 'supplierTypes'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'supplier_type_id' => 'required|exists:supplier_types,id',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
            $data['company_id'] = $user->company_id;

            if ($request->hasFile('logo')) {
                $image = $request->file('logo');
                $filename = 'supplier_logos/' . uniqid('logo_') . '.' . $image->getClientOriginalExtension();
                $resized = Image::make($image)->resize(256, 256, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode();
                Storage::disk('public')->put($filename, $resized);
                $data['logo_url'] = Storage::url($filename);
            }

            $supplier->update($data);
            DB::commit();
            return redirect()->route('suppliers.show', $supplier->id)->with('success', 'Supplier updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating supplier: ' . $e->getMessage());
            return back()->with('error', 'Error updating supplier.');
        }
    }

    public function destroy(Supplier $supplier)
    {
        $user = Auth::user();
        if ($supplier->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }

    public function export()
    {
        $user = Auth::user();
        $suppliers = Supplier::where('company_id', $user->company_id)->get();
        return Excel::download(new SuppliersExport($suppliers), 'suppliers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);

        $user = Auth::user();
        Excel::import(new SuppliersImport($user->company_id), $request->file('file'));
        return back()->with('success', 'Suppliers imported successfully.');
    }
}
