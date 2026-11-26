@extends('layouts.site')

@section('content')
@php
  $categories = ['Mass', 'Prayer', 'Fellowship', 'Formation', 'Music', 'Service', 'Sacraments', 'Special'];
  $dateValue = $event->date ? \Illuminate\Support\Carbon::parse($event->date)->format('Y-m-d') : '';
  $timeValue = $event->time ? \Illuminate\Support\Carbon::parse($event->time)->format('H:i') : '';
@endphp

  <section class="py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Edit Event</h1>
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

      <form method="POST" action="{{ route('admin.events.update', $event) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
          <label class="block text-gray-700 mb-2" for="title">Title *</label>
          <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title', $event->title) }}"
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
          >{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

          <div>
            <label class="block text-gray-700 mb-2" for="time">Time *</label>
            <input
              id="time"
              name="time"
              type="time"
              value="{{ old('time', $timeValue) }}"
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
            value="{{ old('location', $event->location) }}"
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
            value="{{ old('category', $event->category) }}"
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
            value="{{ old('recurring', $event->recurring) }}"
            placeholder="e.g. Every Sunday"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_published" value="1" {{ old('is_published', $event->is_published) ? 'checked' : '' }}>
          <span>Published</span>
        </label>

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
        >
          Update Event
        </button>
      </form>
    </div>
  </section>
@endsection
