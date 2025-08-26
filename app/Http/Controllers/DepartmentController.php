<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index()
    {

        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');
        $departments = Department::all();

        return view('department.departments', compact('departments'));
    }

    public function newDepartment(): View
    {
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');
        return view('department.add-department');
    }

    public function createDepartment(Request $request)
    {
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

        //form validation
        $request->validate([
            'name' => 'required|string|max:50|unique:departments'
            
        ]);

        Department::create([
            'name' => $request->name
        ]);

        return redirect()->route('departments');
    }

    public function editDepartment($id){
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

      
        if($this->isDepartmentBlocked($id)){
            return redirect()->route('departments');
        }
        $department = Department::findOrFail($id);

        return view('department.edit-department', compact('department'));
    }

    public function updateDepartment(Request $request){
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

        $id = $request->id;

        //form validation
        $request->validate([
            'id' => 'required',
            'name' => 'required|string|min:3|max:50|unique:departments,name,' .$id
            
        ],
        [
                'name.required' => 'O departamento é obrigatório',
                'name.min' => 'O departamento deve ter no mínimo :min caracteres',
                'name.max' => 'O departamento deve ter no máximo :max caracteres',
                'name.unique' => 'O departamento ja existe, tente novamente',
        ]
    );

        
        if($this->isDepartmentBlocked($id)){
            return redirect()->route('departments');
        }

        $department = Department::findOrFail($id);

        $department->update([
            'name' => $request->name
        ]);

        return redirect()->route('departments');

    }

    public function deleteDepartment($id) {
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

        
        if($this->isDepartmentBlocked($id)){
            return redirect()->route('departments');
        }

         $department = Department::findOrFail($id);

         //display page for confirmation

         return view('department.delete-department-confirm', compact('department'));
    }

    public function deleteDepartmentConfirm($id){
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

       
        if($this->isDepartmentBlocked($id)){
            return redirect()->route('departments');
        }

         $department = Department::findOrFail($id);

         $department->delete();

         //update all colaborators departament to null
         User::where('department_id', $id)->update(
            [
                'department_id' => null
            ]
        );

         return redirect()->route('departments');
    }

    private function isDepartmentBlocked($id)
    {
        return in_array(intval($id), [1,2]);
    }
}
