@extends('layouts.site')

@section('content')
@php
  $categories = ['worship', 'events', 'sacraments', 'youth', 'outreach', 'church'];
  $dateValue = $gallery->date ? \Illuminate\Support\Carbon::parse($gallery->date)->format('Y-m-d') : '';
@endphp

  <section class="py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Edit Gallery Item</h1>
        <a href="{{ route('admin.gallery.index') }}" class="text-blue-600 hover:text-blue-700">Back to Gallery</a>
      </div>

      @if ($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-6">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('admin.gallery.update', $gallery) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-gray-700 mb-2" for="title">Title *</label>
          <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $gallery->title) }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="description">Description</label>
          <textarea
            id="description"
            name="description"
            rows="4"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none"
          >{{ old('description', $gallery->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2" for="type">Type *</label>
            <select
              id="type"
              name="type"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
              <option value="image" {{ old('type', $gallery->type) === 'image' ? 'selected' : '' }}>Image</option>
              <option value="video" {{ old('type', $gallery->type) === 'video' ? 'selected' : '' }}>Video</option>
            </select>
          </div>

          <div>
            <label class="block text-gray-700 mb-2" for="date">Date *</label>
            <input
              id="date"
              name="date"
              type="date"
              value="{{ old('date', $dateValue) }}"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="category">Category *</label>
          <input
            id="category"
            name="category"
            list="gallery-categories"
            type="text"
            value="{{ old('category', $gallery->category) }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
          <datalist id="gallery-categories">
            @foreach ($categories as $category)
              <option value="{{ $category }}"></option>
            @endforeach
          </datalist>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2" for="url">Video URL</label>
            <input
              id="url"
              name="url"
              type="url"
              value="{{ old('url', $gallery->url) }}"
              placeholder="https://www.youtube.com/embed/..."
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
            <p class="text-sm text-gray-500 mt-1">Required for video items.</p>
          </div>

          <div>
            <label class="block text-gray-700 mb-2" for="thumbnail">Video Thumbnail</label>
            <input
              id="thumbnail"
              name="thumbnail"
              type="file"
              accept="image/*"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
            @if ($gallery->thumbnail)
              <img src="{{ $gallery->thumbnail }}" alt="{{ $gallery->title }}" class="mt-2 w-40 rounded">
            @endif
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="image">Image File</label>
          <input
            id="image"
            name="image"
            type="file"
            accept="image/*"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
          @if ($gallery->type === 'image' && $gallery->url)
            <img src="{{ $gallery->url }}" alt="{{ $gallery->title }}" class="mt-2 w-40 rounded">
          @endif
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2" for="order">Order</label>
            <input
              id="order"
              name="order"
              type="number"
              min="0"
              value="{{ old('order', $gallery->order) }}"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>
        </div>

        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_published" value="1" {{ old('is_published', $gallery->is_published) ? 'checked' : '' }}>
          <span>Published</span>
        </label>

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
        >
          Update Item
        </button>
      </form>
    </div>
  </section>
@endsection
