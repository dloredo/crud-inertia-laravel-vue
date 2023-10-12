<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use DB;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::select(
            'employees.id',
            'employees.name',
            'email',
            'phone',
            'department_id',
            'departments.name as department'
        )
            ->join('departments', 'departments.id', 'employees.department_id')
            ->paginate(8);

        $departments = Department::all();
        return Inertia::render('Employees/Index', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:150',
            'email' => 'required|email|max:80',
            'phone' => 'required|max:15',
            'department_id' => 'required|numeric',
        ]);

        $employee = new Employee($request->all());
        $employee->save();

        return redirect('employees');
    }

    public function update(Request $request, Employee $employee)
    {
        $request->validate([
            'name' => 'required|max:150',
            'email' => 'required|email|max:80',
            'phone' => 'required|max:15',
            'department_id' => 'required|numeric',
        ]);

        $employee->update($request->all());
        return redirect('employees');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect('employees');
    }

    function EmployeeByDepartment()
    {
        $data = Employee::select(
            DB::raw('count(employees.id) as count , departments.name')
        )
            ->join('departments', 'departments.id', 'employees.department_id')
            ->groupBy('departments.name')
            ->get();
        return Inertia::render('Employees/Graphic', [
            'data' => $data,
        ]);
    }

    function reports()
    {
        $employees = Employee::select(
            'employees.id',
            'employees.name',
            'email',
            'phone',
            'department_id',
            'departments.name as department'
        )
            ->join('departments', 'departments.id', 'employees.department_id')
            ->get();

        $departments = Department::all();

        return Inertia::render('Employees/Reports', [
            'employees' => $employees,
            'departments' => $departments,
        ]);
    }
}
