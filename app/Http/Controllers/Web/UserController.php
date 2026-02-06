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
                ->addColumn('avatar', function ($user) {
                    return '<div class="d-flex align-items-center gap-2">
                                <img src="' . $user->avatar_url . '" alt="' . $user->username . '" 
                                     class="rounded-circle border" 
                                     style="width: 40px; height: 40px; object-fit: cover;">
                                <span class="fw-medium">' . $user->username . '</span>
                            </div>';
                })
                ->addColumn('status', function ($user) {
                    $checked = $user->is_active ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input toggle-status" type="checkbox" data-id="' . $user->id . '" ' . $checked . '>
                            </div>';
                })
                ->addColumn('actions', function ($user) {
                    /** @var \App\Models\User $user */
                    $edit = '<button data-id="' . $user->id . '" class="btn btn-sm btn-light text-primary rounded-circle p-2 edit-user me-1"><i class="bi bi-pencil"></i></button>';
                    $delete = $user->id === \Illuminate\Support\Facades\Auth::id() ? '' : '<button data-url="' . route('users.destroy', $user->id) . '" class="btn btn-sm btn-light text-danger rounded-circle p-2 delete-user"><i class="bi bi-trash"></i></button>';
                    return '<div class="d-flex justify-content-center">' . $edit . $delete . '</div>';
                })
                ->rawColumns(['avatar', 'status', 'actions'])
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        try {
            User::create([
                'username' => $request->username,
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

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json(['success' => true, 'user' => $user]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string',
            'is_active' => 'boolean',
        ]);

        try {
            $data = [
                'username' => $request->username,
                'email' => $request->email,
                'role' => $request->role,
                'is_active' => $request->has('is_active') ? $request->is_active : $user->is_active,
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);
            return response()->json(['success' => true, 'msg' => 'User updated successfully']);
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
