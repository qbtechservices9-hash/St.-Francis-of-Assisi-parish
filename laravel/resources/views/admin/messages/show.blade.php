@extends('layouts.site')

@section('content')
  <section class="py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between mb-6">
        <h1 class="text-3xl">Message</h1>
        <form method="POST" action="{{ route('admin.messages.destroy', $message) }}">
          @csrf
          @method('DELETE')
          <button type="submit" class="text-red-600 hover:text-red-700">Delete</button>
        </form>
      </div>

      <div class="bg-white shadow rounded-lg p-6 space-y-3">
        <div><strong>Name:</strong> {{ $message->name }}</div>
        <div><strong>Email:</strong> {{ $message->email }}</div>
        @if ($message->phone)
          <div><strong>Phone:</strong> {{ $message->phone }}</div>
        @endif
        <div><strong>Subject:</strong> {{ $message->subject }}</div>
        <div><strong>Message:</strong></div>
        <div class="text-gray-700 whitespace-pre-line">{{ $message->message }}</div>
      </div>
    </div>
  </section>
@endsection
