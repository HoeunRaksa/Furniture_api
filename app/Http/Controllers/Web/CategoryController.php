<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('categories.index');
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Category::query();

            return DataTables::of($query)
                ->editColumn('created_at', function ($row) {
                    return $row->created_at ? $row->created_at->format('Y-m-d H:i:s A') : '-';
                })
                ->addColumn('actions', function ($row) {
                    $edit = '<button data-id="'. $row->id .'" data-name="' . $row->name . '" type="button" class="edit-category btn btn-sm btn-primary me-1">Edit</button>';
                    $delete = '<button data-url="' . route('categories.destroy', $row->id) . '" class="btn btn-sm btn-danger delete-category">Delete</button>';
                    return $edit . $delete;
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $category = Category::create([
                'name' => $request->name,
                'slug' => str()->slug($request->name),
                'is_active' => true,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Category created successfully',
                'id' => $category->id,
                'name' => $category->name
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating category', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Error creating category: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $category = Category::find($id);

            if (!$category) {
                return response()->json(['data' => ['success' => false, 'msg' => 'Category not found']]);
            }

            $category->name = $request->name;
            $category->slug = str()->slug($request->name);
            $category->save();

            return response()->json([
                'success' => true,
                'msg' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating category', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to update category'
            ], 500);
        }
    }

    /**
     * Remove the specified resource in storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $category = Category::find($id);

            if (!$category) {
                return response()->json(['data' => ['success' => false, 'msg' => 'Category not found']]);
            }

            $category->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'msg' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Category delete failed', ['id' => $id, 'message' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'msg' => 'Failed to delete category'
            ], 500);
        }
    }
}
