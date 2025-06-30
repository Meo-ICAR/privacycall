<?php

namespace App\Http\Controllers;

use App\Models\ThirdCountry;
use Illuminate\Http\Request;

class ThirdCountryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = ThirdCountry::query();

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('country_name', 'like', "%{$searchTerm}%")
                  ->orWhere('country_code', 'like', "%{$searchTerm}%");
        }

        $thirdCountries = $query->paginate(15);

        return view('third-countries.index', compact('thirdCountries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('third-countries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:third_countries',
            'adequacy_decision' => 'boolean',
        ]);

        ThirdCountry::create($request->all());

        return redirect()->route('third-countries.index')->with('success', 'Third Country created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ThirdCountry $thirdCountry)
    {
        return view('third-countries.show', compact('thirdCountry'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThirdCountry $thirdCountry)
    {
        return view('third-countries.edit', compact('thirdCountry'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ThirdCountry $thirdCountry)
    {
        $request->validate([
            'country_name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10|unique:third_countries,country_code,' . $thirdCountry->id,
            'adequacy_decision' => 'boolean',
        ]);

        $thirdCountry->update($request->all());

        return redirect()->route('third-countries.index')->with('success', 'Third Country updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThirdCountry $thirdCountry)
    {
        $thirdCountry->delete();

        return redirect()->route('third-countries.index')->with('success', 'Third Country deleted successfully.');
    }
}
