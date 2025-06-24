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

class SupplierController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Add other supplier fields as needed
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
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

    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Add other supplier fields as needed
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $data = $validator->validated();
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

    public function export()
    {
        return Excel::download(new SuppliersExport, 'suppliers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        Excel::import(new SuppliersImport, $request->file('file'));
        return back()->with('success', 'Suppliers imported successfully.');
    }
}
