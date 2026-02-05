<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BusinessController extends Controller
{
    public function index()
    {
        $business = Business::first() ?? new Business();
        return view('business.index', compact('business'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'logo' => 'nullable|image|max:2048',
        ]);

        try {
            $business = Business::first() ?? new Business();
            $data = $request->except('logo');

            if ($request->hasFile('logo')) {
                if ($business->logo && File::exists(public_path('uploads/business/' . $business->logo))) {
                    File::delete(public_path('uploads/business/' . $business->logo));
                }
                $file = $request->file('logo');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/business'), $filename);
                $data['logo'] = $filename;
            }

            $business->fill($data);
            $business->save();

            return response()->json(['success' => true, 'msg' => 'Settings updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()], 500);
        }
    }
}
