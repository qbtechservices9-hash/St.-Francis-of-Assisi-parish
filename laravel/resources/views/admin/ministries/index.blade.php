@extends('layouts.site')

@section('content')
  <section class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Ministries</h1>
        <a href="{{ route('admin.ministries.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
          New Ministry
        </a>
      </div>

      @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
          {{ session('success') }}
        </div>
      @endif

      <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="w-full text-left text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-4 py-3">Name</th>
              <th class="px-4 py-3">Contact</th>
              <th class="px-4 py-3">Active</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($ministries as $ministry)
              <tr class="border-t">
                <td class="px-4 py-3">{{ $ministry->name }}</td>
                <td class="px-4 py-3">{{ $ministry->contact_email }}</td>
                <td class="px-4 py-3">{{ $ministry->is_active ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('admin.ministries.edit', $ministry) }}" class="text-blue-600 hover:text-blue-700">Edit</a>
                </td>
              </tr>
            @empty
              <tr class="border-t">
                <td colspan="4" class="px-4 py-6 text-center text-gray-600">No ministries yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
@endsection
