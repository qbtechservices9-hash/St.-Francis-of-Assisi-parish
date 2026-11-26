<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Support\MediaStorage;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index()
    {
        $items = GalleryItem::orderBy('date', 'desc')->paginate(20);

        return view('admin.gallery.index', compact('items'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video',
            'category' => 'required|string',
            'date' => 'required|date',
            'url' => 'required_if:type,video|nullable|url',
            'image' => 'required_if:type,image|nullable|image|max:5120',
            'thumbnail' => 'nullable|image|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->type === 'image' && $request->hasFile('image')) {
            $validated['url'] = MediaStorage::storeUploadedFile($request->file('image'), 'gallery');
        }

        if ($request->type === 'video' && $request->hasFile('thumbnail')) {
            $validated['thumbnail'] = MediaStorage::storeUploadedFile($request->file('thumbnail'), 'gallery');
        }

        GalleryItem::create($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item created successfully.');
    }

    public function edit(GalleryItem $gallery)
    {
        return view('admin.gallery.edit', compact('gallery'));
    }

    public function update(Request $request, GalleryItem $gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:image,video',
            'category' => 'required|string',
            'date' => 'required|date',
            'url' => 'nullable|url',
            'image' => 'nullable|image|max:5120',
            'thumbnail' => 'nullable|image|max:5120',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->type === 'video' && !$request->filled('url') && !$gallery->url) {
            return back()
                ->withErrors(['url' => 'A video URL is required for video items.'])
                ->withInput();
        }

        if ($request->type === 'image' && !$request->hasFile('image') && !$gallery->url) {
            return back()
                ->withErrors(['image' => 'An image file is required for image items.'])
                ->withInput();
        }

        if ($request->type === 'image' && $request->hasFile('image')) {
            MediaStorage::delete($gallery->url);
            $validated['url'] = MediaStorage::storeUploadedFile($request->file('image'), 'gallery');
        }

        if ($request->type === 'video') {
            $validated['url'] = $request->filled('url') ? $request->input('url') : $gallery->url;
        }

        if ($request->hasFile('thumbnail')) {
            MediaStorage::delete($gallery->thumbnail);
            $validated['thumbnail'] = MediaStorage::storeUploadedFile($request->file('thumbnail'), 'gallery');
        }

        $gallery->update($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item updated successfully.');
    }

    public function destroy(GalleryItem $gallery)
    {
        if ($gallery->type === 'image') {
            MediaStorage::delete($gallery->url);
        }

        MediaStorage::delete($gallery->thumbnail);

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }
}
