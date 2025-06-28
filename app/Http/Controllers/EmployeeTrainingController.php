<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeTrainingController extends Controller
{
    /**
     * Display a listing of employee training records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $company = $user->company;

        // Get filter parameters
        $employeeId = $request->get('employee_id');
        $trainingId = $request->get('training_id');
        $status = $request->get('status');

        // Get employees and trainings for filters
        $employees = Employee::where('company_id', $company->id)->get();
        $trainings = Training::where('company_id', $company->id)->get();

        // Build the query
        $query = Employee::where('company_id', $company->id)
            ->with(['trainings' => function ($query) use ($trainingId, $status) {
                if ($trainingId) {
                    $query->where('trainings.id', $trainingId);
                }
                if ($status) {
                    switch ($status) {
                        case 'attended':
                            $query->wherePivot('attended', true);
                            break;
                        case 'completed':
                            $query->wherePivot('completed', true);
                            break;
                        case 'not_attended':
                            $query->wherePivot('attended', false);
                            break;
                        case 'not_completed':
                            $query->wherePivot('completed', false);
                            break;
                    }
                }
            }]);

        // Apply employee filter
        if ($employeeId) {
            $query->where('id', $employeeId);
        }

        $employeesWithTrainings = $query->get();

        // Get statistics
        $stats = [
            'total_employees' => $employees->count(),
            'total_trainings' => $trainings->count(),
            'total_assignments' => $employeesWithTrainings->sum(function ($employee) {
                return $employee->trainings->count();
            }),
            'completed_trainings' => $employeesWithTrainings->sum(function ($employee) {
                return $employee->trainings->where('pivot.completed', true)->count();
            }),
        ];

        return view('employee-training.index', compact(
            'employeesWithTrainings',
            'employees',
            'trainings',
            'stats',
            'employeeId',
            'trainingId',
            'status'
        ));
    }

    /**
     * Show the form for creating a new employee training assignment.
     */
    public function create()
    {
        $user = Auth::user();
        $company = $user->company;

        $employees = Employee::where('company_id', $company->id)->get();
        $trainings = Training::where('company_id', $company->id)->get();

        return view('employee-training.create', compact('employees', 'trainings'));
    }

    /**
     * Store a newly created employee training assignment.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'training_id' => 'required|exists:trainings,id',
            'attended' => 'boolean',
            'completed' => 'boolean',
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $training = Training::findOrFail($request->training_id);

        // Check if assignment already exists
        if ($employee->trainings()->where('training_id', $request->training_id)->exists()) {
            return back()->withErrors(['error' => 'This employee is already assigned to this training.']);
        }

        // Create the assignment
        $employee->trainings()->attach($request->training_id, [
            'attended' => $request->boolean('attended'),
            'completed' => $request->boolean('completed'),
            'score' => $request->score,
            'notes' => $request->notes,
        ]);

        return redirect()->route('employee-training.index')
            ->with('success', 'Employee training assignment created successfully!');
    }

    /**
     * Display the specified employee training record.
     */
    public function show(string $id)
    {
        // This would show a specific employee's training record
        $employee = Employee::with('trainings')->findOrFail($id);

        return view('employee-training.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified employee training assignment.
     */
    public function edit(string $id)
    {
        // Parse the ID to get employee_id and training_id
        [$employeeId, $trainingId] = explode('-', $id);

        $employee = Employee::findOrFail($employeeId);
        $assignment = $employee->trainings()->where('training_id', $trainingId)->firstOrFail();

        return view('employee-training.edit', compact('assignment', 'id'));
    }

    /**
     * Update the specified employee training assignment.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'attended' => 'boolean',
            'completed' => 'boolean',
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Parse the ID to get employee_id and training_id
        // This assumes the ID format is "employee_id-training_id"
        [$employeeId, $trainingId] = explode('-', $id);

        $employee = Employee::findOrFail($employeeId);

        $employee->trainings()->updateExistingPivot($trainingId, [
            'attended' => $request->boolean('attended'),
            'completed' => $request->boolean('completed'),
            'score' => $request->score,
            'notes' => $request->notes,
        ]);

        return redirect()->route('employee-training.index')
            ->with('success', 'Employee training record updated successfully!');
    }

    /**
     * Remove the specified employee training assignment.
     */
    public function destroy(string $id)
    {
        // Parse the ID to get employee_id and training_id
        [$employeeId, $trainingId] = explode('-', $id);

        $employee = Employee::findOrFail($employeeId);
        $employee->trainings()->detach($trainingId);

        return redirect()->route('employee-training.index')
            ->with('success', 'Employee training assignment removed successfully!');
    }
}
