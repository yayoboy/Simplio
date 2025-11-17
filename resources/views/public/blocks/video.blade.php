{{-- Video Block --}}
@php
    $url = $block->content['url'] ?? '';
    $provider = $block->content['provider'] ?? 'youtube';
    $autoplay = $block->content['autoplay'] ?? false;
    $aspectRatio = $block->properties['aspectRatio'] ?? '16/9';

    $embedUrl = null;

    if ($url) {
        if ($provider === 'youtube' || str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            // Extract YouTube ID
            preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\?]+)/', $url, $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $embedUrl = "https://www.youtube.com/embed/{$videoId}" . ($autoplay ? '?autoplay=1' : '');
            }
        } elseif ($provider === 'vimeo' || str_contains($url, 'vimeo.com')) {
            // Extract Vimeo ID
            preg_match('/vimeo\.com\/(\d+)/', $url, $matches);
            if (isset($matches[1])) {
                $videoId = $matches[1];
                $embedUrl = "https://player.vimeo.com/video/{$videoId}" . ($autoplay ? '?autoplay=1' : '');
            }
        }
    }

    $paddingBottom = '56.25%'; // 16:9
    if ($aspectRatio === '4/3') $paddingBottom = '75%';
    if ($aspectRatio === '1/1') $paddingBottom = '100%';
@endphp

@if($embedUrl)
    <div class="py-4">
        <div class="relative w-full overflow-hidden rounded-lg bg-gray-900" style="padding-bottom: {{ $paddingBottom }};">
            <iframe
                src="{{ $embedUrl }}"
                class="absolute top-0 left-0 w-full h-full"
                frameborder="0"
                allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture{{ $autoplay ? '; autoplay' : '' }}"
                allowfullscreen
            ></iframe>
        </div>
    </div>
@endif
