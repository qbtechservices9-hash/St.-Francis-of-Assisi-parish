@extends('layouts.site')

@section('content')
  <section class="py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl mb-4">Admin Dashboard</h1>
      <p class="text-gray-600">
        Use the admin menu to manage events, ministries, gallery items, and messages.
        For a full admin panel, consider installing Filament as outlined in the guide.
      </p>
      <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.events.index') }}" class="bg-white border rounded-lg p-4 hover:shadow">Manage Events</a>
        <a href="{{ route('admin.ministries.index') }}" class="bg-white border rounded-lg p-4 hover:shadow">Manage Ministries</a>
        <a href="{{ route('admin.gallery.index') }}" class="bg-white border rounded-lg p-4 hover:shadow">Manage Gallery</a>
        <a href="{{ route('admin.messages.index') }}" class="bg-white border rounded-lg p-4 hover:shadow">View Messages</a>
      </div>
    </div>
  </section>
@endsection
