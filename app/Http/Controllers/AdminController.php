<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function home()
    {
        Auth::user()->can('admin') ?: abort(403, 'Não está autorizado para entrar nessa página');

        // colleact all information about the organization
        $data = [];
        // get total number of colaborators (deleted_at is null)
        $data['total_colaborators'] = User::whereNull('deleted_at')->count();

        // total colaborators deleted
        // onlyTrashed segnifica só os que foram para o lixo
        $data['total_colaborators_deleted'] = User::onlyTrashed()->count();

        // total salary for all colaborators
        $data['total_salary'] = User::withTrashed()->with('detail')->get()->sum(function ($colaborator) {
            return $colaborator->detail->salary;
        });

         $data['total_salary'] = 'R$ ' . number_format($data['total_salary'], 2, ',', '.');

        // total colaborators by department
        $data['total_colaborators_per_department'] = User::withoutTrashed()
            ->with('department')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                return [
                    'department' => $department->first()->department->name ?? "Sem departamento",
                    'total' => $department->count()
                ];
            });

        // total salary by department
        $data['total_salary_by_department'] = User::withoutTrashed()
            ->with('department', 'detail')
            ->get()
            ->groupBy('department_id')
            ->map(function ($department) {
                return [
                    'department' => $department->first()->department->name ?? 'Sem departamento',
                    'total' => $department->sum(function ($colaborator) {
                        return $colaborator->detail->salary;
                    })
                ];
            });
        $data['total_salary_by_department'] =  $data['total_salary_by_department']->map(function($department){
            return [
                'department' => $department['department'],
                'total' => 'R$ ' . number_format($department['total'], 2, ',', '.')
            ];
        });
            

        return view('home', compact('data'));
    }
}
