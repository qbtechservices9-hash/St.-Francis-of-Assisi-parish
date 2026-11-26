@extends('layouts.site')

@section('content')
@php
  $isActive = old('is_active', 1);
@endphp

  <section class="py-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Create Ministry</h1>
        <a href="{{ route('admin.ministries.index') }}" class="text-blue-600 hover:text-blue-700">Back to Ministries</a>
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

      <form method="POST" action="{{ route('admin.ministries.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div>
          <label class="block text-gray-700 mb-2" for="name">Name *</label>
          <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name') }}"
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

        <div>
          <label class="block text-gray-700 mb-2" for="contact_email">Contact Email *</label>
          <input
            id="contact_email"
            name="contact_email"
            type="email"
            value="{{ old('contact_email') }}"
            required
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-gray-700 mb-2" for="icon">Icon</label>
            <input
              id="icon"
              name="icon"
              type="text"
              value="{{ old('icon', 'users') }}"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>

          <div>
            <label class="block text-gray-700 mb-2" for="order">Order</label>
            <input
              id="order"
              name="order"
              type="number"
              min="0"
              value="{{ old('order', 0) }}"
              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
            >
          </div>
        </div>

        <div>
          <label class="block text-gray-700 mb-2" for="image">Image</label>
          <input
            id="image"
            name="image"
            type="file"
            accept="image/*"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600"
          >
        </div>

        <label class="inline-flex items-center gap-2">
          <input type="checkbox" name="is_active" value="1" {{ $isActive ? 'checked' : '' }}>
          <span>Active</span>
        </label>

        <button
          type="submit"
          class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
        >
          Save Ministry
        </button>
      </form>
    </div>
  </section>
@endsection
