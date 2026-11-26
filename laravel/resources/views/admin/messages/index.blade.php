@extends('layouts.site')

@section('content')
  <section class="py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <h1 class="text-3xl mb-6">Messages</h1>

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
              <th class="px-4 py-3">Subject</th>
              <th class="px-4 py-3">Date</th>
              <th class="px-4 py-3">Read</th>
              <th class="px-4 py-3"></th>
            </tr>
          </thead>
          <tbody>
            @forelse ($messages as $message)
              <tr class="border-t">
                <td class="px-4 py-3">{{ $message->name }}</td>
                <td class="px-4 py-3">{{ $message->subject }}</td>
                <td class="px-4 py-3">{{ $message->created_at->format('M d, Y') }}</td>
                <td class="px-4 py-3">{{ $message->is_read ? 'Yes' : 'No' }}</td>
                <td class="px-4 py-3 text-right">
                  <a href="{{ route('admin.messages.show', $message) }}" class="text-blue-600 hover:text-blue-700">View</a>
                </td>
              </tr>
            @empty
              <tr class="border-t">
                <td colspan="5" class="px-4 py-6 text-center text-gray-600">No messages yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
@endsection
