<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }

    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = User::query();

            return DataTables::of($query)
                ->addColumn('full_name', function ($user) {
                    return trim(($user->prefix ? $user->prefix . ' ' : '') . $user->first_name . ' ' . $user->last_name) ?: $user->name;
                })
                ->addColumn('details', function ($user) {
                    $html = '<div><i class="bi bi-telephone small me-1"></i> ' . ($user->phone ?: 'N/A') . '</div>';
                    $html .= '<div class="small text-muted"><i class="bi bi-geo-alt small me-1"></i> ' . ($user->city ?: 'N/A') . '</div>';
                    return $html;
                })
                ->addColumn('status', function ($user) {
                    $checked = $user->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="'.$user->id.'" '.$checked.'>
                            </div>';
                })
                ->addColumn('actions', function ($user) {
                    /** @var \App\Models\User $user */
                    $delete = $user->id === \Illuminate\Support\Facades\Auth::id() ? '' : '<button data-url="' . route('users.destroy', $user->id) . '" class="btn btn-sm btn-light text-danger rounded-circle p-2 delete-user"><i class="bi bi-trash"></i></button>';
                    return '<div class="d-flex justify-content-center">' . $delete . '</div>';
                })
                ->rawColumns(['details', 'status', 'actions'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'is_active' => true,
            ]);
            return response()->json(['success' => true, 'msg' => 'User created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        if ($id == \Illuminate\Support\Facades\Auth::id()) {
            return response()->json(['success' => false, 'msg' => 'Cannot delete yourself'], 403);
        }
        User::findOrFail($id)->delete();
        return response()->json(['success' => true, 'msg' => 'User deleted successfully']);
    }
}
