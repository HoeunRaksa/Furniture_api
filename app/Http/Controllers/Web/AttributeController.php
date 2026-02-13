<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class AttributeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('attributes.index');
    }

    /**
     * Get data for DataTables.
     */
    public function data(Request $request)
    {
        if ($request->ajax()) {
            $query = Attribute::withCount('values');

            return DataTables::of($query)
                ->addColumn('actions', function ($row) {
                    $view = '<button data-id="'.$row->id.'" data-name="'.$row->name.'" class="btn btn-sm btn-info view-values me-1">Values</button>';
                    $edit = '<button data-id="'.$row->id.'" data-name="'.$row->name.'" class="btn btn-sm btn-primary edit-attribute me-1">Edit</button>';
                    $delete = '<button data-url="'.route('attributes.destroy', $row->id).'" class="btn btn-sm btn-danger delete-attribute">Delete</button>';

                    return $view.$edit.$delete;
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
            'name' => 'required|string|max:255|unique:attributes,name',
        ]);

        try {
            $attribute = Attribute::create($request->only('name'));

            return response()->json([
                'success' => true,
                'msg' => 'Attribute created successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:attributes,name,'.$id,
        ]);

        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->update($request->only('name'));

            return response()->json([
                'success' => true,
                'msg' => 'Attribute updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $attribute = Attribute::findOrFail($id);
            $attribute->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Attribute deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to delete attribute',
            ], 500);
        }
    }

    /**
     * Get values for a specific attribute.
     */
    public function getValues($id)
    {
        $values = AttributeValue::where('attribute_id', $id)->get();

        return response()->json($values);
    }

    /**
     * Store a new value for an attribute.
     */
    public function storeValue(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'value' => 'required|string|max:255',
        ]);

        try {
            AttributeValue::create($request->only(['attribute_id', 'value']));

            return response()->json([
                'success' => true,
                'msg' => 'Value added successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Error: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a specific attribute value.
     */
    public function destroyValue($id)
    {
        try {
            $value = AttributeValue::findOrFail($id);
            $value->delete();

            return response()->json([
                'success' => true,
                'msg' => 'Value deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'msg' => 'Failed to delete value',
            ], 500);
        }
    }
}
