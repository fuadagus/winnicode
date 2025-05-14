<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Location;
use App\Models\News;

class LocationController extends Controller
{
    public function index()
    {
        return Location::with('users')->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);
        return Location::create($data);
    }

    public function show(Location $location)
    {
        return $location->load('users');
    }

    public function update(Request $request, Location $location)
    {
        $location->update($request->only('name', 'latitude', 'longitude'));
        return $location;
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return response()->json(null, 204);
    }
}
