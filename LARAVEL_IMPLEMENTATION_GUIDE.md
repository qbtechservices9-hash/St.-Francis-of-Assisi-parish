# Laravel Backend Implementation Guide
## St. Francis Of Assisi Church Website

This guide will help you convert the React frontend to a full-stack Laravel application with admin panel for managing content.

---

## 1. Laravel Project Setup

```bash
# Create new Laravel project
composer create-project laravel/laravel church-website
cd church-website

# Configure .env (APP_URL, DB_*, MAIL_*)

# Install required packages
composer require laravel/breeze --dev  # For authentication
php artisan breeze:install blade      # Install Breeze with Blade templates
npm install && npm run build

# Database setup
php artisan migrate

# Storage symlink for public uploads
php artisan storage:link
```

---

## 2. Database Schema

Create migrations for the church website:

### Events Migration
```php
// database/migrations/xxxx_create_events_table.php
Schema::create('events', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description');
    $table->date('date');
    $table->time('time');
    $table->string('location');
    $table->string('category'); // mass, prayer, fellowship, formation, etc.
    $table->string('recurring')->nullable();
    $table->boolean('is_published')->default(true);
    $table->timestamps();
});
```

### Ministries Migration
```php
// database/migrations/xxxx_create_ministries_table.php
Schema::create('ministries', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->text('description');
    $table->string('contact_email');
    $table->string('image')->nullable();
    $table->string('icon')->default('users');
    $table->integer('order')->default(0);
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});
```

### Gallery Migration
```php
// database/migrations/xxxx_create_gallery_items_table.php
Schema::create('gallery_items', function (Blueprint $table) {
    $table->id();
    $table->string('title');
    $table->text('description')->nullable();
    $table->enum('type', ['image', 'video']);
    $table->string('url'); // Image URL or YouTube embed URL
    $table->string('thumbnail')->nullable();
    $table->string('category'); // worship, events, sacraments, youth, outreach, church
    $table->date('date');
    $table->integer('order')->default(0);
    $table->boolean('is_published')->default(true);
    $table->timestamps();
});
```

### Contact Messages Migration
```php
// database/migrations/xxxx_create_contact_messages_table.php
Schema::create('contact_messages', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('phone')->nullable();
    $table->string('subject');
    $table->text('message');
    $table->boolean('is_read')->default(false);
    $table->timestamps();
});
```

### Pages Migration (for About, Home content)
```php
// database/migrations/xxxx_create_pages_table.php
Schema::create('pages', function (Blueprint $table) {
    $table->id();
    $table->string('slug')->unique();
    $table->string('title');
    $table->longText('content')->nullable();
    $table->json('sections')->nullable(); // For flexible content blocks
    $table->timestamps();
});
```

---

## 3. Models

### Event Model
```php
// app/Models/Event.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title', 'description', 'date', 'time', 
        'location', 'category', 'recurring', 'is_published'
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now())->orderBy('date');
    }
}
```

### Ministry Model
```php
// app/Models/Ministry.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ministry extends Model
{
    protected $fillable = [
        'name', 'description', 'contact_email', 
        'image', 'icon', 'order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
```

### GalleryItem Model
```php
// app/Models/GalleryItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'title', 'description', 'type', 'url', 
        'thumbnail', 'category', 'date', 'order', 'is_published'
    ];

    protected $casts = [
        'date' => 'date',
        'is_published' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->orderBy('date', 'desc');
    }

    public function scopeByCategory($query, $category)
    {
        if ($category !== 'all') {
            return $query->where('category', $category);
        }
        return $query;
    }
}
```

### ContactMessage Model
```php
// app/Models/ContactMessage.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
```

---

## 4. Controllers

### Frontend Controllers

#### HomeController
```php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ministry;
use App\Models\GalleryItem;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::published()->upcoming()->take(4)->get();
        
        return view('home', compact('upcomingEvents'));
    }

    public function about()
    {
        return view('about');
    }

    public function ministries()
    {
        $ministries = Ministry::active()->get();
        
        return view('ministries', compact('ministries'));
    }

    public function events()
    {
        $events = Event::published()->upcoming()->get();
        
        return view('events', compact('events'));
    }

    public function gallery(Request $request)
    {
        $category = $request->get('category', 'all');
        
        $galleryItems = GalleryItem::published()
            ->byCategory($category)
            ->get();
        
        return view('gallery', compact('galleryItems', 'category'));
    }

    public function contact()
    {
        return view('contact');
    }
}
```

