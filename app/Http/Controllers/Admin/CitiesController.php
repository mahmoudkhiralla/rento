<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CitiesController extends Controller
{
    public function index()
    {
        $cities = City::orderBy('name')->paginate(15);

        return view('admin.cities.index', compact('cities'));
    }

    public function create()
    {
        return view('admin.cities.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $data['active'] ?? true;

        City::create($data);

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم إضافة المدينة بنجاح');
    }

    public function edit(City $city)
    {
        return view('admin.cities.edit', compact('city'));
    }

    public function update(Request $request, City $city)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $data['active'] ?? true;

        $city->update($data);

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم تحديث المدينة بنجاح');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('admin.cities.index')
            ->with('success', 'تم حذف المدينة بنجاح');
    }
}
