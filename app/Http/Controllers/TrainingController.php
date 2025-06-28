<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Training;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $trainings = Training::with(['company', 'customer'])
            ->where('company_id', $user->company_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get statistics
        $totalTrainings = $trainings->count();
        $activeTrainings = $trainings->where('is_active', true)->count();
        $inactiveTrainings = $trainings->where('is_active', false)->count();

        return view('trainings.index', compact('trainings', 'totalTrainings', 'activeTrainings', 'inactiveTrainings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        if (!$company) {
            return redirect()->route('trainings.index')
                ->with('error', 'You must be assigned to a company to create trainings.');
        }

        $customers = Customer::where('company_id', $user->company_id)->orderBy('first_name')->get();
        return view('trainings.create', compact('customers', 'company'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->company) {
            return redirect()->route('trainings.index')
                ->with('error', 'You must be assigned to a company to create trainings.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:online,in_person,hybrid',
            'date' => 'nullable|date',
            'duration' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        Training::create($validated);

        return redirect()->route('trainings.index')->with('success', 'Training created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $training->load(['company', 'customer', 'employees']);
        return view('trainings.show', compact('training'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $customers = Customer::where('company_id', $user->company_id)->orderBy('first_name')->get();
        return view('trainings.edit', compact('training', 'customers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:online,in_person,hybrid',
            'date' => 'nullable|date',
            'duration' => 'nullable|string|max:255',
            'provider' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'customer_id' => 'nullable|exists:customers,id',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['company_id'] = $user->company_id;
        $validated['is_active'] = $request->has('is_active');

        $training->update($validated);

        return redirect()->route('trainings.show', $training)->with('success', 'Training updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $training->delete();

        return redirect()->route('trainings.index')->with('success', 'Training deleted successfully.');
    }

    /**
     * Show the form for managing employees in a training.
     */
    public function manageEmployees(Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        // Get all employees for the company
        $employees = \App\Models\Employee::where('company_id', $user->company_id)
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        // Get currently assigned employee IDs
        $assignedEmployeeIds = $training->employees->pluck('id')->toArray();

        return view('trainings.manage-employees', compact('training', 'employees', 'assignedEmployeeIds'));
    }

    /**
     * Update the employees assigned to a training.
     */
    public function updateEmployees(Request $request, Training $training)
    {
        $user = Auth::user();
        if ($training->company_id !== $user->company_id) {
            abort(403, 'Access denied.');
        }

        $request->validate([
            'employee_ids' => 'nullable|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        // Get the selected employee IDs (empty array if none selected)
        $selectedEmployeeIds = $request->input('employee_ids', []);

        // Sync the employees (this will add new ones and remove unselected ones)
        $training->employees()->sync($selectedEmployeeIds);

        $message = count($selectedEmployeeIds) > 0
            ? 'Employees updated successfully for this training.'
            : 'All employees removed from this training.';

        return redirect()->route('trainings.show', $training)
            ->with('success', $message);
    }
}