#### ContactController
```php
// app/Http/Controllers/ContactController.php
namespace App\Http\Controllers;

use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $contact = ContactMessage::create($validated);

        // Optional: Send email notification to admin
        // Mail::to('admin@stfrancisawoshie.org')->send(new ContactMessageReceived($contact));

        return back()->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
```

### Admin Controllers

#### Admin EventController
```php
// app/Http/Controllers/Admin/EventController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderBy('date', 'desc')->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'category' => 'required|string',
            'recurring' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        Event::create($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'category' => 'required|string',
            'recurring' => 'nullable|string',
            'is_published' => 'boolean',
        ]);

        $event->update($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully.');
    }
}
```

#### Admin MinistryController
```php
// app/Http/Controllers/Admin/MinistryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ministry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MinistryController extends Controller
{
    public function index()
    {
        $ministries = Ministry::orderBy('order')->paginate(20);
        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'icon' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ministries', 'public');
            $validated['image'] = Storage::url($path);
        }

        Ministry::create($validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry created successfully.');
    }

    public function edit(Ministry $ministry)
    {
        return view('admin.ministries.edit', compact('ministry'));
    }

    public function update(Request $request, Ministry $ministry)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'contact_email' => 'required|email|max:255',
            'icon' => 'required|string|max:50',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('ministries', 'public');
            $validated['image'] = Storage::url($path);
        }

        $ministry->update($validated);

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry updated successfully.');
    }

    public function destroy(Ministry $ministry)
    {
        if ($ministry->image && str_starts_with($ministry->image, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $ministry->image));
        }

        $ministry->delete();

        return redirect()->route('admin.ministries.index')
            ->with('success', 'Ministry deleted successfully.');
    }
}
```

#### Admin GalleryController
```php
// app/Http/Controllers/Admin/GalleryController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
            'is_published' => 'boolean',
        ]);

        if ($request->type === 'image' && $request->hasFile('image')) {
            $path = $request->file('image')->store('gallery', 'public');
            $validated['url'] = Storage::url($path);
        }

        GalleryItem::create($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item created successfully.');
    }

    public function destroy(GalleryItem $item)
    {
        // Delete file if it's an uploaded image
        if ($item->type === 'image' && str_starts_with($item->url, '/storage/')) {
            Storage::disk('public')->delete(str_replace('/storage/', '', $item->url));
        }

        $item->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Gallery item deleted successfully.');
    }
}
```

#### Admin MessageController
```php
// app/Http/Controllers/Admin/MessageController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;

class MessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(20);
        return view('admin.messages.index', compact('messages'));
    }

    public function show(ContactMessage $message)
    {
        $message->update(['is_read' => true]);
        return view('admin.messages.show', compact('message'));
    }
}
```

---

## 5. Routes

```php
// routes/web.php
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\EventController as AdminEventController;
use App\Http\Controllers\Admin\MinistryController as AdminMinistryController;
use App\Http\Controllers\Admin\GalleryController as AdminGalleryController;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/ministries', [HomeController::class, 'ministries'])->name('ministries');
Route::get('/events', [HomeController::class, 'events'])->name('events');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    Route::resource('events', AdminEventController::class);
    Route::resource('ministries', AdminMinistryController::class);
    Route::resource('gallery', AdminGalleryController::class);
    
    Route::resource('messages', App\Http\Controllers\Admin\MessageController::class)
        ->only(['index', 'show']);
});

require __DIR__.'/auth.php';
```

---

## 6. Frontend Blade Templates

### Layout Template
```blade
{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'St. Francis Of Assisi - Awoshie Anyaa')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">
    @include('partials.header')
    
    <main class="flex-1">
        @yield('content')
    </main>
    
    @include('partials.footer')
</body>
</html>
```

Create these partials to match your React header/footer:
- `resources/views/partials/header.blade.php`
- `resources/views/partials/footer.blade.php`

