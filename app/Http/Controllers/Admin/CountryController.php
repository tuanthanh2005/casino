<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::latest()->get();
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => 'required|max:10|unique:countries,code',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $name = time().'.'.$image->getClientOriginalExtension();
            Storage::disk('public_uploads')->putFileAs('uploads/countries', $image, $name);
            $validated['icon'] = $name;
        }

        Country::create($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country created successfully.');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.form', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'code' => "required|max:10|unique:countries,code,{$country->id}",
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|boolean',
        ]);

        if ($request->hasFile('icon')) {
            // Delete old icon
            if ($country->icon && Storage::disk('public_uploads')->exists('uploads/countries/' . $country->icon)) {
                Storage::disk('public_uploads')->delete('uploads/countries/' . $country->icon);
            }

            $image = $request->file('icon');
            $name = time().'.'.$image->getClientOriginalExtension();
            Storage::disk('public_uploads')->putFileAs('uploads/countries', $image, $name);
            $validated['icon'] = $name;
        }

        $country->update($validated);

        return redirect()->route('admin.countries.index')->with('success', 'Country updated successfully.');
    }

    public function destroy(Country $country)
    {
        if ($country->icon && Storage::disk('public_uploads')->exists('uploads/countries/' . $country->icon)) {
            Storage::disk('public_uploads')->delete('uploads/countries/' . $country->icon);
        }
        $country->delete();
        return redirect()->route('admin.countries.index')->with('success', 'Country deleted successfully.');
    }
}
