<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AreasController extends Controller
{
    public function index(Request $request)
    {
        $cityId = $request->query('city_id');
        $query = Area::with('city')->orderBy('name');
        if ($cityId) {
            $query->where('city_id', $cityId);
        }
        $areas = $query->paginate(15);
        $cities = City::orderBy('name')->get();

        return view('admin.areas.index', compact('areas', 'cities', 'cityId'));
    }

    public function create()
    {
        $cities = City::orderBy('name')->get();

        return view('admin.areas.create', compact('cities'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $data['active'] ?? true;

        Area::create($data);

        return redirect()->route('admin.areas.index')
            ->with('success', 'تم إضافة المنطقة بنجاح');
    }

    public function edit(Area $area)
    {
        $cities = City::orderBy('name')->get();

        return view('admin.areas.edit', compact('area', 'cities'));
    }

    public function update(Request $request, Area $area)
    {
        $data = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'name' => 'required|string|max:255',
            'active' => 'nullable|boolean',
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['active'] = $data['active'] ?? true;

        $area->update($data);

        return redirect()->route('admin.areas.index')
            ->with('success', 'تم تحديث المنطقة بنجاح');
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', 'تم حذف المنطقة بنجاح');
    }
}