### Home Page
```blade
{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('content')
    <!-- Hero Section -->
    <section class="relative h-[600px] flex items-center justify-center text-white">
        <div class="absolute inset-0">
            <img src="/images/church-interior.jpg" alt="Church Interior" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-black/50"></div>
        </div>
        <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-6xl mb-4">Welcome to St. Francis Of Assisi</h1>
            <p class="text-xl md:text-2xl mb-8">Catholic Church, Awoshie Anyaa</p>
            <p class="text-lg mb-8 max-w-2xl mx-auto">
                A community where faith, hope, and love come together. Join us as we worship and serve together.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('about') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition-colors">
                    Learn More
                </a>
                <a href="{{ route('contact') }}" class="bg-white/10 hover:bg-white/20 backdrop-blur-sm text-white px-8 py-3 rounded-lg border border-white/30 transition-colors">
                    Visit Us
                </a>
            </div>
        </div>
    </section>

    <!-- Upcoming Events -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl mb-12 text-center">Upcoming Events</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($upcomingEvents as $event)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="text-blue-600 mb-3">
                            <div class="text-2xl">{{ $event->date->format('M d') }}</div>
                        </div>
                        <h3 class="text-xl mb-2">{{ $event->title }}</h3>
                        <p class="text-gray-600 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $event->time->format('g:i A') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
```

### Contact Form
```blade
{{-- resources/views/contact.blade.php --}}
@extends('layouts.app')

@section('content')
    <section class="py-20">
        <div class="max-w-3xl mx-auto px-4">
            <h1 class="text-4xl mb-8 text-center">Contact Us</h1>
            
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label for="name" class="block text-gray-700 mb-2">Your Name *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label for="email" class="block text-gray-700 mb-2">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label for="phone" class="block text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>

                <div>
                    <label for="subject" class="block text-gray-700 mb-2">Subject *</label>
                    <select id="subject" name="subject" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">Select a subject</option>
                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry</option>
                        <option value="new-member" {{ old('subject') == 'new-member' ? 'selected' : '' }}>New Member Information</option>
                        <option value="sacraments" {{ old('subject') == 'sacraments' ? 'selected' : '' }}>Sacraments</option>
                        <option value="ministries" {{ old('subject') == 'ministries' ? 'selected' : '' }}>Ministry Information</option>
                        <option value="events" {{ old('subject') == 'events' ? 'selected' : '' }}>Events</option>
                        <option value="prayer" {{ old('subject') == 'prayer' ? 'selected' : '' }}>Prayer Request</option>
                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                <div>
                    <label for="message" class="block text-gray-700 mb-2">Message *</label>
                    <textarea id="message" name="message" required rows="6"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-600 resize-none">{{ old('message') }}</textarea>
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">
                    Send Message
                </button>
            </form>
        </div>
    </section>
@endsection
```

---

## 7. Admin Panel with Filament (Recommended)

For a modern, beautiful admin panel, use Filament:

```bash
# Install Filament
composer require filament/filament:"^3.0"
php artisan filament:install --panels

# Create admin user
php artisan make:filament-user

# Create resources
php artisan make:filament-resource Event --generate
php artisan make:filament-resource Ministry --generate
php artisan make:filament-resource GalleryItem --generate
php artisan make:filament-resource ContactMessage --generate
```

---

## 8. File Storage Configuration

```php
// config/filesystems.php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],

// Run this command to create symlink
php artisan storage:link
```

---

## 9. Deployment Checklist

1. Set up .env file with database credentials
2. Run migrations: `php artisan migrate`
3. Seed initial data if needed
4. Set up file storage permissions
5. Configure mail settings for contact form
6. Set up backup system for database and uploads
7. Enable HTTPS
8. Configure caching: `php artisan config:cache`

---

## 10. API Endpoints (Optional - for React frontend)

If you want to keep the React frontend and use Laravel as API:

```php
// routes/api.php
Route::get('/events', [EventController::class, 'index']);
Route::get('/ministries', [MinistryController::class, 'index']);
Route::get('/gallery', [GalleryController::class, 'index']);
Route::post('/contact', [ContactController::class, 'store']);

// In Controllers, return JSON
public function index()
{
    $events = Event::published()->upcoming()->get();
    return response()->json($events);
}
```

Then configure CORS in `config/cors.php` and update React to fetch from Laravel API.

---

This guide provides a solid foundation for your Laravel implementation. You can customize and extend it based on your specific needs!
