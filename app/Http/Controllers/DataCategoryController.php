<?php

namespace App\Http\Controllers;

use App\Models\DataCategory;
use Illuminate\Http\Request;

class DataCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dataCategories = DataCategory::all();
        return view('data-categories.index', compact('dataCategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('data-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        DataCategory::create($request->all());

        return redirect()->route('data-categories.index')->with('success', 'Data Category created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(DataCategory $dataCategory)
    {
        return view('data-categories.show', compact('dataCategory'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DataCategory $dataCategory)
    {
        return view('data-categories.edit', compact('dataCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DataCategory $dataCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $dataCategory->update($request->all());

        return redirect()->route('data-categories.index')->with('success', 'Data Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DataCategory $dataCategory)
    {
        $dataCategory->delete();

        return redirect()->route('data-categories.index')->with('success', 'Data Category deleted successfully.');
    }
}
