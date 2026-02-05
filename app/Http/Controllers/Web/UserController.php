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
                ->addColumn('status', function ($user) {
                    return $user->is_active 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('actions', function ($user) {
                    /** @var \App\Models\User $user */
                    $edit = '<button data-id="' . $user->id . '" class="btn btn-sm btn-primary edit-user me-1">Edit</button>';
                    $delete = $user->id === \Illuminate\Support\Facades\Auth::id() ? '' : '<button data-url="' . route('users.destroy', $user->id) . '" class="btn btn-sm btn-danger delete-user">Delete</button>';
                    return $edit . $delete;
                })
                ->rawColumns(['status', 'actions'])
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
