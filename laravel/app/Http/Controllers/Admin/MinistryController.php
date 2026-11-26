<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use App\Support\MediaStorage;
use Illuminate\Http\Request;

class MinistryController extends Controller
{
    public function index()
    {
        $ministries = Ministry::orderBy('order')->paginate(20);

        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'icon' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = MediaStorage::storeUploadedFile($request->file('image'), 'ministries');
        }

        Ministry::create($validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry created successfully.');
    }

    public function edit(Ministry $ministry)
    {
        return view('admin.ministries.edit', compact('ministry'));
    }

    public function update(Request $request, Ministry $ministry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'icon' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $validated['image'] = MediaStorage::storeUploadedFile($request->file('image'), 'ministries');
        }

        $ministry->update($validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry updated successfully.');
    }

    public function destroy(Ministry $ministry)
    {
        MediaStorage::delete($ministry->image);

        $ministry->delete();

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry deleted successfully.');
    }
}
