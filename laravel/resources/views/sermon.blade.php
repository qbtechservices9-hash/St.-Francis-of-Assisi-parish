@extends('layouts.site')

@section('content')
@php
  $videoUrl = $sermon->video_url;
  $audioUrl = $sermon->audio_url;
  $videoHost = $videoUrl ? (parse_url($videoUrl, PHP_URL_HOST) ?? '') : '';
  $videoPath = $videoUrl ? (parse_url($videoUrl, PHP_URL_PATH) ?? '') : '';
  $extension = strtolower(pathinfo($videoPath, PATHINFO_EXTENSION));
  $isYoutube = $videoUrl && \Illuminate\Support\Str::contains($videoHost, ['youtube.com', 'youtu.be']);
  $isVimeo = $videoUrl && \Illuminate\Support\Str::contains($videoHost, ['vimeo.com']);
  $isEmbed = $videoUrl && ($isYoutube || $isVimeo || \Illuminate\Support\Str::contains($videoUrl, 'embed'));
  $isPlayable = $videoUrl && in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'm4v'], true);
  $embedUrl = $videoUrl;
  if ($isYoutube && !\Illuminate\Support\Str::contains($videoUrl, 'embed')) {
      if (\Illuminate\Support\Str::contains($videoUrl, 'youtu.be/')) {
          $id = trim(\Illuminate\Support\Str::after($videoUrl, 'youtu.be/'));
          $embedUrl = 'https://www.youtube.com/embed/' . $id;
      } elseif (\Illuminate\Support\Str::contains($videoUrl, 'watch?v=')) {
          $id = trim(\Illuminate\Support\Str::after($videoUrl, 'watch?v='));
          $id = \Illuminate\Support\Str::before($id, '&');
          $embedUrl = 'https://www.youtube.com/embed/' . $id;
      }
  }
  if ($isVimeo && !\Illuminate\Support\Str::contains($videoUrl, 'player.vimeo.com')) {
      $id = trim(\Illuminate\Support\Str::afterLast($videoUrl, '/'));
      $embedUrl = 'https://player.vimeo.com/video/' . $id;
  }
@endphp

  <section class="relative overflow-hidden hero-3d">
    <div class="absolute inset-0 bg-gradient-to-r from-[#6B2C2C] via-[#8C6A5D] to-[#6B2C2C]"></div>
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(255,255,255,0.2),_transparent_58%)]"></div>
    <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center text-white hero-intro hero-delay-1">
      <p class="section-kicker text-amber-300">Sermon</p>
      <h1 class="text-4xl md:text-5xl font-semibold mt-4">{{ $sermon->title }}</h1>
      <p class="text-lg text-white/75 mt-4">
        {{ $sermon->preacher ? 'Priest/Friar: ' . $sermon->preacher : 'A message to strengthen our walk with God.' }}
      </p>
      <div class="mt-6 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-2 text-xs font-semibold tracking-[0.2em] uppercase">
        {{ $sermon->date?->format('M d, Y') }}
      </div>
    </div>
  </section>

  <section class="py-20 bg-[#F1EEE9]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid grid-cols-1 lg:grid-cols-[1.35fr,0.65fr] gap-8 items-start">
        <div class="space-y-8">
          @if ($videoUrl)
            <div class="card overflow-hidden">
              <div class="aspect-video bg-black">
                @if ($isEmbed)
                  <iframe
                    src="{{ $embedUrl }}"
                    title="{{ $sermon->title }}"
                    class="w-full h-full"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                  ></iframe>
                @elseif ($isPlayable)
                  <video class="w-full h-full" controls>
                    <source src="{{ $videoUrl }}">
                  </video>
                @else
                  <div class="flex items-center justify-center h-full text-white text-lg">
                    <a href="{{ $videoUrl }}" target="_blank" rel="noopener" class="underline">Open video</a>
                  </div>
                @endif
              </div>
            </div>
          @endif

          @if ($audioUrl)
            <div class="card p-6">
              <p class="section-kicker">Audio</p>
              <h2 class="text-2xl font-semibold mt-3">Listen to the Sermon</h2>
              <audio controls class="w-full mt-4">
                <source src="{{ $audioUrl }}">
              </audio>
            </div>
          @endif

          <div class="card p-6">
            <p class="section-kicker">Summary</p>
            <h2 class="text-2xl font-semibold mt-3">Sermon Highlights</h2>
            <p class="section-lead mt-4">
              {{ $sermon->summary ?: 'This sermon invites us to reflect deeply on God’s Word and apply it to our daily lives.' }}
            </p>
          </div>
        </div>

        <aside class="space-y-6">
          <div class="surface-soft p-6">
            <p class="section-kicker">Key Verses</p>
            @if ($sermon->verses && count($sermon->verses))
              <ul class="mt-4 space-y-4 text-sm text-slate-600">
                @foreach ($sermon->verses as $verse)
                  <li>
                    @php
                      $reference = is_array($verse) ? ($verse['reference'] ?? '') : $verse;
                      $text = is_array($verse) ? ($verse['text'] ?? '') : '';
                    @endphp
                    @if (!empty($reference))
                      <p class="font-semibold text-[#2b1a12]">{{ $reference }}</p>
                    @endif
                    @if (!empty($text))
                      <p class="mt-1">{{ $text }}</p>
                    @endif
                  </li>
                @endforeach
              </ul>
            @else
              <p class="text-sm text-slate-600 mt-4">Key verses will be added soon.</p>
            @endif
          </div>

          @if ($sermon->notes_url)
            <div class="surface-soft p-6">
              <p class="section-kicker">Notes</p>
              <h3 class="text-xl font-semibold mt-3">Download Sermon Notes</h3>
              <p class="text-sm text-slate-600 mt-3">Access the full sermon notes for deeper study.</p>
              <a href="{{ $sermon->notes_url }}" target="_blank" rel="noopener" class="btn btn-primary mt-5">
                Download Notes
              </a>
            </div>
          @endif

          <a href="{{ route('sermons') }}" class="btn btn-soft w-full justify-center">
            Back to Sermons
          </a>
        </aside>
      </div>
    </div>
  </section>
@endsection
