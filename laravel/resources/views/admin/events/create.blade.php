@extends('layouts.site')

@section('content')
@php
  $categories = ['Mass', 'Prayer', 'Fellowship', 'Formation', 'Music', 'Service', 'Sacraments', 'Special'];
  $isPublished = old('is_published', 1);
@endphp

  <section class="py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Create Event</h1>
        <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-700">Back to Events</a>
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

      <form method="POST" action="{{ route('admin.events.store') }}" class="space-y-6">
        @csrf

        <div>
          <label class="block text-gray-700 mb-2" for="title">Title *</label>
          <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title') }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="description">Description *</label>
          <textarea
            id="description"
            name="description"
            rows="5"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 resize-none"
          >{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2" for="date">Date *</label>
            <input
              id="date"
              name="date"
              type="date"
              value="{{ old('date') }}"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>

          <div>
            <label class="block text-gray-700 mb-2" for="time">Time *</label>
            <input
              id="time"
              name="time"
              type="time"
              value="{{ old('time') }}"
              required
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="location">Location *</label>
          <input
            id="location"
            name="location"
            type="text"
            value="{{ old('location') }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="category">Category *</label>
          <input
            id="category"
            name="category"
            list="event-categories"
            type="text"
            value="{{ old('category') }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
          <datalist id="event-categories">
            @foreach ($categories as $category)
              <option value="{{ $category }}"></option>
            @endforeach
          </datalist>
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="recurring">Recurring</label>
          <input
            id="recurring"
            name="recurring"
            type="text"
            value="{{ old('recurring') }}"
            placeholder="e.g. Every Sunday"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_published" value="1" {{ $isPublished ? 'checked' : '' }}>
          <span>Publish immediately</span>
        </label>

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
        >
          Save Event
        </button>
      </form>
    </div>
  </section>
@endsection
