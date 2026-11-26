<nav class="bg-gray-900 text-gray-100">
  <div class="max-w-6xl mx-auto px-4 py-3 flex flex-wrap items-center gap-4">
    <a href="{{ route('admin.dashboard') }}" class="font-semibold hover:text-white">Admin Dashboard</a>
    <a href="{{ route('admin.events.index') }}" class="hover:text-white">Events</a>
    <a href="{{ route('admin.ministries.index') }}" class="hover:text-white">Ministries</a>
    <a href="{{ route('admin.gallery.index') }}" class="hover:text-white">Gallery</a>
    <a href="{{ route('admin.messages.index') }}" class="hover:text-white">Messages</a>
    <span class="text-gray-400">|</span>
    <a href="{{ route('home') }}" class="hover:text-white">View Site</a>
  </div>
</nav>
